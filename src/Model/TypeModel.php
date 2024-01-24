<?php

    declare(strict_types = 1);

    namespace MyApp\Model;

    use MyApp\Entity\Type; // on utilise l'objet qu'on a créé dans le dossier Entity
    use PDO;

    class TypeModel {

        private PDO $db;

        public function __construct(PDO $db) {
            $this->db = $db;
        }

        public function getAllTypes(): array { // on récupère tous les types de la table 
        // ("types" n'a aucun lien avec le nom de la table "Type")
            $sql = "SELECT * FROM Type";
            $stmt = $this->db->query($sql);
            $types = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
            // la commande FETCH_ASSOC permet d'avoir les clés de la table au lieu d'indices
                $types[] = new Type($row['typeID'], $row['label']); 
            }



            return $types;
        }

        public function getOneType(int $typeID) {
            $sql = "SELECT typeID, label FROM Type WHERE typeID = :id";
            $stmt = $this->db->prepare($sql); 
            $stmt->bindValue(":id", $typeID); // on remplace le :id de la requete par la valeur de $id (souci de sécurité)
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }
            return new Type($row['typeID'], $row['label']);

        }


        public function updateType(Type $type): bool {
            $sql = "UPDATE Type SET label = :label WHERE typeID = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':label', $type->getLabel(), PDO::PARAM_STR);
            $stmt->bindValue(':id', $type->getTypeId(), PDO::PARAM_INT);
            return $stmt->execute();
            }


        public function createType(Type $type): bool {
            $sql = "INSERT INTO Type (label) VALUES (:label)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':label', $type->getLabel(), PDO::PARAM_STR);
            return $stmt->execute();
            }

    }


?>