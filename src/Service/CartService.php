<?php

namespace App\Service;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function __construct(
        private RequestStack $requestStack,
        private ProductsRepository $productsRepository,
    ) {
    }

    public function count(): int
    {
        return (int) array_sum($this->quantities());
    }

    public function add(int $productId, int $quantity): void
    {
        $quantities = $this->quantities();
        $quantities[$productId] = ($quantities[$productId] ?? 0) + $quantity;

        $this->save($quantities);
    }

    public function set(int $productId, int $quantity): void
    {
        $quantities = $this->quantities();

        if ($quantity < 1) {
            unset($quantities[$productId]);
        } else {
            $quantities[$productId] = $quantity;
        }

        $this->save($quantities);
    }

    public function remove(int $productId): void
    {
        $this->set($productId, 0);
    }

    public function clear(): void
    {
        $this->save([]);
    }

    /**
     * @return array<int, array{product: Products, quantity: int, lineTotal: float}>
     */
    public function items(): array
    {
        $items = [];

        foreach ($this->quantities() as $productId => $quantity) {
            $product = $this->productsRepository->find($productId);

            if (!$product || $product->getStockQuantity() < 1) {
                continue;
            }

            $quantity = min($quantity, $product->getStockQuantity());

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'lineTotal' => (float) $product->getPrice() * $quantity,
            ];
        }

        return $items;
    }

    public function total(): float
    {
        return (float) array_sum(array_column($this->items(), 'lineTotal'));
    }

    private function quantities(): array
    {
        return $this->requestStack->getSession()->get(self::SESSION_KEY, []);
    }

    private function save(array $quantities): void
    {
        $this->requestStack->getSession()->set(self::SESSION_KEY, $quantities);
    }
}
