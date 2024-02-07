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
            $sql = "SELECT Product.productID as ProductId, name, description, price, Type.typeID as TypeId, label  FROM Product INNER JOIN Type ON Product.type = Type.typeID;";
            $stmt = $this->db->query($sql);
            $products = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
            // la commande FETCH_ASSOC permet d'avoir les clés de la table au lieu d'indices
                $currentType = new Type($row['TypeId'], $row['label']);
                $products[] = new Product($row['ProductId'], $row['name'], $row['description'], floatVal($row['price']), $currentType); 
            }



            return $products;
        }

        public function getOneProduct(int $productID):?Product{
            $sql = "SELECT Product.productID as ProductId, name, description, price, Type.typeID as TypeId, label from Product INNER JOIN Type ON Product.type = Type.typeID WHERE productID = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $productID);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$row){
                return null;
            }
            $currentType = new Type($row['TypeId'], $row['label']);
            return new Product($row['ProductId'], $row['name'], $row['description'], floatval($row['price']), $currentType);
            }

        public function updateProduct(Product $product): bool {
            $sql = "UPDATE Product SET name = :name, description = :description, price = :price, type = :typeId WHERE productID = :productID";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":name", $product->getName());
            $stmt->bindValue(":description", $product->getDescription());
            $stmt->bindValue(":price", $product->getPrice());
            $stmt->bindValue(":typeID", $product->getType()->getTypeId());
            $stmt->bindValue(":productID", $product->getId());
            return $stmt->execute();
        }

        public function getAllProductsByType(Type $type): ?array {
            $sql = "SELECT * FROM Product WHERE type = :typeID;";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":typeID", $type->getTypeId());
            $products = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $products[] = new Product($row['ProductId'], $row['name'], $row['description'], floatval($row['price']), $type);
            }

            return $products;
            
            
        }

    }


?>