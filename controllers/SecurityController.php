<?php

namespace Controllers;

class SecurityController
{ 
    /** Verifie le statut client
     * @return boll true si ok 
     */
    public function is_client() :bool
    {
        if (isset(($_SESSION['user'])['role'])
            && (!empty($_SESSION['user']))
            && (($_SESSION['user'])['role']) === 'client'
            && (($_SESSION['user'])['status']) === 'actif'
        ) {
            return true;
        } else {
            return false;
        }
    }

    /** Verifie le statut admin
     * @return boll true si ok 
     */
    public function is_admin() :bool
    {
        if (isset(($_SESSION['user'])['role']) 
            && (!empty($_SESSION['user'])) 
            && (($_SESSION['user'])['role']) === 'admin'
            && (($_SESSION['user'])['status']) === 'actif'){
            return true;
        }else{
            return false;
        }
    }

}