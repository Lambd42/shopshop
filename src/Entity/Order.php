<?php 

    declare(strict_types = 1);

    namespace MyApp\Entity;
    use MyApp\Entity\User;
    use MyApp\Invoice;


    class Order {

        private $orderId;
        private $orderDate;
        private $status;
        private User $user;
        private Invoice $invoice;

        public function __construct(?int $orderID, $orderDate, $status, User $user, Invoice $invoice) {
            $this->orderID = $orderID;
            $this->orderDate = $orderDate;
            $this->status = $status;
            $this->user = $user;
            $this->invoice = $invoice;
        }

        public function getId(): int {
            return $this->orderID;
        }

        public function setId($id): void {
            $this->orderID = $id;
        }

        public function getDate(): string {
            return $this->orderDate;
        }

        public function setDate($date): void {
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

        public function getInvoice(): Invoice {
            return $this->invoice;
        }

        public function setInvoice($invoice): void {
            $this->invoice = $invoice;
        }


    }