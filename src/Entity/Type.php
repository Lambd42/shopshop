<?php 

    declare(strict_types = 1);

    namespace MyApp\Entity;

    class Type { // on crée un objet Type

        // on crée les attributs (variables) de l'objet :
        private ?int $typeID = null; // on associe pas de valeur car la table incrémentera l'id par elle-même
        private string $label; 

        public function __construct(?int $typeID, string $label) { // constructeur de l'objet 
            $this->typeID = $typeID; // on atribue la valeur de $id à l'attibut 'id'
            $this->label = $label; // $this fait référence à l'instance en cours
        }

        public function getTypeId():?int {
            return $this->typeID;
        }

        public function getLabel():string {
            return $this->label;
        }

        public function setTypeId(?int $typeID):void {
            $this->typeID = $typeID;
        }

        public function setLabel(?int $label):void {
            $this->label = $label;
        }


    }


?>