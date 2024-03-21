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
        $sql = "SELECT Order.orderID, orderDate, status, User.userID, firstName, lastName, email, address, postalCode, city, phone, password, roles FROM Order INNER JOIN User ON Order.userID = User.userID;";
        $stmt = $this->db->query($sql);
        $orders = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
            $orders[] = new Order($row[Order.orderID], $row[orderDate], $row[status], $user);
        }

        return $orders;

    }

    public function getOneOrder($id): ?Order {
        $sql = "SELECT Order.orderID, orderDate, status, User.userID, firstName, lastName, email, address, postalCode, city, phone, password, roles FROM Order INNER JOIN User ON Order.userID = User.userID WHERE Order.orderID = :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) {
            return null;
        }
        $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
        return new Order($row[Order.orderID], $row[orderDate], $row[status], $user);

    }

    public function getOrdersByUser(User $user): array {
        $sql = "SELECT Order.orderID, orderDate, status, User.userID, firstName, lastName, email, address, postalCode, city, phone, password, roles FROM Order INNER JOIN User ON Order.userID = User.userID WHERE Order.userID = :userID;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":userID", $user.getUserId());
        $stmt->execute();
        $orders = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
            $orders[] = new Order($row[Order.orderID], $row[orderDate], $row[status], $user);
        }

        return $orders;


    }
}