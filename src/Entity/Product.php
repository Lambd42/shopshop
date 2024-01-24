<?php 

    declare(strict_types = 1);

    namespace MyApp\Entity;
    use MyApp\Entity\Type;

    class Product { // on crée un objet Product

        // on crée les attributs (variables) de l'objet :
        private ?int $productID = null; // on associe pas de valeur car la table incrémentera l'id par elle-même
        private string $name;
        private string $description;
        private float $price; 
        private Type $type;
        

        public function __construct(?int $productID, string $name, string $description, float $price, Type $type) { // constructeur de l'objet 
            $this->productID = $productID; // on atribue la valeur de $id à l'attibut 'id'
            $this->name = $name;
            $this->description = $description;
            $this->price = $price;
            $this->type = $type;
        }

        public function getId():?int {
            return $this->productID;
        }

        public function setId(?int $productID):void {
            $this->productID = $productID;
        }

        public function getName(): string {
            return $this->name;
        }

        public function setName(string $name):void {
            $this->name = $name;
        }

        public function getDescription(): string {
            return $this->description;
        }

        public function setdescription(string $description):void {
            $this->description = $description;
        }

        public function getPrice(): float {
            return $this->price;
        }

        public function setPrice(float $price):void {
            $this->price = $price;
        }

        public function getType(): Type {
            return $this->type;
        }

        public function setType(Type $type): void {
            $this->type = $type;
        }

    }


?>