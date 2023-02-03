<?php

namespace Controllers;


class ErrorsController
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

        if ($send){
             $message ="L'administrateur du site est prévenu, nous allons tout mettre en oeuvre pour rétablir la situation dans les meuilleurs délais.";
        }else{
             $message = 'Si la situation persiste merci de bien vouloir contacter l\'administrateur de ce site par mail en précisant le code erreur suivant :'.($e->getCode());
        }
        include 'public/views/errorsWebSite.phtml';
        exit();
    }
    public function ajaxError(): void
    {
        $error=  htmlspecialchars(trim($_GET['err'])) ;

        $message = 'Si la situation persiste merci de bien vouloir contacter l\'administrateur de ce site par mail en précisant le code erreur suivant : Erreur ajax,  ' . $error;
       
        include 'public/views/errorsWebSite.phtml';
        exit();
    }
}







