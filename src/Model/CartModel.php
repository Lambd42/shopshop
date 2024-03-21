<?php

    declare(strict_types = 1);

    namespace MyApp\Model;

    use MyApp\Entity\Cart; 
    use MyApp\Entity\User;
    use MyApp\Model\UserModel;
    use PDO;

    class CartModel {
        private PDO $db;

        public function __construct(PDO $db) {
            $this->db = $db;
        }

        public function getAllCarts(): array {
            $sql = "SELECT * FROM Cart INNER JOIN User ON Cart.userID = User.userID;";
            $stmt = $this->db->query($sql);
            $carts = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user = new User($row['userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
                $carts[] = new Cart($row['cartID'], $row['creationDate'], $row['status'], $user);
            }
            return $carts;
        }

        public function getOneCart($cartId): ?Cart {
            $sql = "SELECT * FROM Cart INNER JOIN User ON Cart.userID = User.userID WHERE Cart.cartID = :cartID";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":cartID", $cartId);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }
            $user = new User($row['userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
            return new Cart($row['cartID'], $row['creationDate'], $row['status'], $user);
        }

        public function createCart(Cart $cart): ?bool {
            $sql = "INSERT INTO Cart (creationDate, status, userID) VALUES (:creationDate, :status, :userID);";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":creationDate", $cart->getCreationDate());
            $stmt->bindValue(":status", $cart->getStatus());
            $stmt->bindValue(":userID", $cart->getUser()->getUserId());
            return $stmt->execute();

        }

        public function deleteCart(Cart $cart): ?bool {
            $sql = "DELETE FROM Cart WHERE cartID = :cartID;";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":cartID", $cart->getId());
            return $stmt->execute();
        }

        public function updateCatrt(Cart $cart): ?bool {
            $sql = "UPDATE Cart SET status = :status, userID = :userId;";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":status", $cart->getStatus());
            $stmt->bindValue(":userId", $cart->getUser()->getUserId());
            return $stmt->execute();
        }

        public function getCartByUser($user): ?Cart {
            $userID = $user->getUserId();
            $sql = "SELECT * FROM Cart INNER JOIN User ON Cart.userID = User.userID WHERE User.userID = :userID";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":userID", $userID);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }
            $user = new User($row['userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
            return new Cart($row['cartID'], $row['creationDate'], $row['status'], $user);
        }
    }


?>