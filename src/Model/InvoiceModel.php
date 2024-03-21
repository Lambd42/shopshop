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

    public function getAllInvoices():array {
        $sql = "SELECT Invoice.invoiceID, invoiceDate, totalAmount, PaymentStatus, Order.orderID, orderDate, status, User.userID, firstName, lastName, email, address, postalCode, city, phone, password, roles FROM Invoice INNER JOIN Order ON Invoice.orderID = Order.orderID INNER JOIN User ON Order.userID = User.userID;";
        $stmt = $this->db->query($sql);
        $invoices = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
            $order = new Order($row[Order.orderID], $row[orderDate], $row[status], $user);
            $invoices[] = new Invoice($row['Invoice.invoiceID'], $row[invoiceDate], $row[totalAmount], $row[paymentStatus], $order);
        }

        return $invoices;

    }

    public function getOneInvoice($id): ?Order {
        $sql = "SELECT Invoice.invoiceID, invoiceDate, totalAmount, PaymentStatus, Order.orderID, orderDate, status, User.userID, firstName, lastName, email, address, postalCode, city, phone, password, roles FROM Invoice INNER JOIN Order ON Invoice.orderID = Order.orderID INNER JOIN User ON Order.userID = User.userID WHERE Invoice.invoiceID = :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) {
            return null;
        }
        $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
        $order = new Order($row[Order.orderID], $row[orderDate], $row[status], $user);
        return new Invoice($row['Invoice.invoiceID'], $row[invoiceDate], $row[totalAmount], $row[paymentStatus], $order);

    }

    public function getInvoiceByUser(User $user): array {
        $sql = "SELECT Invoice.invoiceID, invoiceDate, totalAmount, PaymentStatus, Order.orderID, orderDate, status, User.userID, firstName, lastName, email, address, postalCode, city, phone, password, roles FROM Invoice INNER JOIN Order ON Invoice.orderID = Order.orderID INNER JOIN User ON Order.userID = User.userID WHERE User.userID = :userId;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":userID", $user.getUserId());
        $stmt->execute();
        $invoices = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
            $order = new Order($row[Order.orderID], $row[orderDate], $row[status], $user);
            $invoices[] = new Invoice($row['Invoice.invoiceID'], $row[invoiceDate], $row[totalAmount], $row[paymentStatus], $order);
        }

        return $orders;


    }

    public function getInvoiceByOrder(Order $order) {
        $sql = "SELECT Invoice.invoiceID, invoiceDate, totalAmount, PaymentStatus, Order.orderID, orderDate, status, User.userID, firstName, lastName, email, address, postalCode, city, phone, password, roles FROM Invoice INNER JOIN Order ON Invoice.orderID = Order.orderID INNER JOIN User ON Order.userID = User.userID WHERE Order.orderID = :orderId;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":orderId", $order.getId());
        $stmt->execute();
        $invoices = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
            $order = new Order($row[Order.orderID], $row[orderDate], $row[status], $user);
            $invoices[] = new Invoice($row['Invoice.invoiceID'], $row[invoiceDate], $row[totalAmount], $row[paymentStatus], $order);
        }

        return $orders;

    }
}