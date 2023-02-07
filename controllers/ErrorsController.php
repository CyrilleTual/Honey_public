<?php

namespace Controllers;


class ErrorsController extends SecurityController
{
    public function __construct()
    {
        require_once "config/config.php";
    }

    /** Gestion erreur pdo connexion -> version minimaliste du site 
     * @param $e : erreur PDO
     */
    public function pdoError(object $e) :void
    {
        $messageAdmin = ($e->getMessage());  
        $to      = ADMIN_ADDRESS;
        $subject = 'erreur grave vincent petit';
        $messageMail = "erreur $messageAdmin";
        // envoi d'un mail à l'admin ou si echec , message 
        $send=mail($to, $subject, $messageMail);

        $date = date('d.m.Y h:i:s');
        error_log("*** Le ".$date." Erreur: " .$messageAdmin . PHP_EOL,3,'./logErrors.log' );


        if ($send){
             $message ="L'administrateur du site est prévenu, nous allons tout mettre en oeuvre pour rétablir la situation dans les meuilleurs délais.";
        }else{
             $message = 'Si la situation persiste merci de bien vouloir contacter l\'administrateur de ce site par mail';
        }
        include 'public/views/errorsWebSite.phtml';
        exit();
    }
    public function ajaxError(): void
    {
        $messageAdmin=  htmlspecialchars(trim($_GET['err'])) ;
        $date = date('d.m.Y h:i:s');
        error_log("*** Le " . $date . " Erreur Ajax : " . $messageAdmin . PHP_EOL, 3, './logErrors.log');

        $message = 'Si la situation persiste merci de bien vouloir contacter l\'administrateur de ce site par mail';
       
        include 'public/views/errorsWebSite.phtml';
        exit();
    }

    public function manageErrors($errors=null): void
    {
        if (!$this->is_admin()) {
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
        $data = [];
        // token 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
        $data['token'] = $token;
           // recup des errors
        $data['errors'] = $_SESSION['logErrors'];
        new RendersController('admin/logErrorsManage', $data, $errors);
    }


    public function delLogErrors() :void
    {
        if (!$this->is_admin()) {
            $_SESSION['message']  = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('Location: index.php?route=homePage&action=init');
            exit();
        }
        // verification du token 

        if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token']){
            $errors[] = "Une erreur est apparue !";
            self::manageErrors($errors);
            exit();
        }else{
            // on efface le contenu du fichier de log 
            $fichier = fopen('logErrors.log', 'r+'); 
            ftruncate($fichier, 0);
            fclose($fichier);
            // sans oublier d'effacer le contenu de la variable de session !!
            $_SESSION['logErrors'] = [];
            // et on revient sue la page acceuil 
            header('Location: index.php?route=homePage&action=init');
            exit();
        }     
    }
}







