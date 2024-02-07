<?php 

    declare(strict_types = 1);

    namespace MyApp\Entity;
    use MyApp\Entity\User;



    class Cart {

        private ?int $cartID = null;
        private ?string $creationDate = null;
        private ?string $status = null;
        private User $user;

        public function __construct(?int $cartID, ?string $creationDate, ?string $status, User $user) {
            $this->cartID = $cartID;
            $this->creationDate = $creationDate;
            $this->status = $status;
            $this->user = $user;
        }

        public function getId() :?int {
            return $this->cartID;
        }

        public function setId($cartID) :void {
            $this->cartID = $cartID;
        }

        public function getCreationDate() :?string {
            return $this->creationDate;
        }

        public function setCreationDate($creationDate) :void {
            $this->creationDate = $creationDate;
        }

        public function getStatus() :?string {
            return $this->status;
        }

        public function setStatus($status) :void {
            $this->status = $status;
        }

        public function getUser() :?User {
            return $this->user;
        }

        public function setUser($user) :void {
            $this->user = $user;
        }
    
    }



?>