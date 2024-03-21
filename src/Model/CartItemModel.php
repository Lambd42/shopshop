<?php

    declare(strict_types = 1);

    namespace MyApp\Model;

    use MyApp\Entity\CartItem;
    use MyApp\Entity\Product;
    use MyApp\Entity\Cart;
    use MyApp\Entity\User;
    use MyApp\Entity\Type;
    use PDO;

    class CartItemModel {
        private PDO $db;

        public function __construct(PDO $db) {
            $this->db = $db;
        }

        public function getAllCartItems(): array { 
            $sql = "SELECT * FROM Contenir INNER JOIN Product ON Contenir.productID = Product.productID INNER JOIN Cart ON Contenir.cartID = Cart.cartID INNER JOIN User ON Cart.userID = User.userID INNER JOIN Type ON Product.typeID = Type.typeID;";
            $stmt = $this->db->query($sql);
            $cartItems = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
                $cart = new Cart($row['Cart.cartID'], $row['creationDate'], $row['status'], $user);
                $type = new Type($row['Type.typeId'], $row['label']);
                $product = new Product($row['Product.productId'], $row['name'], $row['description'], floatVal($row['price']), $type, $row['image']); 
                $cartItems[] = new CartItem($product, $cart, $row['quantity']);
            }

            return $cartItems;
        }

        public function getOneCartItem(int $productId, int $cartId): ?Product {
            $sql = "SELECT * FROM Contenir INNER JOIN Product ON Contenir.productID = Product.productID INNER JOIN Cart ON Contenir.cartID = Cart.cartID INNER JOIN User ON Cart.userID = User.userID INNER JOIN Type ON Product.typeID = Type.typeID WHERE Contenir.productID = :productID AND Contenir.cartID = :cartID;";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':productID', $productId);
            $stmt->bindValue(':cartID', $cartId);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }
            $user = new User($row['User.userID'],$row['email'],$row['firstName'],$row['lastName'],$row['password'],json_decode($row['roles']));
            $cart = new Cart($row['Cart.cartID'], $row['creationDate'], $row['status'], $user);
            $type = new Type($row['Type.typeId'], $row['label']);
            $product = new Product($row['Product.productId'], $row['name'], $row['description'], floatVal($row['price']), $type, $row['image']);
            $cartItem = new CartItem($product, $cart, $row['quantity']);
            return $cartItem;
        }

        public function createCartItem(CartItem $cartItem): ?bool {
            $sql = "INSERT INTO Contenir (productID, cartID, quantity, unitPrice) VALUES (:productID, :cartID, :quantity, :unitPrice);";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":productID", $cartItem->getProduct()->getId());
            $stmt->bindValue(":cartID", $cartItem->getCart()->getId());
            $stmt->bindValue(":quantity", $cartItem->getQuantity());
            $stmt->bindValue(":unitPrice", $cartItem->getUnitPrice());
            return $stmt->execute();
        }
    }
