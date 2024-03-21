<?php 

    declare(strict_types = 1);

    namespace MyApp\Entity;
    use MyApp\Entity\User;


    class Order {

        private ?int $orderId = null;
        private $orderDate;
        private $status;
        private User $user;

        public function __construct(?int $orderID, $orderDate, $status, User $user) {
            $this->orderID = $orderID;
            $this->orderDate = $orderDate;
            $this->status = $status;
            $this->user = $user;
        }

        public function getId(): ?int {
            return $this->orderID;
        }

        public function setId($id): void {
            $this->orderID = $id;
        }

        public function getOrderDate(): string {
            return $this->orderDate;
        }

        public function setOrderDate($date): void {
            $this->orderDate = $date;
        }

        public function getStatus(): string {
            return $this->status;
        }

        public function setStatus($status): void {
            $this->status = $status;
        }

        public function getUser(): User {
            return $this->user;
        }

        public function setUser($user): void {
            $this->user = $user;
        }


    }