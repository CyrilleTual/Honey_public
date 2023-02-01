<?php

namespace Controllers;

class HomePageController
{
    public function init() : void
    {
        // creation du caroussel 
        $model = new \Models\CarouselPics();
        $picsToDisplay = $model->getPicsByQuery('status', 'actif'); // recup d'un tableau à afficher des photos actives  

        // creation de la liste des 10 news à afficher 
        $model = new \Models\News();
        $newsToDisplay = $model->getNewsByQuery('status', 'actif', ' DESC', '10');

        // selection de la dernière news pour section " A la une " 
        $lastNews = array_shift($newsToDisplay);

        $data['last']= $lastNews;
        $data['pics'] = $picsToDisplay;
        $data['news'] = $newsToDisplay;
        new RendersController('homePage', $data);
    }

}