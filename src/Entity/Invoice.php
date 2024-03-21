<?php 

    declare(strict_types = 1);

    namespace MyApp\Entity; 
    use MyApp\Entity\Order;

    class Invoice {

        private ?int $invoiceID = null; 
        private ?string $invoiceDate;
        private ?float $totalAmount;
        private ?string $paymentStatus;
        private Order $order;

        public function __construct(?int $invoiceID, ?string $invoiceDate, ?float $totalAmount, ?string $paymentStatus, Order $order) {
            $this->invoiceID = $invoiceID;
            $this->invoiceDate = $invoiceDate;
            $this->totalAmount = $totalAmount;
            $this->paymentStatus = $paymentStatus;
            $this->order = $order;
        }

        public function getId(): int {
            return $this->invoiceID;
        }

        public function setId($id): void {
            $this->invoiceID = $id;
        }

        public function getInvoiceDate(): ?string {
            return $this->invoiceDate;
        }

        public function setInvoiceDate($date): void {
            $this->invoiceDate = $date;
        }

        public function getTotalAmount(): ?float {
            return $this->totalAmount;
        }

        public function setTotalAmount($totalAmount): void {
            $this->totalAmount = $totalAmount;
        }

        public function getPaymentStatus(): ?string {
            return $this->getPaymentStatus;
        }

        public function setPaymentStatus($paymentStatus): void {
            $this->PaymentStatus = $paymentStatus;
        }

        public function getOrder(): Order {
            return $this->order;
        }

        public function setOrder($order): void {
            $this->order = $order;
        }
    }
