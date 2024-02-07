<?php
declare (strict_types = 1);
namespace MyApp\Controller;

use MyApp\Entity\Type;
use MyApp\Entity\User;
use MyApp\Model\ProductModel;
use MyApp\Model\TypeModel;
use MyApp\Model\UserModel;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class DefaultController
{
    private $twig;
    private $typeModel;
    private $productModel;
    private $userModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->typeModel = $dependencyContainer->get('TypeModel');
        $this->productModel = $dependencyContainer->get('ProductModel');
        $this->userModel = $dependencyContainer->get('UserModel');
    }

    public function home()
    {
        echo $this->twig->render('defaultController/home.html.twig', []);
    }

    public function error404()
    {
        echo $this->twig->render('defaultController/error404.html.twig', []);
    }

    public function error500()
    {
        echo $this->twig->render('defaultController/error500.html.twig', []);
    }

    public function contact()
    {
        echo $this->twig->render('defaultController/contact.html.twig', []);
    }

    public function types()
    {
        $types = $this->typeModel->getAllTypes();
        echo $this->twig->render('defaultController/types.html.twig', ['types' => $types]);
    }

    public function products()
    {
        $products = $this->productModel->getAllProducts();
        echo $this->twig->render('defaultController/products.html.twig', ['products' => $products]);
    }

    public function users()
    {
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('defaultController/users.html.twig', ['users' => $users]);
    }

    public function updateType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'typeID', FILTER_SANITIZE_NUMBER_INT); // on nettoie la variable pour ne récupérer qu'un entier
            $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
            if (!empty($_POST['label'])) {
                $type = new Type(intVal($id), $label); // intVal transforme une string en int
                $success = $this->typeModel->updateType($type);
                if ($success) {
                    header('Location: index.php?page=types');
                }
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        }
        $type = $this->typeModel->getOneType(intVal($id));
        echo $this->twig->render('defaultController/updateType.html.twig', ['type' => $type]);
    }

    public function updateUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_NUMBER_INT);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
            $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $user = new User(intVal($id), $email, $firstName, $lastName, $password, ['User']);
            $success = $this->userModel->updateUser($user);
            if ($success) {
                header('Location: index.php?page=users');
            }
        } else {
            $id = filter_input(INPUT_GET, 'userID', FILTER_SANITIZE_NUMBER_INT);
        }
        $user = $this->userModel->getUserById(intVal($id));
        echo $this->twig->render('defaultController/updateUser.html.twig', ['user' => $user]);
    }

    public function addType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
            if (!empty($_POST['label'])) {
                $type = new Type(null, $label);
                $success = $this->typeModel->createType($type);
                if ($success) {
                    header('Location: index.php?page=types');
                }
            }
        }
        echo $this->twig->render('defaultController/addType.html.twig', []);
    }

    public function deleteUser()
    {
        $id = filter_input(INPUT_GET, 'userID', FILTER_SANITIZE_NUMBER_INT);
        $this->userModel->deleteUser(intVal($id));
        header('Location: index.php?page=users');
    }

    public function updateProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productID = intval($_POST['productID']);
            $ancientProduct = $this->productModel->getOneProduct($productID);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $price = $ancientProduct->getPrice();
            $type = $ancientProduct->getType();
            $product = new Product($productID, $name, $description, $price, $type);
            $success = $this->productModel->updateProduct($product);
            if ($success) {
                header('Location: index.php?page=products');
            }
        }

        else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        }
        $product = $this->productModel->getOneProduct(intVal($id));
        echo $this->twig->render('defaultController/updateProduct.html.twig', ['product' => $product]);
        
    }

    public function register() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];

            $passwordLegnth = strlen($password);
            $containsDigit = preg_match('/\d/', $password);
            $containsUpper = preg_match('/[A-Z]/', $password);
            $containsLower = preg_match('/[a-z]/', $password);
            $containsSpecial = preg_match('/[^a-zA-Z\d]/', $password);

            if (!$firstName || !$lastName || !$email || !$password) {
                $_SESSION['message'] = 'Error : wrong information';
            }

            elseif ($passwordLegnth<8 || !$containsDigit || !$containsUpper || !$containsLower || !$containsSpecial) {
                $_SESSION['message'] = 'Error : bad password';
            }

            elseif ($password !== $confirmPassword) {
                $_SESSION['message'] = 'Error : passwords don\'t match';
                header('Location: index.php?page=register');
                exit;
            }

            else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $user = new User(null, $email, $firstName, $lastName, $hashedPassword, ['User']);
                $result = $this->userModel->createUser($user);
                
                if ($result) {
                    $_SESSION['message'] = 'Register successful';
                    header('Location: index.php?page=login');
                    exit;
                }

                else {
                    $_SESSION['message'] = 'Error during process';
                }

            }

            // header('Location: index.php?page=register');

        }
        echo $this->twig->render('defaultController/register.html.twig', []);
    }


    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'];
            $user = $this->userModel->getUserByEmail($email);

            if (!$user) {
                $_SESSION['message'] = 'Wrong email';
                header('Location: index.php?page=login');
            }

            else {
                if ($user->verifyPassword($password)) {
                    $_SESSION['login'] = $user->getEmail();
                    $_SESSION['roles'] = $user->getRoles();
                    header('location: index.php');
                    exit;
                }

                else {
                    $_SESSION['message'] = 'Wrong password';
                    header('Location: index.php?page=login');
                    exit;
                }
            }
        }
        echo $this->twig->render('defaultController/login.html.twig', []);
    }

}
