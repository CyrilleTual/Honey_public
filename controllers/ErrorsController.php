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
}







