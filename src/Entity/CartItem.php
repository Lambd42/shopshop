<?php 

    declare(strict_types = 1);

    namespace MyApp\Entity;
    use MyApp\Entity\Cart;
    use MyApp\Entity\Product;


    class CartItem {

        private Product $product;
        private Cart $cart;
        private int $quantity;
        private float $unitPrice;

        public function __construct(Product $product, Cart $cart, int $quantity) {
            $this->product = $product;
            $this->cart = $cart;
            $this->quantity = $quantity;
            $this->unitPrice = $this->product->getPrice();
        }

        public function getProduct(): Product {
            return $this->product;
        }

        public function setProduct(Product $product): void {
            $this->product = $product;
        }

        public function getCart(): Cart {
            return $this->cart;
        }

        public function setCart(Cart $cart): void {
            $this->cart = $cart;
        }

        public function getQuantity(): int {
            return $this->quantity;
        }

        public function setQuantity(int $quantity): void {
            $this->quantity = $quantity;
        }

        public function getUnitPrice(): float {
            return $this->unitPrice;
        }

        public function setUnitPrice(float $unitPrice): void {
            $this->unitPrice = $unitPrice;
        }
    }