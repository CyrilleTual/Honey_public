<?php

namespace Controllers;

class HomePageController extends SecurityController
{
    public function init() : void
    {
        // creation du caroussel 
        $model = new \Models\CarouselPics();
        $picsToDisplay = $model->getPicsByQuery('status', 'actif'); // recup d'un tableau à afficher des photos actives  

        // creation de la liste des 10 news à afficher 
        $model = new \Models\News();
        $newsToDisplay = $model->getNewsByQuery('status', 'actif', ' DESC', '10');

        // separation de la dernière news pour section " A la une " 
        $lastNews = array_shift($newsToDisplay);

        $errors=[]; 
        $nb = 0 ;
       
        if ($this->is_admin()) {      
            $errorFile = fopen('logErrors.log', 'r');
            while (!feof($errorFile)) {
                $errors[] = fgets($errorFile);    
            }

            if (count($errors)>0){
                $nb = count($errors);
                $mess = $nb>3? 'Attention ' . $nb-1 . ' erreurs sont survenues, voici les 3 dernières :' : 'Attention ' . $nb-1 . ' erreur(s) sont survenues :';

                if($errors[0] == false){  // teste si le contenu du tableau est = false (ligne vide) -> raz de $errors
                    $errors = [];
                }else{
                    // on stocker en session les erreurs pour pouvoir les traiter
                    $_SESSION['logErrors'] = $errors;
                    // on recup les 3 dernières erreurs (le dernier elemet est une ligne vide )
                    $errors = array_slice($errors, -4, 3);
                    array_unshift($errors, $mess);
                }

            }

            
           // $errors[1] == false ? $errors =[]: $errors;


        }
        // appel de l'affichage
        $data['last']= $lastNews;
        $data['pics'] = $picsToDisplay;
        $data['news'] = $newsToDisplay;
        new RendersController('homePage', $data, $errors);






    }

}