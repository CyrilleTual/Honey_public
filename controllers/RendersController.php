<?php

declare(strict_types=1);

namespace Controllers;

class RendersController
{
    private ?string $view; // view à appeler
    private ?array $data;  // array regroupant les informations à tranmettre vers la vue sauf les erreurs
    private ?array $errors; // array eventuel des erreurs à tramsmettre à la vue 
    
    public function __construct($view = 'default', $input = null, $errors = null) 
    {
        // recupération de la liste des catégories disponibles pour navbar
        $modelCat = new \Models\Categories();
        $data['categories'] = $modelCat->getCategoriesByQuery('categories.status', 'actif'); 
        $data['input'] = $input;

        // hydratation de l'objet RendersController créé
        $this->view   = isset($_GET['view']) ? $_GET['view'] : $view;
        $this->data   = $data;
        $this->errors = $errors;
      
        $this->handleRequest(); // appel de la methode de traitement
    }

    private function handleRequest(): void
    {
        if ($this->view === 'default') {
            header('Location: index.php?route=homePage&action=init');
            exit();

        } else {
            switch ($this->PageExist()) {  // true si la page existe bien dans le site 
                case true:
                    $this->render($this->view, $this->data, $this->errors); // déclanche la méthode render 
                    break;
                case false:
                    $this->render('page404', $this->data, $this->errors); // la page demandée n'existe pas -> on affiche la page 404.phtml
                    break;
                default:
                    header('Location: index.php?route=homePage&action=init');
                    exit();
                    break;
            }
        }
    }
    
    // méthode déclachant l'affichage 
    private function render($view, $data = null, $errors = null): void
    {
        require "public/template/layout.phtml";
    }

    // methode vérifiant l'existance d'une vue 
    private function PageExist(): bool
    {
        if (file_exists("public/views/{$this->view}.phtml"))
            return true;
        else
            return false;

    }
}
