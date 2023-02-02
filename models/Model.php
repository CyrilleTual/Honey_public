<?php

namespace Models;

class Model
{
    private  $pdo; // représente l'instance de connexion à la DB

    /******************************************************************
     * création d'un instance PDO pour connextion à la base de données
     */
    public function __construct()
    {  
        if ($this->pdo === null){
            $config = require 'config/database_dist.php'; // recup des paramètres de connxion
            try {
                $this->pdo = new \PDO("mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'] . ";charset=" . $config['db_utf'], $config['username'], $config['password'], [
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, // retourne un tableau indexé par le nom de la colonne 
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION // lance PDOExeptions
                ]);
            } catch (\PDOException $e) {
                $error = new \Controllers\ErrorsController;
                $error->pdoError($e);
                die;
            }
        }
    }

    /***********************************************************************************
     * Définition de méthodes génériques pour les manupulations de la base de données *
     *********************************************************************************/

    /********************************************************************************
     * findAll -> retourne tous les enregistrements d'une tabble  (READ)
     * @param : aucun, se sert de l'objet sur lequel la méthode est utilisée
     * @return array Tableau des enregistrements trouvés  
     */
    // protected function findAll(): array | false
    // {
    //     $query = $this->pdo->prepare("SELECT * FROM $this->table ");
    //     $query->execute();
    //     return $query->fetchAll();
    // }
    // // utilisation : return $this->findAll(); 

    /** findByQuery ->  attend en paramètre une requète sql et un tableau de paramètres
     * @param string $sdl -> requète préparée 
     * @param array $datas -> tableau valeurs  (pour les contraintes sur le WHERE)
     * @return array  comprenant les enregistrements trouvés
     */
    protected function findByQuery(string $sql, array $datas = []): array | false
    {
        try{
            $query = $this->pdo->prepare($sql);
            $query->execute($datas);
            return $query->fetchAll();
        } catch(\PDOException $e){
            $error = new \Controllers\ErrorsController;
            $error->pdoError($e);
            die;
        }
        
    }
    // utilisation -> voir le README


    /** findOneByQuery -> ATTENTION DIFFERENCE avec la précédente FETCH et non FETCH ALL  adapté pour 1 seul retour
     * @param string $sdl -> requète préparée 
     * @param array $datas -> tableau valeurs  (pour les contraintes sur le WHERE)
     * @return array  comprenant l'enregistrement trouvé
     */
    protected function findOneByQuery(string $sql, array $datas = []): array | false
    {
        try{
            $query = $this->pdo->prepare($sql);
            $query->execute($datas);
            return $query->fetch();
        }catch(\PDOException $e){
            $error = new \Controllers\ErrorsController;
            $error->pdoError($e);
            die;
        }
       
    }


    /** Fonction split  : pour eclater un tableau de type [champs:valeurs] en une sring de champs incluant " =?" et un tableau de valeurs à passer en paramètres
     *  @param array $ tableau ['champ1'=>'value1', 'champ2=>value2' etc...]
     *  @return array Tableau  ['string de champs','tableau des valeurs']
     */
    protected function split(array $toSplit): array 
    {
        // On boucle pour éclater $params -> stockage des champs et des values indépendament
        $champs = [];
        $valeurs = [];
        foreach ($toSplit as $champ => $valeur) {
            $champs[]   = "$champ = ?";
            $valeurs[]  = $valeur;
        }
        // On transforme les tableaux en une chaîne de caractères séparés par des AND
        $liste_champs = implode(' AND ', $champs);   //  ex : "user_id = ? AND user_lastName = ?"
        return [$liste_champs, $valeurs];
    }
    // utilisation [$liste_champs, $valeurs] = $this->split($params)


    /** findOne -> un enregisterment par son id  (READ)
     * @param int : valeur de l'id 
     * @return array Tableau de l'enregistrement trouvé ou false si pas enregistrement 
     */
    protected function findOne(int $id): array | false
    {
        try{
            // preparation de la requête
            $query = $this->pdo->prepare("SELECT * 
                    FROM    $this->table 
                    WHERE   $this->idName = ?");
            $query->execute([$id]);
            return $query->fetch();
        } catch (\PDOException $e) {
            $error = new \Controllers\ErrorsController;
            $error->pdoError($e);
            die;
        }
        /**
         * Exemple d'utilisation : 
         *  $id = 3;
         *  return $this->findOne($id);
         */
    }


    /** findBY -> Sélection les enregistrements repondants à un ou plusieurs critères  (READ)
     * @param array $ tableau de critères ['champ1'=>'value1', 'champ2=>value2' etc...]
     * @return array Tableau des enregistrements trouvés  
     */
    protected function findBy(array $params = []): array | false
    {
        try{
            // on appel la methode split pour  transformer le tableau de critère 
            [$liste_champs, $valeurs] = $this->split($params);
            // preparation de la requête
            $query = $this->pdo->prepare("SELECT * 
                                        FROM    $this->table 
                                        WHERE   $liste_champs", $valeurs);
            // execution  de la requête
            $query->execute($valeurs);
            return $query->fetchAll();

        } catch (\PDOException $e) {
            $error = new \Controllers\ErrorsController;
            $error->pdoError($e);
            die;
        }
        /**
         * Exemple d'utilisation : 
         *  $params = ['id' => 28,'lastName' => "BRAVO"];
         *  return $this->findBy($params);
         */
    }


    /** delete -> Suppression d'un enregistrement selon un id  (DELETE)
     * @param int $idValue : valeur de l'id 
     * @return boolean true ou false si problème d'execution 
     */
    protected function delete(int $idValue): bool
    {
        try{
            $query = $this->pdo->prepare("DELETE    
                                        FROM    $this->table 
                                        WHERE   $this->idName = ?");

            return $query->execute([$idValue]);

        } catch (\PDOException $e) {
            $error = new \Controllers\ErrorsController;
            $error->pdoError($e);
            die;
        }

    }
    // ex :  return $this->delete(32);

    /** Création d'un enregistrement à partir d'un objet hydraté (CREATE)
     * @param model : $model Objet hydraté à inserer dans la Db, de la classe Database (par l'extend)
     * @return void
     */
    protected function create(Model $model): void 
    {
        $champs     = []; // colonnes du tableau
        $inter      = [];  // signes ? pour préparer la requête
        $valeurs    = []; // tableau des valeurs à insérer 

        // Boucle sur l'objet pour recupérer les propriétés/valeurs mais on enlève les propriétés "pdo" et "table" 
        foreach ($model as $champ => $valeur) {
            if ($valeur     !== null && $champ != 'pdo' && $champ != 'table' && $champ != 'idName') {
                $champs[]   = $champ;
                $inter[]    = "?";
                $valeurs[]  = $valeur;  // ex: array(2) { [0]=> string(4) "tutu" [1]=> string(4) "lolo" }
            }
        }
        // preparation des éléments à insérer dans la requête
        $table          = $this->table; // nom de la table 
        $liste_champs   = implode(',',  $champs); // ex: string(19) "lastName, firstName"
        $liste_inter    = implode(',', $inter);  // ex:  "?, ?"
        // requête 
        try{
            $query = $this->pdo->prepare('INSERT INTO ' . $table . '(' . $liste_champs . ') values (' . $liste_inter . ')');
            $query->execute($valeurs);
        } catch (\PDOException $e) {
            $error = new \Controllers\ErrorsController;
            $error->pdoError($e);
            die;
        }
        // ex d'utilisation : return $this->create($model); 
    }

    
    /** Mise à jour d'un enregistrement  (UPDATE )
     * @param int $id : id de l'enregistrement à modifier
     * @param Model $model : Objet à modifier, de la classe Database (par l'extend)
     * @return void 
     */
    protected function update(int $id, Model $model): void
    { 
        $champs     = [];
        $valeurs    = [];
        // Boucle pour recupérer les propriétés/valeurs mais on enlève les propriétés "pdo" et "table" 
        // on va réassigner tous les champs avec les valeurs issues de $model
        foreach ($model as $champ => $valeur) {
            if ($valeur !== null && $champ != 'pdo' && $champ != 'table' && $champ != 'idName') {
                $champs[] = "$champ = ?";
                $valeurs[] = $valeur;
            }
            // $champs si 2 champs : array(2) { [0]=> string(12) "lastName = ?" [1]=> string(13) "firstName = ?" } 
        }
        $valeurs[] = $id; // pour transformer un int en array
        // On transforme le tableau "champs" en une chaine de caractères
        $liste_champs = implode(', ', $champs);
        // Requête

        try{
            $query =  $this->pdo->prepare("UPDATE $this->table 
                                        SET $liste_champs  
                                        WHERE $this->idName = ?");

            $query->execute($valeurs);

        } catch (\PDOException $e) {
            $error = new \Controllers\ErrorsController;
            $error->pdoError($e);
            die;
        }
        // ex d'utilisation :  $valID = 33; return $this->update($valID, $model);
    }

    /** Hydratation automatique des données -> Setter automatique 
     * Atention demande que les setters soient nommés de la forme "setXxxx" où Xxxx = nom attribut
     * @param array $datas Tableau associatif des données (respecter l'ordre )
     * @return self Retourne l'objet hydraté
     */
    protected function hydrate(array $datas): Model
    {
        foreach ($datas as $key => $value) {
            // On récupère le nom du setter correspondant à l'attribut.
            $setter = 'set' . ucfirst($key);
            // Si le setter correspondant existe.
            // exemple :($setter, $key, $value) donne :(string(12) "setFirstName" string(9) "firstName" string(4) "pole")
            if (method_exists($this, $setter)) {   // On appelle le setter si on le trouve . 
                $this->$setter($value); // hydraration de la propriété
            }
        }
        return $this;
    }
    /**
     * Utilisation : $this->hydrate($datas) ( voir le README pour plus d'indormations)
     * penser aussi que "$this->create($this->hydrate($datas));" est possible 
     */

}
