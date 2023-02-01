<?php

namespace Models;

class Categories extends Model
{
    protected string $table; // nom de la table 
    protected string $idName;  // nom du champ d'identifiant pour les methodes se servant de l'id
    //
    protected string $categoryName;
    protected string $status;

    public function __construct()
    {
        parent::__construct();  // ne pas oublier sinon destruction de l'instance PDO !
        $this->table = "categories";
        $this->idName = "id_category";
    }


    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $value)
    {
        $this->categoryName = $value;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $value)
    {
        $this->status = $value;
    }


    /****************************************************************************************
     * @param string $byColum : liste sous forme de string des clés des colonnes du WHERE
     * @param string $datas : listes des valeurs correspondantes aux clés de $byColumn
     * @param string $order :  ordre du tri 
     * @param int $ limit : nombre maxi d'enregistrements retournés
     * @return array : tableau des enregistrements trouvés 
     */
    public function getCategoriesByQuery(string $byColumn = '1', string $datas = '1', string $order = " DESC ", int $limit = 500): array |false
    {
        $sql = 'SELECT  *
                FROM ' . $this->table . '
                WHERE ' . $byColumn . ' = ? ORDER BY ' . $this->table . '.' . $this->idName . $order . ' LIMIT ' . $limit;
        return $this->findByQuery($sql, [$datas]);
    }

    /**
     * recheche d'une seule catégorie 
     */
    public function getOneCategoriesByQuery(string $byColumn = '1', string $datas = '1', string $order = " DESC ", int $limit = 500): array |false
    {
        $sql = 'SELECT  *
                FROM ' . $this->table . '
                WHERE ' . $byColumn . ' = ? ORDER BY ' . $this->table . '.' . $this->idName . $order . ' LIMIT ' . $limit;
        return $this->findOneByQuery($sql, [$datas]);
    }







}
