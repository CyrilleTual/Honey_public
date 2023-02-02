<?php

namespace Controllers;


class ErrorsController
{   
    /** Gestion erreur pdo connexion 
     * @param $e : erreur PDO
     */
    public function pdoError(object $e) :void
    {
        require_once "config/config.php";
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
}







