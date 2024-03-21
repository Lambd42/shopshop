<?php
namespace MyApp\Service;

use PDO;
use MyApp\Model\TypeModel;
use MyApp\Model\ProductModel;
use MyApp\Model\UserModel;
use MyApp\Model\CartModel;
use MyApp\Model\CartItemModel;
use MyApp\Model\ReviewModel;

class DependencyContainer
{
    private $instances = [];

    public function __construct()
    {
    }

    public function get($key)
    {
        if (!isset($this->instances[$key])) {
            $this->instances[$key] = $this->createInstance($key);
        }

        return $this->instances[$key];
    }

    private function createInstance($key)
    {
        switch ($key) {

            case 'PDO' : return $this->createPDOinstance();

            case 'TypeModel' :
                $pdo = $this->get('PDO');
                return new TypeModel($pdo);

            case 'ProductModel' :
                $pdo = $this->get('PDO');
                return new ProductModel($pdo);

            case 'UserModel' :
                $pdo = $this->get('PDO');
                return new UserModel($pdo);

            case 'CartModel' :
                $pdo = $this->get('PDO');
                return new CartModel($pdo);

            case 'CartItemModel' :
                $pdo = $this->get('PDO');
                return new CartItemModel($pdo);

            case 'ReviewModel' :
                $pdo = $this->get('PDO');
                return new ReviewModel($pdo);

            default:
                throw new \Exception("No service found for key: " . $key);
        }
    }


    private function createPDOinstance(){
    
        try { //on teste un script
            $pdo = new PDO('mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_NAME'].';charset',$_ENV['DB_USER'],$_ENV['DB_PASS']);
            // on créée un nouveau PDO qu'on met dans la variable $pdo
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
            // on utilise une fonction de PDO (la fleche permet de préciser qu'on exécute une fonction)
        }

        catch(PDOException $e) { //si le scipt ne marche pas (ici, erreur de PDO qui sera stockée dans $e), on essaye un autre script
            throw new \Exception('PDO erreur de connection '.$e->getMessages());
            //on renvoie le message d'erreur pour pouvoir l'analyser
        }

    }

}
?>
