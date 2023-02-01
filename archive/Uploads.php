<?php

namespace Models;

const UPLOADS_DIR = 'public/uploads/';

// Extensions acceptées pour les images
const FILE_EXT_IMG = ['jpg', 'jpeg', 'gif', 'png'];

// Constante MIME_TYPES permettant de vérifier les fichiers uploadés

const MIME_TYPES = array(
    // images
    'png' => 'image/png',
    'jpe' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'gif' => 'image/gif',
    'bmp' => 'image/bmp',
    'ico' => 'image/vnd.microsoft.icon',
    'tiff' => 'image/tiff',
    'tif' => 'image/tiff',
    'svg' => 'image/svg+xml',
    'svgz' => 'image/svg+xml',
);

class Uploads
{

    /**
     * @param array $file -> correspond à $_FILES ( formaulaire ) 
     * contient : name/full_path/type/tmp_name/error/size
     * @param string $dossier -> dossier de stockage (au delà du $folder)
     * @param array &$errors -> tableau des erreurs (passage par reference)
     * @param string $folder -> chemin vers les dosssier
     * @Param array $fileExtention -> extensions  attendues
     */
    public function uploadFile(array $file, string $dossier, array &$errors, string $folder = UPLOADS_DIR, array $fileExtensions = FILE_EXT_IMG)
    {

        $filename = '';
        // On récupère l'extension du fichier pour vérifier si elle est dans $fileExtensions  ( $file -> [] qui vient du formulaire)
        $tmpNameArray = explode(".", $file["name"]);  // ["name"]=> string(47) "Capture d’écran 2022-12-22 à 17.20.14.png" 
        $tmpExt = strtolower(end($tmpNameArray)); // string(3) "png"


        if ($file["error"] === UPLOAD_ERR_OK) {  // cle error du champ de formilaire de type file (0=ok)
            
            /// 1 ) test de l'extension

            $tmpName = $file["tmp_name"];        // chemin vers le fichier temporaire 
            if (in_array($tmpExt, $fileExtensions)) {// check si extention du fichier téléchargé est dans notre tableau

                // on remplace les espaces eventuels par des _ car le nom de fichier
                // servira d'id 
                $namePic = str_replace (" ","_",(basename($file["name"])));

                // creation d'un nom unique pour notre fichier 
                // $filename = uniqid() . '-' . basename($file["name"]);
                $filename = uniqid() . '-' . $namePic;

                if (!move_uploaded_file($tmpName, $folder . $dossier . "/" . $filename)) {
                    $errors[] = "Le fichier n'a pas été enregistré correctement";
                }
                // mime_content_type
                // Détecte le type de contenu d'un fichier.
                // On vérifie le contenue de fichier, pour voir s'il appartient aux MIMES autorises.
                if (!in_array(mime_content_type($folder . $dossier ."/". $filename), MIME_TYPES, true)) {                    
                    // si erreur on efface le fichier uploadé 
                    $toErase= $folder . $dossier . "/" . $filename;
                    unlink($toErase);
                    $errors[] = "Le fichier n'a pas été enregistré correctement car son contenu ne correspond pas à son extension !";
                }
            } else {
                $errors[] = "Ce type de fichier n'est pas autorisé !";
            }
        } else if ($file["error"] == UPLOAD_ERR_INI_SIZE || $file["error"] == UPLOAD_ERR_FORM_SIZE) {
            //fichier trop volumineux
            $errors[] = "Le fichier est trop volumineux";
        } else {
            $errors[] = "Une erreur a eu lieu au moment de l'upload";
        }
        $filename = $dossier . "/" . $filename;
        return $filename;
    }
}
