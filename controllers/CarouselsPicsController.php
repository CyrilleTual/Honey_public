<?php

namespace Controllers;

use Models\Model;

class CarouselsPicsController
{
    /** Methode de vérification de la validité d'in id de photo passé en GET 
     */
    public function idPicByGetIsOK () : array
    {
        // verification de l'existance d'un id en GET , que c'est un numeric et > 0 
        if (!array_key_exists('id', $_GET) or !is_numeric($_GET['id']) or (($_GET['id']) < 1)) {
            new RendersController('page404');
            exit;
        }
        $idPic = trim($_GET['id']);
        // verifie si la photo existe dans la DB et on la recupère 
        $modelPic = new \Models\CarouselPics();
        $product = ($modelPic->findOnePic($idPic));

        if (!empty($product)) {
            return($product);  
        } else {
            new RendersController('page404'); // pas de produit matchant avec l'id reçu en GET
            exit;
        }
    }

    /** Affichage du formulaire d'ajout - admin
     * @param array : $errors pour réaffichage apres erreur(s)
     */

    public function displayFormAddPic(array $errors=null) :void
    {   
        // mise en place d'un nouveau token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
        $data['token'] = $token;
        // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
        new RendersController('admin/carouselAddOrModify', $data, $errors);

    }

    /** Affichage du formulaire de synthèse des photos du slider - admin
     * @param array $errors pour réaffichage apres erreur(s)
     */
    public function displayFormPicsSlider(array $errors = null) :void
    {
        $data = [];
        $valuesToDisplay = []; // pour recevoir les données à afficher sous forme d'un array .
        $model = new \Models\CarouselPics();
        $valuesToDisplay = $model->getPicsByQuery(); // recup d'un tableau à afficher 
        // mise en place d'un token pour sécuriser la soumission du formulaire 
        // generation d'un tableau de token ( 1 par ligne car delete)
        $nbOfValues = count($valuesToDisplay);

        $token = [];

        for ($i = 0; $i < $nbOfValues; $i++) {
            $model = new \Models\Tools();
            $token[$valuesToDisplay[$i]['id_carousel']] = $model->randomChain(20);
        }

        $_SESSION['auth'] = $token;
        $data["token"] = $token;
        $data["values"] = $valuesToDisplay;

        new RendersController('admin/carouselDisplay', $data, $errors);
    }

    /** récupération des photos diponibles (staut actif)  (pour slider public)
     *  */  
    public function getPicsAvailable() : array
    {
        $model = new \Models\CarouselPics();
        return $model->getPicsByQuery('status', 'actif'); // recup d'un tableau à afficher des photos actives
    }


    /** Modification d'une photo -  Affichage du formulaire  - admin
     */
    public function modifyPic() :void 
    {
        // on verifie la validité de l'id passé en get et si c'est ok on recupère la photo 
        $productToModify = self::idPicByGetIsOK();

        // mise en place d'un token
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        $data['token'] = $token;
        $data['pic'] = $productToModify;

        // affichage de la vue de la vue de modification de la photo
        new RendersController('admin/carouselAddOrModify', $data);
    }


    /** Delete d'une photo - traitement de la demande - admin
     */
    public function deletePic() :void
    {
        $errors = []; // tableau des erreurs 

        // verification de la validité du token 
        if (isset($_SESSION['auth'][$_POST['id']]) && $_SESSION['auth'][$_POST['id']] != $_POST['token'])
            $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";
        // recupération et vérification de la validité de l'id passé en POST exit si pas bon  
        if (!array_key_exists('id', $_POST) or !is_numeric($_POST['id']) or (($_POST['id']) < 1)) 
            $errors[] = "Une erreur est apparue";
            
        $id = trim($_POST['id']);
        // verifie si une news existe dans la DB et on la recupère 
        $modelPic = new \Models\CarouselPics();
        $pic = ($modelPic->findOnePic($id));
        if (empty($pic)) $errors[] = "Une erreur est apparue";

        // si tout abs erreur -> delete 
        if (count($errors) == 0) {
            // appel de la methode d'effacement dans la BD
            $eraseok = ($modelPic->delOnePic($pic['id_carousel'])); // true si effacement ok (PDO)
            // effacement de la photo stockée 
            if ($eraseok) {
                $toErase = "public/uploads/".($pic['picture']);
                if (file_exists($toErase)) {
                    unlink($toErase);
                }
            }
        } else {
            self::displayFormPicsSlider($errors); // cas où il y a des erreurs 
            exit();
        }
        self::displayFormPicsSlider();
        exit();
    }
    
    /** Création ou Modification photo - traitement du formulaire  - admin
     */

    public function AddOrModifyCarouselPic() :void 
    {
        $errors = []; // tableau des erreurs 
        $addPic = [
            'addDescription'    => '',
            'addPicture'        => '',
            'addStatus'         => ''
        ];

        if (
            array_key_exists('description', $_POST)
            && array_key_exists('status', $_POST)
        ) {

            $addPic = [
                'addDescription'        => trim(($_POST['description'])),
                'addPicture'            => 'default.png',
                'addStatus'             => trim(($_POST['status'])),
            ];

            // verification de la validité du token 
            if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

            // verifie que l'on a soit nouvelle image, soit ancienne 
            if ((isset($_FILES['picture']) && $_FILES['picture']['name'] == '' ) && (!isset($_POST['id'])))
                $errors[] = "Erreur avec le fichier image "; 

            // verification description et etat.    
            if ($addPic['addDescription'] == '')
                $errors[] = "Merci de renseigner une description";

            if ($addPic['addStatus'] == ''||  (($addPic['addStatus'] !== 'actif')&& (($addPic['addStatus'] !== 'inactif'))))
                $errors[] = "Erreur dans le formulaire";

            // si pas d'erreurs à ce niveau . 
            if (count($errors) == 0) {
                // On instancie "CarouszlsPics"
                $model = new \Models\CarouselPics();

                /**************************************************
                 * Traitement de l'image - cas d'une création 
                 */
                if ((!isset($_POST['id'])) && isset($_FILES['picture']) && $_FILES['picture']['name'] !== '') {
                    //var_dump(' creation');
                    $dossier = "slider"; // Nom du dossier dans lequel on va mettre l'image uploadée.
                    $model = new \Models\Uploads(); // on se sert du model Uploads
                    // on appelle  la methode de controle du fichier image qui renvoie le nom concatené avec un uid si tout est ok
                    // ET qui met à jour le tableau d'erreur (passage par reference de $errors)
                    $addPic['addPicture'] = $model->uploadFile($_FILES['picture'], $dossier, $errors);
                }

                /*******************************************************
                 * Traitement de l'image - cas d'une mofification 
                 */
                if (isset($_POST['id'])) {
                    // avec un nouveau fichier image //
                    if (isset($_FILES['picture']) && $_FILES['picture']['name'] !== '') {
                        // on traite la nouvelle image 
                        $dossier = "slider"; // Nom du dossier dans lequel on va mettre l'image uploadée.
                        $model = new \Models\Uploads(); // on se sert du model Uploads
                        // on appelle  la methode de controle du fichier image qui renvoie le nom concatené avec un uid si tout est ok
                        $addPic['addPicture'] = $model->uploadFile($_FILES['picture'], $dossier, $errors);

                        // si il n' y a pas d'erreur dans le nouveau fichier image on efface l'ancienne.
                        if (count($errors) == 0) {
                            $toErase = "public/uploads/".(($_POST['photo_recup']));
                            if (file_exists($toErase)) {
                                unlink($toErase);
                            }
                        } elseif (count($errors) !== 0) {  // sinon on garde l'ancienne en recuperant le nom 
                            $addPic['addPicture'] = ($_POST['photo_recup']);
                        }

                    } else {  // sans nouveau fichier image, on garde l'ancienne photo
                        // var_dump(' Sans Nouveau fichier image');
                        $addPic['addPicture'] = ($_POST['photo_recup']);
                        // var_dump($addProduct['addPicture']);
                    }
                }

                if (count($errors) == 0) {
                    // On créé notre tableau à mettre dans la BDD tableau de type cle/valaveur
                    $datasCarouselPic = [
                        'description'       => $addPic['addDescription'],
                        'picture'           => $addPic['addPicture'],
                        'status'            => $addPic['addStatus']
                    ];

                    // ici on est dans le cas d'une création 
                    if (!isset($_POST['id'])) {
                        // On appelle la méthode permettant l'INSERT INTO dans la BDD
                        $model = new \Models\CarouselPics();
                        $model->addNewPic($datasCarouselPic);
                        // On affiche un ou plusieurs messages de validation.
                        $valids[] = 'Votre demande de création de compte a bien été enregistrée.';
                    }

                    // si il existe $_POST['id'] on est dans le cas de la mofif et non de la création
                    if (isset($_POST['id'])) {
                        $id_carousel = trim(htmlspecialchars($_POST['id']));
                        // On appelle la méthode permettant l'INSERT INTO dans la BDD
                        $model = new \Models\CarouselPics();
                        $model->updatePic($id_carousel, $datasCarouselPic);
                    }
                    // on appelle la page de gestion des images 
                    self::displayFormPicsSlider();
                    exit();
                }
                self::displayFormPicsSlider($errors);
                exit;
            }
            self::displayFormPicsSlider($errors);
            exit; 
        }
        $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";
        self::displayFormPicsSlider($errors);
        exit;
    }


    /** sortie du tableau des pics pour le caroussel (requete Ajax) 
     */
    public function ajaxPics() :void
    {
        $model = new \Models\CarouselPics();
        $picsToDisplay = $model->getPicsByQuery('status', 'actif'); // recup d'un tableau à afficher des photos actives      
        include 'public/views/carousel.phtml';
    } 

}