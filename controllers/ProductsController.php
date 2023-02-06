<?php

namespace Controllers;

class ProductsController extends SecurityController
{

    /** Methode de vérification de la validité d'in id de produit passé en GET 
     * (atention sert également pour créer item ( voir itemscontroller))
     */
    public function idProductByGetIsOK () : array
    {
        // verification de l'existance d'un id en GET , que c'est un numeric et > 0 
        if (!array_key_exists('id', $_GET) or !is_numeric($_GET['id']) or (($_GET['id']) < 1)) {
            new RendersController('page404');
            exit;
        }
        $idProd = trim($_GET['id']);
        // verifie si le produit  existe dans la DB et on le recupère 
        $modelProd = new \Models\Products();
        $product = ($modelProd->findOneProduct($idProd));

        if (!empty($product)) {
            return $product;    
        } else {
            new RendersController('page404'); // pas de produit matchant avec l'id reçu en GET
        }
    }
   

    /** récupération des produits diponibles (actifs) 
     *  */  
    public function getProductsAvailable() : array
    {
        $model = new \Models\Products();
        return $model->getProductsByQuery('status', 'actif'); // recup d'un tableau à afficher des produits actives
    }

    /** Affichage du formulaire de synthèse des produits actis ou non 
     */
    public function displayFormProducts(): void
    {

        if (!$this->is_admin()) {
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }

        $data = [];
        $valuesToDisplay = []; // pour recevoir les données à afficher sous forme d'un array .
        // mise en place d'un token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        $model = new \Models\Products();
        $valuesToDisplay = $model->getProductsByQuery(); // recup d'un tableau à afficher 
        $data[0] = $token;
        $data[1] = $valuesToDisplay;

        new RendersController('admin/productsDisplay', $data);
    }

    /** Affichage du formulaire de création d'un nouveau produit 
     * -> necessite la liste des catégories  $cat[]
     */
    public function displayFormAddProducts() : void
    {
        if ($this->is_admin()) {
            $data = [];
            $catAvailable = [];
            $model = new \Models\Tools();
            $token = $model->randomChain(20);
            $_SESSION['auth'] = $token;

            // recupération des catégories disponibles 
            $model = new \Models\Categories();
            $catAvailable = $model->getCategoriesByQuery('status', 'actif');  

            // recup des id des cat dispo pour contrôle lors du traitement du formulaire
            foreach ($catAvailable as $cat){
                $idCatAvailable [] = $cat['id_category'];
            }
            // sauvegarde en session 
            $_SESSION['cat'] = $idCatAvailable;

            $data[0] = $token;
            $data[1] = $catAvailable;

            // affichage de la vue d'affichage en passant $token et $valuesToDisplay par le render sous $data 
            new RendersController('admin/productsAdd', $data);
        } else {
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
    }

    

    /** Modification d'un produit Affichage du formulaire  - admin
     */
    public function editProduct() : void
    {
        if ($this->is_admin()) {
            // on verifie la validité de l'id passé en get et si c'est ok on recupère le produit 
            $productToModify = self::idProductByGetIsOK();

            // recuperation de toutes les categories (pour affichage de la catégorie actuelle)
            $model = new \Models\Categories();
            $catList = $model->getCategoriesByQuery();

            // recupération des catégories disponibles 
            $model = new \Models\Categories();
            $catAvailable = $model->getCategoriesByQuery('status', 'actif'); // recup d'un tableau à afficher des cats actives
            // recup des id des cat dispo pour contrôle lors du traitement du formulaire
            foreach ($catAvailable as $cat) {
                $idCatAvailable[] = $cat['id_category'];
            }
            // sauvegarde en session 
            $_SESSION['cat'] = $idCatAvailable;

            // mise en place d'un token
            $model = new \Models\Tools();
            $token = $model->randomChain(20);
            $_SESSION['auth'] = $token;

            // nom de la catégorie actuelle  // besion de la liste de toutes les categories
            foreach ($catList as $key => $valeur) {
                if (($productToModify["id_category"]) === ($valeur["id_category"])) {
                    $currentCat = ($valeur["categoryName"]);
                }
            }

            $data[0] = $token;
            $data[1] = $catAvailable;
            $data[2] = $productToModify;
            $data[3] = $currentCat;

            // affichage de la vue de la vue de modification de produit 
            new RendersController('admin/productsModify', $data);
        } else {
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
    }



    /**Création ou Modification d'un produit traitement du formulaire  - admin
     */

    public function AddOrModifyProductProcess() :void
    {
        if ($this->is_admin()) {
            $errors = []; // tableau des erreurs 
            $addProduct = [
                'addCat'            => '',
                'addName'           => '',
                'addRef'            => '',
                'addTeaser'         => '',
                'addDescription'    => '',
                'addInfos'          => '',
                'addPicture'        => '',
                'addStatus'         => ''
            ];
            if (
                array_key_exists('category', $_POST)
                && array_key_exists('productName', $_POST)
                && array_key_exists('reference', $_POST)
                && array_key_exists('teaser', $_POST)
                && array_key_exists('description', $_POST)
                && array_key_exists('infos', $_POST)
                && array_key_exists('status', $_POST)){

                $addProduct = [
                    'addCat'                => trim(($_POST['category'])),
                    'addName'               => trim(($_POST['productName'])),
                    'addRef'                => trim(($_POST['reference'])),
                    'addTeaser'             => trim(($_POST['teaser'])),
                    'addDescription'        => (trim(($_POST['description']))),
                    'addInfos'              => trim(($_POST['infos'])),
                    'addPicture'            => 'default.png',
                    'addStatus'             => trim(($_POST['status'])),
                ];

                // verification de la validité du token 

                if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                    $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

                // verification des 4 champs obligatoires :  catégorie, nom, ref, et etat.    

                if ($addProduct['addCat'] == '')
                    $errors[] = "Un problème est survenu lors de l'envoi du formulaire";

                // verification de la validité de l'id de la catégirie 
                $catAvailable = $_SESSION['cat'];
                $id = intval($addProduct['addCat']);
                if (!in_array($id, $catAvailable, true)) 
                    $errors[] = "Hummm, la catégorie semble poser un petit problème ";

                if ($addProduct['addName'] == '')
                    $errors[] = "Veuillez renseigner un nom SVP!";

                if ($addProduct['addRef'] == '')
                    $errors[] = "Veuillez renseigner une Référence SVP !";
            
                if ($addProduct['addStatus'] =='' || (($addProduct['addStatus'] !== 'actif')&&($addProduct['addStatus'] !== 'inactif')))
                    $errors[] = "Un problème est survenu lors de l'envoi du formulaire";
        
                if (count($errors) == 0) {
                    // On instancie "Products"
                    $model = new \Models\Products();
                    
                /**************************************************
                 * Traitement de l'image - cas d'une création 
                 */
                if ((!isset($_POST['id'])) && isset($_FILES['picture']) && $_FILES['picture']   ['name'] !== '') {

                    $dossier = "img_of_products"; // Nom du dossier dans lequel on va mettre l'image uploadée.
                    $model = new \Models\Uploads(); // on se sert du model Uploads

                    // on appelle  la methode de controle du fichier image qui renvoie le nom concatené avec un uid si tout est ok
                    // ET qui met à jour le tableau d'erreur (passage par reference de $errors)
                    $addProduct['addPicture'] = $model->uploadFile($_FILES['picture'], $dossier, $errors);
                }

                    /*******************************************************
                     * Traitement de l'image - cas d'une mofification 
                     */
                    if (isset($_POST['id'])) {

                        // avec un nouveau fichier image //
                        if (isset($_FILES['picture']) && $_FILES['picture']['name'] !== '') {

                            // on traite la nouvelle image 
                            $dossier = "img_of_products"; // Nom du dossier dans lequel on va mettre l'image uploadée.
                            $model = new \Models\Uploads(); // on se sert du model Uploads
                            // on appelle  la methode de controle du fichier image qui renvoie le nom concatené avec un uid si tout est ok
                        
                            $addProduct['addPicture'] = $model->uploadFile($_FILES['picture'], $dossier, $errors);
                        
                            // si il n' y a pas d'erreur dans le nouveau fichier image on efface l'ancienne.
                            // SAUF s'il s'agit de la photo par defaut 

                            if (count($errors) == 0 && ($_POST['photo_recup']!=='default.png')) {
                                $toErase = "public/uploads/" . (($_POST['photo_recup']));                          
                                if (file_exists($toErase)) {
                                    unlink($toErase);
                                } 
                            } elseif (count($errors) !== 0) {  // sinon on garde l'ancienne                         
                            $addProduct['addPicture'] = ($_POST['photo_recup']);       
                            }

                        } else {  // sans nouveau fichier image, on garde l'ancienne photo

                        // var_dump(' Sans Nouveau fichier image');                  
                            $addProduct['addPicture'] = ($_POST['photo_recup']);
                            // var_dump($addProduct['addPicture']);
                        }
                    }
                    
                    if (count($errors) == 0) {

                        // On créé notre tableau de datasProd à mettre dans la BDD tableau de type cle/valaveur
                        $datasProd = [
                            'id_category'       => $addProduct['addCat'],
                            'productName'       => $addProduct['addName'],
                            'productRef'        => $addProduct['addRef'],
                            'teaser'            => $addProduct['addTeaser'],
                            'description'       => $addProduct['addDescription'],
                            'infos'             => $addProduct['addInfos'],
                            'picture'           => $addProduct['addPicture'],
                            'status'            => $addProduct['addStatus']
                        ];

                        // si il existe $_POST['id'] on est dans le cas de la mofif 
                        if (isset($_POST['id'])){
                            $id_product = trim(htmlspecialchars($_POST['id']));
                            // On appelle la méthode permettant l'INSERT INTO dans la BDD
                            $model = new \Models\Products();
                            $model->UpdateProduct( $id_product, $datasProd);
                        }

                        // ici on est dans le cas d'une création 
                        if (!isset($_POST['id'])) {
                            // On appelle la méthode permettant l'INSERT INTO dans la BDD
                            $model = new \Models\Products();
                            $model->addNewProduct($datasProd);
                            // On affiche un message de validation.
                            $valids[] = 'Votre demande de création de compte a bien été enregistrée.';
                        }
                        // reaffichage pour une autre action sur les produits
                        $model = new \Models\Tools();
                        $token = $model->randomChain(20);
                        $_SESSION['auth'] = $token;
                        //unset($addUser);
                        $_SESSION['message']="insertion ok ";
                        header("Location: index.php?route=products&action=displayFormProducts");
                        exit();   
                    }
                }
            } 
            /** on est ici dans le cas où il y a des erreurs - 
             * on va réafficher le formulaire adéquat
             *  */
            if((count($errors)==0)) $errors[] = "Un problème est survenu lors de l'envoi du formulaire";
            $data = [];
            $catAvailable = [];
            $model = new \Models\Tools();
            $token = $model->randomChain(20);
            $_SESSION['auth'] = $token;
            $modelCat = new \Models\Categories();
            $catAvailable = $modelCat->getCategoriesByQuery('status', 'actif');
            $data[0] = $token;
            $data[1] = $catAvailable;
        
            // cas d'un nouveau produit --------------------------------------------------------------------
            if (!isset($_POST['id'])){
                new RendersController('admin/productsAdd', $data, $errors);
            }
            // cas d'une modification ------------------------------------------------------------------
            if (isset($_POST['id'])){
                //  on recupère le produit à modifier 
                $modelProd = new \Models\Products();
                $productToModify = $modelProd->findOneProduct($_POST["id"]);
                // recuperation de toutes les  categories (pour affichage de la catégorie actuelle)
                $model = new \Models\Categories();
                $catList = $model->getCategoriesByQuery();
                // nom de la catégorie actuelle  // besion de la liste de toutes les categories
                foreach ($catList as $key => $valeur) {
                    if (($productToModify["id_category"]) === ($valeur["id_category"])) {
                        $currentCat = ($valeur["categoryName"]);
                    }
                }
                $data[0] = $token;
                $data[1] = $catAvailable;
                $data[2] = $productToModify;
                $data[3] = $currentCat;
                // affichage de la vue de la vue de modification de produit 
                new RendersController('admin/productsModify', $data, $errors);
            }
        } else {
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
    }
    /**Affichage des produits d'une catégorie - public - Methode appellée par la barre de navigation 
     */

    public function displayProductsOfOneCategory() :void    
    {
        // verification de l'existance d'une cat en GET , que c'est un numeric et > 0 
        if (!array_key_exists('cat', $_GET) or !is_numeric($_GET['cat']) or (($_GET['cat'])<1))  {
            new RendersController('page404');
            exit;
        }
        $idCat = trim($_GET['cat']);

        // verifie si la catégorie existe et si oui on recupère directement le nom de la catégorie.
        $modelCat = new \Models\Categories();
        $categorie = ($modelCat->getOneCategoriesByQuery('id_category', $idCat));  

        if (!empty($categorie)){
            // mise en place d'un token pour sécuriser la soumission du formulaire (future fonctionnilté commande)
            $model = new \Models\Tools();
            $token = $model->randomChain(20);
            $_SESSION['auth'] = $token;

            // tableau regroupant les datas envoyées à la vue via le render
            $data = [];

            // on selectionne l'ensemble des produits actif de la bonne catégorie
            $ProductsToDisplay = []; // pour recevoir les données à afficher sous forme d'un array .
            $model = new \Models\Products();
            $ProductsToDisplay = $model->getProductsPublic('products.status', 'products.id_category', 'actif', $idCat); // recup d'un tableau à afficher

            $newProductsToDisplay = [];
            foreach ($ProductsToDisplay as $key => $value) {
                //id des produits actifs
                $idProd = $value['id_product'];
                //on va chercher les items disponibles ( actifs ) pour un produit par 
                $modelitem  = new \Models\Items();
                $itemsDispo = $modelitem->getItemsPublic('items.status', 'items.id_product', 'actif', $idProd);
                // on raccroche le tableau des items dispo au produit en ajoutant la clé 'items'
                $value['items'] = $itemsDispo;
                $newProductsToDisplay[] = $value;
            }
            $data[0] = $token;
            $data[1] = $newProductsToDisplay;
            $data[2] = $categorie;

            new RendersController('displayProducts',$data);
        } else {
            new RendersController('page404'); // categorie sans produit actif -> pas 
        }
    }

    /** Affichage de tous les produits de toutes les catégories - public - 
     */
    public function displayProductsOfAllCats() :void
    {
            // mise en place d'un token (pour futures commandes)
            $model = new \Models\Tools();
            $token = $model->randomChain(20);
            $_SESSION['auth'] = $token;

            // tableau regroupant les datas envoyées à la vue via le render
            $data = [];

            // on selectionne l'ensemble des produits actif 
            $ProductsToDisplay = []; // pour recevoir les données à afficher sous forme d'un array .
            $model = new \Models\Products();
            $ProductsToDisplay = $model->getProductsPublic('products.status', '1', 'actif', '1');

            $newProductsToDisplay = [];
            foreach ($ProductsToDisplay as $value) {
                //id des produits actifs
                $idProd = $value['id_product'];
                //on va chercher les items disponibles ( actifs ) 
                $modelitem  = new \Models\Items();
                $itemsDispo = $modelitem->getItemsPublic('items.status', 'items.id_product', 'actif', $idProd);
                // on raccroche le tableau des items dispo au produit en ajoutant la clé 'items'
                $value['items'] = $itemsDispo;
                $newProductsToDisplay[] = $value;
            }
            $data[0] = $token;
            $data[1] = $newProductsToDisplay;
            $data[2] = [
                'categoryName' =>'Tous les produits',
            ]; 

            new RendersController('displayProducts', $data);
        
    }

    /** Methode appelée par une requète ajax pour construire page selon recherche
     */

    public function getProductsByRequest() :void
    {
        // mise en place d'un token 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        // tableau regroupant les datas à envoyer à la vue via le render
        $data = [];
        $ProductsToDisplay = []; // pour recevoir les données 

        // Récupérer ce que JS nous a envoyé par ajax
        $content = file_get_contents("php://input");
        $data = json_decode($content, true);

        //$data['textToFind'] = "%Pain%";
        $search = "%" . $data['textToFind'] . "%"; 
        $model = new \Models\Products();
        $ProductsToDisplay = $model->getProductsAjax($search);

        /** $ProductsToDisplay = tableau avec les produits correspondants à la requète
         *  on cherhcher après les items disponibles *****
         */

        $newProductsToDisplay = [];
        foreach ($ProductsToDisplay as $value) {
            //id des produits actifs
            $idProd = $value['id_product'];
            //on va chercher les items disponibles ( actifs ) 
            $modelitem  = new \Models\Items();
            $itemsDispo = $modelitem->getItemsPublic('items.status', 'items.id_product', 'actif', $idProd);
            // on raccroche le tableau des items dispo au produit en ajoutant la clé 'items'
            $value['items'] = $itemsDispo;
            $newProductsToDisplay[] = $value;
        }
        $data['input'][0] = $token;
        $data['input'][1] = $newProductsToDisplay;
        $data['input'][2] = [
            'categoryName' => 'Tous les produits',
        ];
        include "public/views/displayProductsDetails.phtml";
    }


}

