<?php

namespace Controllers;

class ItemsController extends SecurityController
{

    /**  Methode de vérification de la validité d'in id de produit passé en GET
     */
    public function idItemByGetIsOK() : array
    {
        // verification de l'existance d'un id en GET , que c'est un numeric et > 0 
        if (!array_key_exists('id', $_GET) or !is_numeric($_GET['id']) or (($_GET['id']) < 1)) {
            new RendersController('page404');
            exit;
        }
        $idItem = trim($_GET['id']);
        // verifie si le produit  existe dans la DB et on le recupère 
        $modelItem = new \Models\Items();
        $item = ($modelItem->findOneItem($idItem));

        if (!empty($item)) {
            return $item;
        } else {
            new RendersController('page404'); // pas de produit matchant avec l'id reçu en GET
        }
    }

    /** Affichage du formulaire des Items
     */
    public function displayItems() : void
    {
        if ($this->is_admin()) {
            $data = [];
            $valuesToDisplay = []; // pour recevoir les données à afficher sous forme d'un array .
            // mise en place d'un token pour sécuriser la soumission du formulaire 
            $model = new \Models\Tools();
            $token = $model->randomChain(20);
            $_SESSION['auth'] = $token;

            // on recupère l'id du produit et on le vérifie ( l'id passé en GET )
            $modelProd = new \Controllers\ProductsController();
            $product = $modelProd->idProductByGetIsOK();

            // recherche de tous les items d'un produit 
            $model = new \Models\Items();
            $idProduct = $product['id_product'];
            $valuesToDisplay = $model->getItemsByQuery("items . id_product",$idProduct); // recup d'un tableau à afficher 

            $data['token'] = $token;
            $data['valuesToDisplay'] = $valuesToDisplay;
            $data['idProduct'] = $idProduct; // indispensable pour creer un article si il n'y en a pas de préexistant 
            // affichage de la vue d'affichage en passant $token et $ valuesToDisplay par le render sous $data 

            new RendersController('admin/itemsDisplay', $data);
        } else {
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
    }


    /** Affichage du formulaire de création d'un nouvel item (article)
     */

    public function createItem() : void
    {
        if ($this->is_admin()) {
            // mise en place d'un nouveau token pour sécuriser la soumission du formulaire 
            $model = new \Models\Tools();
            $token = $model->randomChain(20);
            $_SESSION['auth'] = $token;

            // on recupère le produit associé par l'id passé en GET 
            $modelProd = new \Controllers\ProductsController();
            $product = $modelProd -> idProductByGetIsOK();

            // on récupère la table des TVA 
            $model = new \Models\Vat();
            $vatToDisplay = $model->getVatByQuery('status','actif'); // recup d'un tableau à afficher 


            // recup des id des tva dispo pour contrôle lors du traitement du formulaire
            //$idCatAvailable = [];
            foreach ($vatToDisplay as $vat) {
                $idVatAvailable[] = $vat['id_vat'];
            }
            // sauvegarde en session 
            $_SESSION['vat'] = $idVatAvailable;

            $data['token'] = $token;
            $data['vatToDisplay'] = $vatToDisplay;
            $data['product'] = $product;
        
            // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
            new RendersController('admin/itemsAddOrModify', $data);
        } else {
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }

    }

    /** Préparation et affichage du formulaire de modification d'un item (article)
     * -> Utilise le même formulaire que pour la création 
     */

    public function modifyItem() : void 
    {
        if ($this->is_admin()) {
            //  on vérifie que l'id passé en GET est ok et on recupère l'item à  modifier 
            $item = self::idItemByGetIsOK() ;

            // on recupère le produit associé 
            $model = new \Models\Products();
            $product = $model->findOneProduct($item['id_product']);

            // mise en place d'un nouveau token pour sécuriser la soumission du formulaire 
            $model = new \Models\Tools();
            $token = $model->randomChain(20);
            $_SESSION['auth'] = $token;

            // on récupère la table des TVA 
            $model = new \Models\Vat();
            $vatToDisplay = $model->getVatByQuery('status', 'actif'); // recup d'un tableau à afficher 
            // recup des id des tva dispo pour contrôle lors du traitement du formulaire
            //$idCatAvailable = [];
            foreach ($vatToDisplay as $vat) {
                $idVatAvailable[] = $vat['id_vat'];
            }
            // sauvegarde en session 
            $_SESSION['vat'] = $idVatAvailable;

            $data['token'] = $token;
            $data['vatToDisplay'] = $vatToDisplay;
            $data['product'] = $product;
            $data['item'] = $item;

            // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
            new RendersController('admin/itemsAddOrModify', $data);
        } else {
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
       
    }



    /** traitement du formulaire de création/ modification d'un item (article)
     */

     public function createItemProcess() : void
    {
        if ($this->is_admin()) {
            // initialisation des variables    
            $addItem = [
                'id_product' => '',
                'itemRef'    => '',
                'pack'       => '',
                'price'      => '',
                'stock'      => '',
                'id_vat'     => '',
                'status'     => '',
            ];

            // initialisation du tableau des erreurs 
            $errors = [];

            // vérification que le formulaire est complet 
            if (array_key_exists('idProduct', $_POST)
                && array_key_exists('ref', $_POST)
                && array_key_exists('pack', $_POST)
                && array_key_exists('price', $_POST)
                && array_key_exists('stock', $_POST)
                && array_key_exists('vat', $_POST)
                && array_key_exists('status', $_POST)
                ) {

                // on peuple le tableau préparé 
                $addItem = [
                    'id_product'    => trim($_POST['idProduct']),
                    'itemRef'       => trim($_POST['ref']),
                    'pack'          => trim($_POST['pack']),
                    'price'         => trim($_POST['price']),
                    'stock'         => trim($_POST['stock']),
                    'id_vat'        => trim($_POST['vat']),
                    'status'        => trim($_POST['status'])
                ];

                // Mise en oeuvre des contrôles :
                //1) validité du token 
                if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                    $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

                //2) validité de la référence
                if($addItem['itemRef'] == '')
                    $errors[] = "Veuillez renseigner une référence SVP !";

                if (((strlen($addItem['itemRef'])) < 2) or ((strlen($addItem['itemRef'])) > 30)) {
                    $errors[] = "La référence doit comporter entre 2 et 30 caractères";
                    $_POST['ref'] = ""; // pour effacer reaffichage   
                }  

                //3 controle du prix en chiffre
                if ((!is_numeric($addItem['price']))||($addItem['price'])<0)
                    $errors[] = "Le prix doit être un nombre valide";

                //3 controle du stock en chiffre    
                if ((!is_numeric($addItem['stock']))||($addItem['stock'])<0)
                    $errors[] = "Le stock doit être un nombre valide";    

                // 3) controle de la validité de la tva par verification de la validité de l'id  
                $vatAvailable = $_SESSION['vat'];
                $idVat = intval($addItem['id_vat']);
                if (!in_array($idVat, $vatAvailable, false))
                $errors[] = "Hummm, la TVA semble poser un petit problème ";

                // verification du status
                if (($addItem['status'] == '' ||  (($addItem['status'] !== 'actif') && (($addItem['status'] !== 'inactif')))))
                    $errors[] = "Erreur dans le formulaire";


                // si nouvel item  et pas d'erreur -> création de l'item
                if ((!isset($_POST['id'])) && (count($errors) == 0)){
                    $model = new \Models\Items();
                    $model->addNewItem($addItem);
                    $id = $addItem['id_product'];             
                    header("Location: index.php?route=Items&action=displayItems&id=$id");
                    exit();            
                }

                // si il s'agit d'une mise à jour -> methode Update
                if ((isset($_POST['id'])) && (count($errors) == 0)) {
                    $model = new \Models\Items();
                    $model->updateItem(($_POST['id']),$addItem);
                    $id = $addItem['id_product'];
                    header("Location: index.php?route=Items&action=displayItems&id=$id");
                    exit();  
                }
            }

            /********************************************************************
             *  si erreurs, régénération de la vue 
             */
            if (count($errors)==0)
                $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

            $model = new \Models\Tools();
            $token = $model->randomChain(20);
            $_SESSION['auth'] = $token;

            if ((isset($_POST['idProduct']))) {
                $idProduct = $_POST['idProduct'];
            }

            $modelProd = new \Models\Products();
            $product = $modelProd->findOneProduct($idProduct);

            if ($product == FALSE) {
                new RendersController();
                exit;
            }

            // on récupère la table des TVA 
            $model = new \Models\Vat();
            $vatToDisplay = $model->getVatByQuery('status', 'actif'); // recup d'un tableau à afficher 
    
            $data['token'] = $token;
            $data['vatToDisplay'] = $vatToDisplay;
            $data['product'] = $product;
            
            // si il s' agit d'une MAJ on recupère en plus l'item concerné 
            if ((isset($_POST['id']))) {
                $modelItem = new \Models\Items();
                $item = $modelItem->findOneItem($_POST["id"]);
                $data['item'] = $item;
            }

            // affichage de la vue d'affichage en passant les valeurs 
            new RendersController('admin/itemsAddOrModify', $data, $errors);
        } else {
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }

    }

    /** sortie du prix d'un item par l'id (requete Ajax) 
     */
    public function ajaxPrice() : void
    {
        $content = file_get_contents("php://input");
        $data = json_decode($content, true);
        $idToSearch = $data['idToFind'];
        $model = new \Models\Items();
        $item = $model->getItemPrice("id_item", $idToSearch);
        $priceOfItem = $item['price'];
        //include du template
        include 'public/views/price.phtml';
    } 

}
