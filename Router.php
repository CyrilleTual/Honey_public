<?php

namespace Services;

use Controllers\RendersController;

class Router
{
    private ?string $route;
    private ?string $action;

    public function __construct()
    {
        $this->route = isset($_GET['route']) ? $_GET['route'] : null;
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
        $this->router();
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    private function router()
    {
        // si une vue est définie on appelle le render

        // if (!empty($this->view) && $this->view !== null) {
        //     var_dump(('idi'));
        //     new RendersController();   
        // }

        // si pas de vue, mise en route du controller

        if (!empty($this->route) && $this->route !== null) {
            $strReplaceUp = ucfirst($this->route); // passe en majuscule la premiere lettre pour reconstituer le controller appelé

            $nomfichier = str_replace("\\", "/", "controllers\\$strReplaceUp" . 'Controller.php'); // pour en vérifier l'existence

            if (file_exists($nomfichier)) {  // le controlleur existe 

                $controller = "controllers\\$strReplaceUp" . "Controller";

                $classFinal = new $controller();

                if (!empty($this->getAction()) && $this->getAction() !== null) {  // si une action est définie 
                    $_action = $this->getAction();

                    //var_dump($classFinal); // exemple : "Controllers\ProductsController"
                    //var_dump($_action); // exemple : "displayProductsOfOneCategory"

                    if (\method_exists($classFinal, $this->getAction())) $classFinal->$_action(); // on cheche la methode appellée
                    else 
                    new RendersController(); // methode definie n'existe pas
                 
                }else{
                    new RendersController(); // pas de methode définie 
                }
            } else {
                new RendersController(); // pas de controlleur valide         
            }
        } else {
            new RendersController();  /// si pas de ($_GET['route'])  on va au render    
        }
    }
}
