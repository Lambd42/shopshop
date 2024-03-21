<?php
declare (strict_types = 1);
namespace MyApp\Controller;

use MyApp\Entity\Type;
use MyApp\Entity\User;
use MyApp\Entity\Product;
use MyApp\Entity\Cart;
use MyApp\Entity\Review;
use MyApp\Entity\CartItem;
use MyApp\Model\ProductModel;
use MyApp\Model\TypeModel;
use MyApp\Model\UserModel;
use MyApp\Model\CartModel;
use MyApp\Model\ReviewModel;
use MyApp\Model\CartItemModel;
use MyApp\Entity\Order;
use MyApp\Model\OrderModel;
use MyApp\Entity\Invoice;
use MyApp\Model\InvoiceModel;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class DefaultController
{
    private $twig;
    private $typeModel;
    private $productModel;
    private $userModel;
    private $cartModel;
    private $reviewModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->typeModel = $dependencyContainer->get('TypeModel');
        $this->productModel = $dependencyContainer->get('ProductModel');
        $this->userModel = $dependencyContainer->get('UserModel');
        $this->cartModel = $dependencyContainer->get('CartModel');
        $this->reviewModel = $dependencyContainer->get('ReviewModel');
        $this->cartItemModel = $dependencyContainer->get('CartItemModel');
    }

    public function home()
    {
        $products = $this->productModel->getAllHomepageProducts();
        $types = $this->typeModel->getAllTypes();
        echo $this->twig->render('defaultController/home.html.twig', ["types" => $types, "products" => $products]);
    }

    public function error404()
    {
        echo $this->twig->render('defaultController/error404.html.twig', []);
    }

    public function error403()
    {
        echo $this->twig->render('defaultController/error403.html.twig', []);
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

    public function productsByType() {
        $typeId = filter_input(INPUT_GET, "typeID", FILTER_SANITIZE_NUMBER_INT);
        $type = $this->typeModel->getOneType(intval($typeId));
        $products = $this->productModel->getAllProductsByType($type);
        echo $this->twig->render('defaultController/productsByType.html.twig', ['products' => $products, 'type' => $type]);
    }

    public function users()
    {
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('defaultController/users.html.twig', ['users' => $users]);
    }

    public function carts() {
        $carts = $this->cartModel->getAllCarts();
        echo $this->twig->render('defaultController/carts.html.twig', ['carts' => $carts]);
    }

    public function myCart() {
        $email = $_SESSION['login'];
        $user = $this->userModel->getUserByEmail($email);
        $cart = $this->cartModel->getCartByUser($user);
        $cartItems = $this->CartItemModel->getCartitemsByCart($cart);
        echo $this->twig->render('defaultController/myCart.html.twig', ['items' => $cartItems]);
    }

    public function addToCartClient() {
        $email = $_SESSION['login'];
        $user = $this->UserModel->getUserByEmail($email);
        if ($this->cartModel->getCartByUser($user) === null) { // si l'utilisateur n'a pas encore de panier actif, on lui en crée un
            $creationDate = date("Y-m-d");
            $cart = new Cart(null, $creationDate, '', $user);
            $this->cartModel->createCart($cart);
        }
    }

    public function myOrders() {
        $email = $_SESSION['login'];
        $user = $this->UserModel->getUserByEmail($email);
        $orders = $this->orderModel->getOrdersByUser($user);
        echo $this->twig->render('defaultController/myOrders.html.twig', ["orders" => $orders]);
        
    }

    public function pay() {
        $email = $_SESSION['login'];
        $user = $this->UserModel->getUserByEmail($email);
        $order = new order(null, date("Y-m-d"), '', $user);
        $this->orderModel->createOrder($order);
        $cart = $this->cartModel->getCartByUser($user);
        $cartItems = $this->cartItemModel->getCartItemsByCart($cart);
        foreach($cartItems as $item) {
            $this->cartItemModel->deleteCartItem($item);
        }

    }

    public function addCart() {
        if ($_SERVER["REQUEST_METHOD"] == 'POST') {
            $creationDate = date("Y-m-d");
            $email = filter_input(INPUT_POST, "user", FILTER_VALIDATE_EMAIL);
            $user = $this->userModel->getUserByEmail($email);
            $cart = new Cart(null, $creationDate, '', $user);
            $result = $this->cartModel->createCart($cart);
            if ($result and in_array('Admin', $_SESSION['roles'])) {
                header('Location: index.php?page=carts');
            }
        }
        $clients = $this->userModel->getAllClients();
        echo $this->twig->render('defaultController/addCart.html.twig', ['users' => $clients]);
    }

    public function deleteCart() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $cartId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $cart = $this->cartModel->getOneCart($cartId);
            $result = $this->cartModel->deleteCart($cart);
            if ($result) {
                header('Location: index.php?page=carts');
            }

        }
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
            $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
            $type = $ancientProduct->getType();
            $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING);
            $homepage = filter_input(INPUT_POST, "homepage", FILTER_VALIDATE_BOOLEAN);
            $product = new Product($productID, $name, $description, $price, intval($stock), $type, $image, $homepage);
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

    public function product() {
        $productID = filter_input(INPUT_GET, "productID", FILTER_SANITIZE_NUMBER_INT);
        $product = $this->productModel->getOneProduct(intval($productID));
        $reviews = $this->reviewModel->getAllReviewsByProduct($product);
        echo $this->twig->render('defaultController/product.html.twig', ['product' => $product, 'reviews' => $reviews]);
    }

    public function createReview() {
        if (!isset($_SESSION['login'])) {
            header('Location: index.php?page=login');
            exit;
        }
        elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
            $productID = filter_input(INPUT_POST, "productID", FILTER_SANITIZE_NUMBER_INT);
            $text = filter_input(INPUT_POST, "text", FILTER_SANITIZE_STRING);
            $note = filter_input(INPUT_POST, "note", FILTER_SANITIZE_NUMBER_INT);
            $product = $this->productModel->getOneProduct(intval($productID));
            $user = $this->userModel->getUserByEmail($_SESSION['login']);
            $review = new Review(null, $product, $user, intval($note), $text);
            $success = $this->reviewModel->createReview($review);
            if ($success) {
                header("Location: index.php?page=product&productID=$productID");
                exit;
            }

        }
        else {
            $productID = filter_input(INPUT_GET, "productID", FILTER_SANITIZE_NUMBER_INT);
            $product = $this->productModel->getOneProduct(intval($productID));
        }
        echo $this->twig->render('defaultController/createReview.html.twig', ['product' => $product]);

    }

    public function addItemInCart() {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $productID = filter_input(INPUT_POST, "productID", FILTER_SANITIZE_NUMBER_INT);
            $quantity = filter_input(INPUT_POST, "quantity", FILTER_SANITIZE_NUMBER_INT);
            $user = $this->userModel->getUserByEmail($email);
            $cart = $this->cartModel->getCartByUser($user);
            $product = $this->productModel->getOneProduct(intval($productID));
            if ($quantity > $product->getStock()) {
                $_SESSION['message'] = 'Not enough stock';
                header('Location: index.php?page=addItemInCart');
            }
            else {
                $cartItem = new CartItem($product, $cart, intval($quantity));
                $success = $this->cartItemModel->createCartItem($cartItem);
                if (!$success) {
                    $_SESSION['message'] = 'error during process';
                    header('Location: index.php?page=addItemInCart');
                }
                else {
                    $_SESSION['message'] = 'success';
                    header('Location: index.php?page=addItemInCart');
                }

            }
            

        }
        $products = $this->productModel->getAllProducts();
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('defaultController/addItemInCart.html.twig', ['products' => $products, 'users' => $users]);
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

    public function logout() {
        $_SESSION = array(); session_destroy(); 
        header('Location: index.php'); 
        exit;
        }

}
