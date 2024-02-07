<?php

    declare(strict_types = 1);

    namespace MyApp\Model;

    use MyApp\Entity\User; // on utilise l'objet qu'on a créé dans le dossier Entity
    use PDO;

    class UserModel {

        private PDO $db;

        public function __construct(PDO $db) {
            $this->db = $db;
        }

        public function getAllUsers(): array {
            $sql = "SELECT * FROM User";
            $stmt = $this->db->query($sql);
            $users = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
            // la commande FETCH_ASSOC permet d'avoir les clés de la table au lieu d'indices
                $users[] = new User($row['userID'], $row['email'], $row['lastName'], $row['firstName'], $row['password'], json_decode($row['roles'])); 
            }

            return $users;
        }

        public function getAllClients(): array {
            $sql = "SELECT * FROM User;";
            $stmt = $this->db->query($sql);
            $users = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!in_array('admin', json_decode($row['roles']))) {
                    $users[] = new User($row['userID'], $row['email'], $row['lastName'], $row['firstName'], $row['password'], json_decode($row['roles'])); 
                }
            }

            return $users;
        }


        public function getUserById(int $userID):?User{
            $sql = "SELECT * from User where userID = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $userID);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$row){
                return null;
            }
            return new User($row['userID'], $row['email'], $row['firstName'], $row['lastName'], $row['password'], json_decode($row['roles']));
            }


        public function getUserByEmail(string $email):?User {
            $sql = "SELECT * FROM User WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":email", $email);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            return new User($row['userID'], $row['email'], $row['lastName'], $row['firstName'], $row['password'], json_decode($row['roles'])); 
        }


        public function updateUser(User $user): bool {
            $sql = "UPDATE User SET email = :email, firstName = :firstName, lastName = :lastName, password = :password, roles = :roles WHERE userID = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
            $stmt->bindValue(':firstName', $user->getFirstName(), PDO::PARAM_STR);
            $stmt->bindValue(':lastName', $user->getLastName(), PDO::PARAM_STR);
            $stmt->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
            $stmt->bindValue(':roles', json_encode($user->getRoles()));
            $stmt->bindValue(':id', $user->getUserId(), PDO::PARAM_INT);
            return $stmt->execute();
        }


        public function createUser(User $user): bool {
            $sql = "INSERT INTO User (email, firstName, lastName, password, roles) VALUES (:email, :firstName, :lastName, :password, :roles)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
            $stmt->bindValue(':firstName', $user->getFirstName(), PDO::PARAM_STR);
            $stmt->bindValue(':lastName', $user->getLastName(), PDO::PARAM_STR);
            $stmt->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
            $stmt->bindValue(':roles', json_encode($user->getRoles()));
            return $stmt->execute();
            }

        public function deleteUser(int $id): bool {
            $sql = "DELETE FROM User WHERE userID = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }

    }





?>