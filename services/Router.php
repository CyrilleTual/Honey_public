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
        if (!empty($this->route) && $this->route !== null) { // une route est définie 
            $strReplaceUp = ucfirst($this->route); // passe en majuscule la premiere lettre pour reconstituer le controller appelé
            $nomfichier = str_replace("\\", "/", "controllers\\$strReplaceUp" . 'Controller.php'); // pour en vérifier l'existence

            if (file_exists($nomfichier)) {  // le controlleur existe 

                $controller = "controllers\\$strReplaceUp" . "Controller";
                $classFinal = new $controller();
                if (!empty($this->getAction()) && $this->getAction() !== null) {  // si une action est définie 
                    $_action = $this->getAction();
                    if (\method_exists($classFinal, $this->getAction())) $classFinal->$_action(); // on cheche la methode appellée
                    else
                    header('Location: index.php?route=homePage&action=init'); // methode definie n'existe pas                 
                }else{
                    header('Location: index.php?route=homePage&action=init'); // pas de methode définie 
                    exit();
                }
            } else {
                header('Location: index.php?route=homePage&action=init'); // pas de controlleur valide 
                exit();       
            }
        } else {
            new RendersController();  /// si pas de ($_GET['route'])  on va au render (traite le cas où il existe une view)   
        }
    }
}
