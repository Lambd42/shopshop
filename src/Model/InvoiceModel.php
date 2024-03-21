<?php 

declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Order;
use MyApp\Entity\Invoice; 
use PDO;

class InvoiceModel {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllOrders():array {
        $sql = "SELECT Invoice.invoiceID, invoiceDate, totalAmount, PaymentStatus, Order.orderID, orderDate, status, User.userID, firstName, lastName, email, address, postalCode, city, phone, password, roles FROM Invoice INNER JOIN Order ON Invoice.orderID = Order.orderID INNER JOIN User ON Order.userID = User.userID;";
        $stmt = $this->db->query($sql);
        $invoices = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
            $invoice = new Invoice($row['Invoice.invoiceID'], $row[invoiceDate], $row[totalAmount], $row[paymentStatus], $row[Order.orderID]);
            $orders[] = new Order($row[Order.orderID], $row[orderDate], $row[status], $user, $invoice);
        }

        return $orders;

    }

    public function getOneOrder($id): ?Order {
        $sql = "SELECT Order.orderID, orderDate, status, User.userID, firstName, lastName, email, address, postalCode, city, phone, password, roles, Invoice.invoiceID, invoiceDate, totalAmount, paymentStatus FROM Order INNER JOIN User ON Order.userID = User.userID INNER JOIN Invoice ON Order.invoiceID = Invoice.invoiceID WHERE Order.orderID = :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) {
            return null;
        }
        $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
        $invoice = new Invoice($row['Invoice.invoiceID'], $row[invoiceDate], $row[totalAmount], $row[paymentStatus], $row[Order.orderID]);
        return new Order($row[Order.orderID], $row[orderDate], $row[status], $user, $invoice);

    }

    public function getOrdersByUser(User $user): array {
        $sql = "SELECT Order.orderID, orderDate, status, User.userID, firstName, lastName, email, address, postalCode, city, phone, password, roles, Invoice.invoiceID, invoiceDate, totalAmount, paymentStatus FROM Order INNER JOIN User ON Order.userID = User.userID INNER JOIN Invoice ON Order.invoiceID = Invoice.invoiceID WHERE Order.userID = :userID;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":userID", $user.getUserId());
        $stmt->execute();
        $orders = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
            $invoice = new Invoice($row['Invoice.invoiceID'], $row[invoiceDate], $row[totalAmount], $row[paymentStatus], $row[Order.orderID]);
            $orders[] = new Order($row[Order.orderID], $row[orderDate], $row[status], $user, $invoice);
        }

        return $orders;


    }
}