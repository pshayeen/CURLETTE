<?php

namespace App\Controller;

use App\Entity\OrderItems;
use App\Entity\Orders;
use App\Entity\Products;
use App\Entity\User;
use App\Form\CheckoutType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart')]
final class CartController extends AbstractController
{
    #[Route(name: 'app_cart', methods: ['GET', 'POST'])]
    public function index(Request $request, CartService $cart, EntityManagerInterface $entityManager): Response
    {
        $items = $cart->items();
        $total = (float) array_sum(array_column($items, 'lineTotal'));

        /** @var User|null $currentUser */
        $currentUser = $this->getUser();
        $savedAddress = $currentUser?->getDeliveryAddress();

        $form = $this->createForm(CheckoutType::class, [
            'fulfillmentMethod' => 'delivery',
            'deliveryAddress' => $savedAddress,
            'saveAddress' => null === $savedAddress || '' === $savedAddress,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $method = (string) $form->get('paymentMethod')->getData();
            $reference = trim((string) $form->get('paymentReference')->getData());
            $isDelivery = 'pickup' !== $form->get('fulfillmentMethod')->getData();
            $address = trim((string) $form->get('deliveryAddress')->getData());

            if ($isDelivery && '' === $address) {
                $form->get('deliveryAddress')->addError(new FormError('Please enter your delivery address.'));
            }

            if (in_array($method, ['gcash', 'bank'], true) && '' === $reference) {
                $form->get('paymentReference')->addError(new FormError('Please enter the reference number.'));
            }

            if ([] === $items) {
                $this->addFlash('danger', 'Your cart is empty.');
            } elseif ($form->isValid()) {
                /** @var User $user */
                $user = $this->getUser();

                $order = new Orders();
                $order->setCustomerName($user->getFullName() ?: $user->getUserIdentifier());
                $order->setCustomerEmail($user->getEmail() ?? '');
                $order->setCustomerPhone($user->getPhoneNumber());
                $order->setFulfillmentMethod($isDelivery ? 'delivery' : 'pickup');
                $order->setDeliveryAddress($isDelivery ? $address : null);
                $order->setPaymentMethod($method);
                $order->setPaymentReference('' !== $reference ? $reference : null);
                $order->setCustomer($user);

                if ($isDelivery && $form->get('saveAddress')->getData()) {
                    $user->setDeliveryAddress($order->getDeliveryAddress());
                }

                foreach ($items as $line) {
                    /** @var Products $product */
                    $product = $line['product'];

                    $item = new OrderItems();
                    $item->setProductName($product->getProductName());
                    $item->setUnitPrice($product->getPrice());
                    $item->setQuantity($line['quantity']);
                    $order->addItem($item);
                    $entityManager->persist($item);

                    $product->setStockQuantity($product->getStockQuantity() - $line['quantity']);
                }

                $order->setTotal(number_format($total, 2, '.', ''));
                $order->setStatus('placed');
                $order->setCreatedAt(new \DateTimeImmutable());

                $entityManager->persist($order);
                $entityManager->flush();

                $cart->clear();

                $this->addFlash('success', sprintf('Thanks for your order! Your order number is #%d.', $order->getId()));

                return $this->redirectToRoute('app_account_orders', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('cart/index.html.twig', [
            'items' => $items,
            'total' => $total,
            'form' => $form->createView(),
        ], $form->isSubmitted() ? new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(Request $request, Products $product, CartService $cart): Response
    {
        if (!$this->isCsrfTokenValid('cart' . $product->getId(), $request->getPayload()->getString('_token'))) {
            return $this->redirectToRoute('app_products_page', [], Response::HTTP_SEE_OTHER);
        }

        if ($product->getStockQuantity() < 1) {
            $this->addFlash('danger', 'Sorry, that product is out of stock.');
        } else {
            $cart->add($product->getId(), max(1, $request->getPayload()->getInt('quantity', 1)));
            $this->addFlash('success', sprintf('%s was added to your cart.', $product->getProductName()));
        }

        return $this->redirectToRoute('app_products_page', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(Request $request, Products $product, CartService $cart): Response
    {
        if ($this->isCsrfTokenValid('cart' . $product->getId(), $request->getPayload()->getString('_token'))) {
            $cart->set($product->getId(), min($request->getPayload()->getInt('quantity'), $product->getStockQuantity()));
        }

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(Request $request, Products $product, CartService $cart): Response
    {
        if ($this->isCsrfTokenValid('cart' . $product->getId(), $request->getPayload()->getString('_token'))) {
            $cart->remove($product->getId());
        }

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }
}
