<?php

namespace App\Controller;

use App\Entity\OrderItems;
use App\Entity\Orders;
use App\Form\OrdersType;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use App\Security\Voter\RecordOwnerVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/orders')]
#[IsGranted('ROLE_STAFF')]
final class OrdersController extends AbstractController
{
    #[Route(name: 'app_orders_index', methods: ['GET', 'POST'])]
    public function index(OrdersRepository $ordersRepository, ProductsRepository $productsRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $products = $productsRepository->findBy([], ['productName' => 'ASC']);

        $order = new Orders();
        $order->setFulfillmentMethod('pickup');
        $form = $this->createForm(OrdersType::class, $order, ['include_status' => false, 'require_contact' => true]);
        $form->handleRequest($request);

        $quantities = [];
        $itemsError = null;

        if ($form->isSubmitted()) {
            $quantities = $this->readQuantities($request, $products);
            $itemsError = $this->checkQuantities($quantities, $products);

            if (in_array($order->getPaymentMethod(), ['gcash', 'bank'], true) && !$order->getPaymentReference()) {
                $form->get('paymentReference')->addError(new FormError('Please enter the reference number.'));
            }

            $isDelivery = 'delivery' === $order->getFulfillmentMethod();

            if ($isDelivery && '' === trim((string) $order->getDeliveryAddress())) {
                $form->get('deliveryAddress')->addError(new FormError('Please enter the delivery address.'));
            }

            if ($form->isValid() && null === $itemsError) {
                if (!$isDelivery) {
                    $order->setDeliveryAddress(null);
                }

                $total = 0.0;

                foreach ($products as $product) {
                    $quantity = $quantities[$product->getId()] ?? 0;
                    if ($quantity < 1) {
                        continue;
                    }

                    $item = new OrderItems();
                    $item->setProductName($product->getProductName());
                    $item->setUnitPrice($product->getPrice());
                    $item->setQuantity($quantity);
                    $order->addItem($item);
                    $entityManager->persist($item);

                    $product->setStockQuantity($product->getStockQuantity() - $quantity);
                    $total += (float) $product->getPrice() * $quantity;
                }

                $order->setTotal(number_format($total, 2, '.', ''));
                $order->setStatus($isDelivery ? 'placed' : 'fulfilled');
                $order->setCreatedAt(new \DateTimeImmutable());
                $order->setCreatedBy($this->getUser());

                $entityManager->persist($order);
                $entityManager->flush();

                $this->addFlash('success', 'Order added.');

                return $this->redirectToRoute('app_orders_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $term = trim($request->query->getString('q'));
        $status = $request->query->getString('status');

        return $this->render('orders/index.html.twig', [
            'orders' => $ordersRepository->search($term, $status),
            'newOrders' => $ordersRepository->findByStatus('placed'),
            'products' => $products,
            'term' => $term,
            'status' => $status,
            'form' => $form->createView(),
            'selectedQuantities' => $quantities,
            'itemsError' => $itemsError,
            'openAddModal' => $form->isSubmitted(),
        ], $form->isSubmitted() ? new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/{id}', name: 'app_orders_show', methods: ['GET'])]
    public function show(Orders $order): Response
    {
        return $this->render('orders/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_orders_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Orders $order, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(RecordOwnerVoter::EDIT, $order);

        $form = $this->createForm(OrdersType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Order updated.');

            return $this->redirectToRoute('app_orders_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('orders/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_orders_delete', methods: ['POST'])]
    public function delete(Request $request, Orders $order, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(RecordOwnerVoter::DELETE, $order);

        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();

            $this->addFlash('success', 'Order deleted.');
        }

        return $this->redirectToRoute('app_orders_index', [], Response::HTTP_SEE_OTHER);
    }

    private function readQuantities(Request $request, array $products): array
    {
        $submitted = $request->request->all('quantities');
        $quantities = [];

        foreach ($products as $product) {
            $quantity = (int) ($submitted[$product->getId()] ?? 0);
            if ($quantity > 0) {
                $quantities[$product->getId()] = $quantity;
            }
        }

        return $quantities;
    }

    private function checkQuantities(array $quantities, array $products): ?string
    {
        if ([] === $quantities) {
            return 'Please choose at least one product.';
        }

        foreach ($products as $product) {
            $quantity = $quantities[$product->getId()] ?? 0;
            if ($quantity > $product->getStockQuantity()) {
                return sprintf('Only %d of %s left in stock.', $product->getStockQuantity(), $product->getProductName());
            }
        }

        return null;
    }
}