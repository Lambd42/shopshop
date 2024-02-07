<?php

declare (strict_types = 1);

namespace MyApp\Routing;

use MyApp\Controller\DefaultController;
use MyApp\Controller\UserController;
use MyApp\Service\DependencyContainer;

class Router
{
    private $dependencyContainer;
    private $pageMappings;
    private $defaultPage;
    private $errorPage;

    public function __construct(DependencyContainer $dependencyContainer)
    {
        $this->dependencyContainer = $dependencyContainer;
        // Tableau contenant l'ensemble des pages (controller) de votre site
        // La clé est le mot qui sera récupéré dans la variable page de l'url
        // La valeur est un tableau composé de 2 colonnes
        // Colonne 1 : classe du contrôleur
        // Colonne 2 : nom de la méthode à appeler

        $this->pageMappings = [
            'home' => [DefaultController::class, 'home'],
            '404' => [DefaultController::class, 'error404'],
            '500' => [DefaultController::class, 'error500'],
            '403' => [DefaultController::class, 'error403'],
            'contact' => [DefaultController::class, 'contact'],
            'types' => [DefaultController::class, 'types'],
            'updateType' => [DefaultController::class, 'updateType'],
            'products' => [DefaultController::class, 'products'],
            'updateProduct' => [DefaultController::class, 'updateProduct'],
            'users' => [DefaultController::class, 'users'],
            'updateUser' => [DefaultController::class, 'updateUser'],
            'addType' => [DefaultController::class, 'addType'],
            'deleteUser' => [DefaultController::class, 'deleteUser'],
            'register' => [DefaultController::class, 'register'],
            'login' => [DefaultController::class, 'login'],
            'carts' => [DefaultController::class, 'carts'],
            'deleteCart' => [DefaultController::class, 'deleteCart'],
            'updateCart' => [DefaultController::class, 'updateCart']
            
        ];
        $this->defaultPage = 'home';
        $this->errorPage = '404';
    }

    public function route($twig)
    {
        $requestedPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
       
        // Si l'url ne contient pas la variable page, redirection vers la page d'accueil
        if (!$requestedPage) {
            $requestedPage = $this->defaultPage;
        } else {
            // Si la valeur de la page ne correspond pas à une clé du tableau associatif, redirection vers une page d'erreur
            if (!array_key_exists($requestedPage, $this->pageMappings)) {
                $requestedPage = $this->errorPage;
            }
        }

        // Récupère la ligne qui correspond à la clé comprise dans page
        $controllerInfo = $this->pageMappings[$requestedPage];
        /* Destructuration du tableau en mettant la première valeur du tableau de la ligne dans $controllerClass et la deuxième
        valeur dans $method */
        [$controllerClass, $method, $requiredRoles] = $controllerInfo;

        // Vérification de l'existence de la classe et de la méthode du contrôleur a appeler
        if ($this->checkUserPermissions($requiredRoles)) {
            // Vérification de l'existence de la classe et de la méthode du contrôleur à appeler if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
            if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
                // Instancie la classe récupérée
                $controller = new $controllerClass($twig, $this->dependencyContainer); //la fonction call_user_func appelle une méthode sur un objet call_user_func([$controller, $method]);
                    
            // Instancie la classe récupérée
            $controller = new $controllerClass($twig, $this->dependencyContainer); //la fonction call_user_func appelle une méthode sur un objet call_user_func([$controller, $method]);
            } else {
                // Si la classe ou la méthode n'existe pas, utilisez le contrôleur d'erreur 500
                $this->handleError($twig, '500'); 
            }
        }
        else {
        $this->handleError($twig, '403');
        }
    }
}
