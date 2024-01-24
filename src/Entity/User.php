<?php 

    declare(strict_types = 1);

    namespace MyApp\Entity;

    class User { // on crée un objet Type

        // on crée les attributs (variables) de l'objet :
        private ?int $userID = null; // on associe pas de valeur car la table incrémentera l'id par elle-même
        private string $email;
        private string $lastName;
        private string $firstName;
        private string $password; 
        private array $roles;

        public function __construct(?int $userID, string $email, string $firstName, string $lastName, string $password, array $roles) { // constructeur de l'objet 
            $this->userID = $userID; // on atribue la valeur de $id à l'attibut 'id'
            $this->email = $email; // $this fait référence à l'instance en cours
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->password = $password;
            $this->roles = $roles;
        }

        public function getUserId():?int {
            return $this->userID;
        }

        public function setUserId(?int $userID):void {
            $this->userID = $userID;
        }

        public function getEmail():string {
            return $this->email;
        }

        public function setEmail(string $email):void {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // si le mail n'est pas valide, on crée un message d'erreur
                throw new InvalidArgumentException("email invalide");
            }
            $this->email = $email;
        }

        public function getFirstName():string {
            return $this->firstName;
        }

        public function setFirstName(string $firstName):void {
            $this->firstName = $firstName;
        }

        public function getLastName():string {
            return $this->lastName;
        }

        public function setLastName(string $lastName):void {
            $this->lastName = $lastName;
        }

        public function getPassword():string {
            return $this->password;
        }

        public function setPassword(string $password):void {
            $this->password = $password;
        }

        public function verifyPassword(string $password): bool {
            return password_verify($this->password, $password);
        }

        public function getRoles():array {
            return $this->roles;
        }

        public function setRoles(array $roles):void {
            $this->roles = $roles;
        }

    }


?>