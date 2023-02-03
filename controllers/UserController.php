<?php

namespace Controllers;

class UserController extends SecurityController
{

    /**  Methode de vérification de la validité d'id passé en GET 
     * @return array retourne l'user
     */
    public function idUserByGetIsOK() : array 
    {
        // verification de l'existance d'un id en GET , que c'est un numeric et > 0 
        if (!array_key_exists('id', $_GET) or !is_numeric($_GET['id']) or (($_GET['id']) < 1)) {
            new RendersController('page404');
            exit;
        }
        $id = trim($_GET['id']);
        // verifie si l'user existe
        $model = new \Models\Users();
        $user = ($model->getUsers('id_user', $id));

        if (!empty($user)) {
            return ($user);
        } else {
            new RendersController('page404'); // pas de produit matchant avec l'id reçu en GET
            exit;
        }
    }



    /** Affichage du formulaire d'enregistrement d'un nouvel user
     */
    public function displayFormRegister(): void
    {
        // teste si user déja loggé 
        if (isset((($_SESSION['user'])['role'])) && (!empty($_SESSION['user']))) {
            $_SESSION['message']  = "Vous êtes déja connecté, création de compte impossible";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
        // mise en place d'un token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
 
        // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
        new RendersController('formRegister', $token);
    }


    /** Traitement, validation et soumission du form de création de compte
     */
    public function register(): void
    {
        // initialisation du tableau des erreurs 
        $errors = [];

        // initialisation des variables 
        $valids = [];
        $addUser = [
            'addNom'                => '',
            'addPrenom'             => '',
            'addEmail'              => '',
            'addPassword'           => '',
            'addPassword_confirme'  => '',
        ];

        // vérification du remplissage du formulaire 
        if (
            array_key_exists('firstname', $_POST)
            && array_key_exists('lastname', $_POST)
            && array_key_exists('email', $_POST)
            && array_key_exists('password', $_POST)
            && array_key_exists('passwordConfirm', $_POST)
        ) {       

            $addUser = [
                'addPrenom'             => trim(ucfirst($_POST['firstname'])),
                'addNom'                => trim(strtoupper($_POST['lastname'])),
                'addEmail'              => trim(strtolower($_POST['email'])),
                'addPassword'           => trim($_POST['password']),
                'addPassword_confirme'  => trim($_POST['passwordConfirm']),
            ];

            // Mise en oeuvre des contrôles :
            //1) validité du token 
            if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

            // 2) validité du prénom ;
            if ($addUser['addPrenom'] == '') {
                $errors[] = "Veuillez renseigner votre prénom SVP !";
              
            }elseif(((strlen($addUser['addPrenom'])) <2 ) or ((strlen($addUser['addPrenom'])) > 30)){
                $errors[] = "Le prenom doit comporter entre 2 et 30 caractères";
                $_POST['firstname'] = ""; // pour effacer reaffichage 

            }elseif(!preg_match('/^[a-zA-Z-\'\ ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ]{2,30}$/', $addUser['addPrenom'])) {
                $errors[] = "Le prenom ne peux comporter que des lettres, espaces, tirets et apostrophes .";
                $_POST['firstname'] = ""; // pour effacer reaffichage 
            }

            //3) validité du nom
            if ($addUser['addNom'] == ''){
                $errors[] = "Veuillez renseigner votre nom SVP !";
            }elseif(((strlen($addUser['addNom'])) < 2) or ((strlen($addUser['addNom'])) > 50)) {
                $errors[] = "Le nom doit comporter entre 2 et 50 caractères";
                $_POST['lastname'] = ""; // pour effacer reaffichage 

            }elseif(!preg_match('/^[a-zA-Z-\'\ ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ]{2,30}$/', $addUser['addNom'])) {
                $errors[] = "Le nom ne peux comporter que des lettres, espaces, tirets et apostrophes.";
                $_POST['lastname'] = ""; // pour effacer reaffichage 
            }

            // 4) validité de l'email
            if (!filter_var($addUser['addEmail'], FILTER_VALIDATE_EMAIL)){
                $errors[] =  'Veuillez renseigner un email valide SVP !';
                $_POST['email'] = ""; // pour effacer reaffichage 
            }
               

            // 5) validité du mot de passe    
           
            if (empty($addUser['addPassword'])){
                $errors[] = "Veuillez renseigner votre mot de passe !";
            } elseif (((strlen($addUser['addPassword'])) < 8) or ((strlen($addUser['addPassword'])) > 31)) {
                $errors[] = "Le mot de passe doit comporter entre 8 et 30 caractères";
            } elseif (!preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/', $addUser['addPassword'])) {
                $errors[] = "Mot de passe non conforme  !";
            }elseif($addUser['addPassword'] !== $addUser['addPassword_confirme'])
                $errors[] = "Vous n'avez pas confirmé correctement votre mot de passe !";

            // si le formulaire est valide on va tester si l'adresse mail existe dans la DB: 

            if (count($errors) == 0) {             
                // on teste l'existance de l'adesse mail dans la Base    
                $model = new \Models\Users();
                $userExist = $model->getUserByEmail($addUser['addEmail']); // retourne un tableau contenant l'user si existe ou tableau vide
                if (!empty($userExist)) {
                    $errors[] = 'Cette adresse e-mail existe déjà !';
                }

                // cas du + avec les adressses mail (evite les duplications sur alias)
                // test si le signe + existe dans le mail
                $mail = ($addUser['addEmail']);
                $posPlus = strpos(($addUser['addEmail']), "+");
                if ($posPlus !== false) {
                    $debut = substr($mail, 0, $posPlus);
                    // on recupère ce qui est après l'@
                    $posAt = strpos(($addUser['addEmail']), "@");
                    $long = strlen($mail);
                    $end = $long - $posAt;
                    $fin = substr($mail, -$end);
                    //reconstitution de l'adresse mail
                    $secondMail = $debut . $fin;
                    // on verifie l'existance dans la Db:
                    $model = new \Models\Users();
                    $userExist = $model->getUserByEmail($secondMail);
                    if (!empty($userExist)) {
                        $errors[] = 'Vous semblez déjà inscrit, les alias ne sont pas autorisés';
                    }
                }

            }    

            /// si tout est ok on traite l'insertion dans la Db
            if (count($errors) == 0) {

                // on securise le PWD
                $datas = [
                    'firstname' => trim(ucfirst($_POST['firstname'])),
                    'lastname'  => trim(strtoupper($_POST['lastname'])),
                    'email'     => trim(strtolower($_POST['email'])),
                    'password'  => password_hash(trim($_POST['password']), PASSWORD_DEFAULT),
                ];

                $model->addNewUser($datas);

                // on recuprère les informations de l'utilisateur et on creer la session (permet de verifier l'insertion et de recup id)
                $userExist = $model->getUserByEmail($addUser['addEmail']); // retourne un tableau contenant l'user si existe ou tableau vide
                if (empty($userExist)) {
                    $errors[] = 'Un problème est survenu';
                } else {
                    $userExist = $userExist[0]; // recupere l'user  

                    // creation de session
                    $_SESSION['user'] = [
                        'id'            => $userExist['id_user'],
                        'firstName'     => $userExist['firstname'],
                        'lastName'      => $userExist['lastname'],
                        'email'         => $userExist['email'],
                        'role'          => $userExist['role'],
                        'status'        => $userExist['status'],
                    ];

                    $_SESSION['message']  = "Vous êtes maintenant enregistré et connecté ! ";
                    header('Location: index.php?route=homePage&action=init');
                    exit();
                }
                ///////////////////////////////////////////////////////////////////////
                /////////////////////////////////////////////////////////////////////////

            }
        }
        // reaffichage de la vue avec regeration du token et passage du tableau d'erreurs
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
        // affichage de la vue d'affichage en passant $token 
        new RendersController('formRegister', $token, $errors);
    }


    /** Affichage du formulaire de connexion 
     */
    public function displayFormConnect() : void
    {
        // teste si user déja loggé 
        if (isset((($_SESSION['user'])['role'])) && (!empty($_SESSION['user']))) {
            $_SESSION['message']  = "Vous êtes déja connecté ! ";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
        // mise en place d'un token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
        // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
        new RendersController('formConnect', $token);
    }

    /** Traitement, validation du login de session 
     */
    public function checkAndConnect() : void
    {
        // teste si user déja loggé 
        if (isset((($_SESSION['user'])['role'])) && (!empty($_SESSION['user']))) {
            $_SESSION['message']  = "Vous êtes déja connecté";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }

        $model = new \Models\Users();

        $errors = []; // initialisation du tableau des erreurs 
        $errorsArray = new \Models\ErrorMessages(); // 
        $messagesErrors = $errorsArray->getMessages();
        /**
         * et quand une erreur se produit on alimente le tableau des erreurs :
         * $errors[] = $messagesErrors [x];  où X est le numero du message d'erreur
         */

        // initilisation du tableau de récupération des datas
        $authUser = [
            'email'              => '',
            'password'           => ''
        ];
        // recupération des datas si les clés existent 
        if (array_key_exists('email', $_POST) && array_key_exists('password', $_POST)) {

            // on peuple le tableau des datas 
            $authUser = [
                'email'     => trim(strtolower($_POST['email'])),
                'password'  => trim($_POST['password']),
            ];

            // verif token
            if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
            $errors[] = $messagesErrors[0];

            // verif de la validité du email par fonction native pph filter_var sur FILTER_VALIDATE_EMAIL
            if (!filter_var($authUser['email'], FILTER_VALIDATE_EMAIL))
                $errors[] = $messagesErrors[6];

            // verif mdp bien renseigne
            if (empty($authUser['password']))
                $errors[] = $messagesErrors[6];

            // verif token
            if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                $errors[] = $messagesErrors[0];

            // en l'absence d'erreur  

            if (count($errors) == 0) {

                // on va tester l'existance du user dans la Db

                $model = new \Models\Users();
                $userExist = $model->getUserByEmail($authUser['email']); // retourne un tableau contenant l'user si existe ou tableau vide
                if (empty($userExist)) {
                    $errors[] = $messagesErrors[8];
                    // var_dump($errors); -> array(1) { [0]=> string(21) "Erreur identification" }
                } else {
                    $userExist = $userExist[0]; // recupere l'user seul 
                    // on verifie la concordence des mots de passe 
                    $pwdForm = $authUser['password'];
                    $pwdDataBase = $userExist["password"];
                    //$passwordOK = password_verify($authUser['password'], $userExist['password']);
                    $passwordOK = password_verify($pwdForm, $pwdDataBase);

                    //$passwordOK = ($pwdForm === $pwdDataBase); // pour test pwd non cryptés

                    if ($userExist !== false && $passwordOK) {

                        //verification du statut actif de l'utilisateur
                        $status = $userExist["status"];

                        if ($status !== 'actif') {
                            $_SESSION['message']  = "Vous n'êtes pas autorisé à vous connecter";
                            new RendersController();
                            exit();
                        } else {
                            // creationde session
                            $_SESSION['user'] = [
                                'id'            => $userExist['id_user'],
                                'firstName'     => $userExist['firstname'],
                                'lastName'      => $userExist['lastname'],
                                'email'         => $userExist['email'],
                                'role'          => $userExist['role'],
                                'status'        => $userExist['status'],
                            ];

                            $_SESSION['message']  = "bien connecté ! ";
                            header('Location: index.php?route=homePage&action=init');
                            exit();

                        }
                    } else {
                        $errors[] = $messagesErrors[8];
                    }

                    //////////////////////////////////////////////////////////////////////////////////
                }
            }
        }


        // reaffichage de la vue avec regeration du token et passge du tableau d'erreurs
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
        // affichage de la vue d'affichage en passant $token 
        new RendersController('formConnect', $token, $errors);
    }

    /** Deconnexion / fin de session
     */
    public function logoutUser() : void
    {
        unset($_SESSION['user']); // methode de cyrille
        // $_SESSION['user'] = []; // methode de micke
        session_destroy();
        header('Location: index.php?route=homePage&action=init');
        exit();
    }


    /** Affichage du formulaire de gestion des roles et status des utilisateurs
     */
    public function displayFormAdminUsers($errors = null) : void
    {

        if ($this->is_admin()) {
            // mise en place d'un token pour sécuriser la soumission du formulaire 
            $model = new \Models\Tools();
            $token = $model->randomChain(20);
            $_SESSION['auth'] = $token;

            $model = new \Models\Users();
            $valuesToDisplay = $model->getUsers(); // recup d'un tableau à afficher 
            $data["token"] = $token;
            $data["values"] = $valuesToDisplay;

            // affichage de la vue d'affichage en passant les données
            new RendersController('admin/userManagement', $data, $errors);

        } else {
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
    }

    /** Modification d'un utilisateur -  Affichage du formulaire  - admin
     */
    public function modifyUser():void
    {

        if($this->is_admin()){
            // on verifie la validité de l'id passé en get et si c'est ok on recupère les infos
            $ToModify = self::iduserByGetIsOK();
            // on reverifie qu'il ne s'agir pas du super utilisateur ( id =1 )
            if ($ToModify[0]['id_user'] !== 1) {
                // mise en place d'un token
                $model = new \Models\Tools();
                $token = $model->randomChain(20);
                $_SESSION['auth'] = $token;
                $data['token'] = $token;
                $data['user'] = $ToModify[0];
                // affichage de la vue de la vue de modification 
                new RendersController('admin/userModify', $data);
                exit();
            };
            // si on tente d'agir sur le super utilidateur c'est du piratage !!
            session_destroy();
            header('Location: index.php?route=homePage&action=init');
            exit();
        }else{
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
        
    }

    /** Traitement du formulaire de modification d'un utilisateur
     */

    public function modifyUserProcess():void 
    {
        if ($this->is_admin()) {
            // protection si tentative modif super-utilisateur
            if($_POST['idUser'] == 1){
                session_destroy();
                header('Location: index.php?route=homePage&action=init');
                exit();
            }
            // initialisation des variables 
            $modifUser = [
                'role'    => '',
                'status'  => '',
            ];

            // initialisation du tableau des erreurs 
            $errors = [];
            $errorsArray = new \Models\ErrorMessages(); // 
            $messagesErrors = $errorsArray->getMessages();

            // vérification que le formulaire est complet 
            if (
                array_key_exists('idUser', $_POST)
                && array_key_exists('role', $_POST)
                && array_key_exists('status', $_POST)
                ) {

                // on peuple le tableau préparé 
                $modifUser = [
                    'role'       => trim($_POST['role']),
                    'status'     => trim($_POST['status'])
                ];

                // Mise en oeuvre des contrôles :
                //1) validité du token 

                if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

                //2) validité du role
                if ($modifUser['role'] == '' || (($modifUser['role']!=='client')&&($modifUser['role'] !== 'admin')))
                $errors[] = "Erreur";
                

                //3) validité du statut
                if ($modifUser['status'] == '' || (($modifUser['status'] !== 'actif') && ($modifUser['status'] !== 'inactif')))
                $errors[] = "Erreur";
                

                //  il s'agit d'une mise à jour -> methode Update

                if ((isset($_POST['idUser'])) && (count($errors) == 0)) {
                    $model = new \Models\Users();
                    // verification de l'existance de l'user 
                    $userExist = $model->getUsers('id_user', $_POST['idUser']); // retourne un tableau contenant l'user si existe ou tableau vide
                    if (empty($userExist)) {
                        $errors[] = $messagesErrors[8];
                        // var_dump($errors); -> array(1) { [0]=> string(21) "Erreur identification" }
                    } else {
                        $model->updateUser(($_POST['idUser']), $modifUser); 
                        self::displayFormAdminUsers();
                        exit();
                    }
                        new RendersController('homePage');
                    exit();
                }
                


            }

            /********************************************************************
             *  si erreurs, régénération de la vue 
             */
            $errors[] = "Une erreurs est survenue, modification non prise en compte";
            self::displayFormAdminUsers($errors);
            exit();

        }else{
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
    }

}



