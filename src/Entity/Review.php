<?php 

    declare(strict_types = 1);

    namespace MyApp\Entity;
    use MyApp\Entity\User;
    use MyApp\Entity\Product;


    class Review {

        private ?int $reviewID = null;
        private Product $product;
        private User $user; 
        private int $note;
        private string $text;

        public function __construct(?int $reviewID, Product $product, User $user, int $note, string $text) {
            $this->reviewID = $reviewID;
            $this->product = $product;
            $this->user = $user;
            $this->note = $note;
            $this->text = $text;
        }

        public function getId(): ?int {
            return $this->reviewID;
        }

        public function setId(int $reviewID): void {
            $this->reviewID = $reviewID;
        }

        public function getProduct(): ?Product {
            return $this->product;
        }

        public function setProduct(Product $product): void {
            $this->product = $product;
        }

        public function getUser(): ?User {
            return $this->user;
        }

        public function setUser(User $user): void {
            $this->user = $user;
        }

        public function getNote(): ?int {
            return $this->note;
        }

        public function setNote(int $note): void {
            $this->note = $note;
        }

        public function getText(): ?string {
            return $this->text;
        }

        public function setText(string $text): void {
            $this->text = $text;
        }
    }


?>