<?php 

declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Order;
use MyApp\Entity\Invoice; 
use PDO;

class OrderModel {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllOrders():array {
        $sql = "SELECT Order.orderID, orderDate, status, User.userID, firstName, lastName, email, address, postalCode, city, phone, password, roles, Invoice.invoiceID, invoiceDate, totalAmount, paymentStatus FROM Order INNER JOIN User ON Order.userID = User.userID INNER JOIN Invoice ON Order.invoiceID = Invoice.invoiceID;";

    }
}