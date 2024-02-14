<?php

    declare(strict_types = 1);

    namespace MyApp\Model;

    use MyApp\Entity\Product; // on utilise l'objet qu'on a créé dans le dossier Entity
    use PDO;
    use MyApp\Entity\Type;

    class ProductModel {

        private PDO $db;

        public function __construct(PDO $db) {
            $this->db = $db;
        }

        public function getAllProducts(): array {
            $sql = "SELECT Product.productID as ProductId, name, description, price, stock, Type.typeID as TypeId, image, homepage, label  FROM Product INNER JOIN Type ON Product.type = Type.typeID;";
            $stmt = $this->db->query($sql);
            $products = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
            // la commande FETCH_ASSOC permet d'avoir les clés de la table au lieu d'indices
                $currentType = new Type($row['TypeId'], $row['label']);
                $products[] = new Product($row['ProductId'], $row['name'], $row['description'], floatVal($row['price']), intval($row['stock']), $currentType, $row['image'], boolval($row['homepage'])); 
            }

            return $products;
        }

        public function getOneProduct(int $productID):?Product{
            $sql = "SELECT Product.productID as ProductId, name, description, price, stock, Type.typeID as typeID, image, homepage, label from Product INNER JOIN Type ON Product.type = Type.typeID WHERE productID = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $productID);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$row){
                return null;
            }
            $currentType = new Type($row['typeID'], $row['label']);
            return new Product($row['ProductId'], $row['name'], $row['description'], floatval($row['price']), intval($row['stock']), $currentType, $row['image'], boolval($row['homepage']));
        }

        public function updateProduct(Product $product): bool {
            $sql = "UPDATE Product SET name = :name, description = :description, price = :price, stock = :stock, type = :typeId, image = :image, homepage = :homepage WHERE productID = :productID;";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":name", $product->getName());
            $stmt->bindValue(":description", $product->getDescription());
            $stmt->bindValue(":price", $product->getPrice());
            $stmt->bindValue(":stock", $product->getStock());
            $stmt->bindValue(":typeId", $product->getType()->getTypeId());
            $stmt->bindValue(":productID", $product->getId());
            $stmt->bindValue(":image", $product->getImage());
            $stmt->bindValue(":homepage", intval($product->getHomepage()));
            return $stmt->execute();
        }

        public function getAllProductsByType(Type $type): ?array {
            $sql = "SELECT * FROM Product WHERE type = :typeID;";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":typeID", $type->getTypeId(), PDO::PARAM_INT);
            $stmt->execute();
            $products = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $products[] = new Product($row['productID'], $row['name'], $row['description'], floatval($row['price']), intval($row['stock']), $type, $row['image'], boolval($row['homepage']));
            }
            return $products;
            
        }

        public function getAllHomepageProducts(): ?array {
            $sql = "SELECT * FROM Product INNER JOIN Type on Product.type = Type.typeID WHERE homepage = 1;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $products = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $type = new Type($row['typeID'], $row['label']);
                $products[] = new Product($row['productID'], $row['name'], $row['description'], floatval($row['price']), intval($row['stock']), $type, $row['image'], boolval($row['homepage']));
            }
            return $products;

        }

    }


?>