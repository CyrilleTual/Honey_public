<?php

namespace Models;

class Products extends Model
{
    protected string $table; // nom de la table -- ne pas toucher
    protected string $idName;  // nom du champ d'identifiant pour les methodes se servant de l'id -- don't touch
    // 
    protected int $id_product;
    protected string $id_category;
    protected string $productName;
    protected string $productRef;
    protected string $teaser;
    protected string $description;
    protected string $infos;
    protected string $picture;
    protected string $status;

    public function __construct()
    {
        parent::__construct();  // ne pas oublier sinon destruction de l'instance PDO !
        $this->table = "products";
        $this->idName = "id_product";
    }


    public function getId_product(): int
    {
        return $this->id_product;
    }

    public function setId_product(int $value)
    {
        $this->id_product = $value;
    }

    public function getId_category(): string
    {
        return $this->id_category;
    }

    public function setId_category(string $value)
    {
        $this->id_category = $value;
    }

    public function getProductRef(): string
    {
        return $this->productRef;
    }

    public function setProductRef(string $value)
    {
        $this->productRef = $value;
    }

    public function getTeaser(): string
    {
        return $this->teaser;
    }

    public function setTeaser(string $value)
    {
        $this->teaser = $value;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $value)
    {
        $this->description = $value;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }

    public function setPicture(string $value)
    {
        $this->picture = $value;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $value)
    {
        $this->status = $value;
    }
    public function getInfos(): string
    {
        return $this->infos;
    }

    public function setInfos(string $value)
    {
        $this->infos = $value;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function setProductName(string $value)
    {
        $this->productName = $value;
    }


    /**
     * trouve tous les enregistrements
     */
    public function findAllCat()
    {
        return $this->findAll();
    }

    /****************************************************************************************
     * Exemple de methode de sélection complexe utilisant une requête préparée 
     * @param string $byColum : liste sous forme de string des clés des colonnes du WHERE
     * @param string $datas : listes des valeurs correspondantes aux clés de $byColumn
     * @param string $order :  ordre du tri 
     * @param int $ limit : nombre maxi d'enregistrements retournés
     * @return array : tableau des enregistrements trouvés 
     */

    public function getProductsByQuery(string $byColumn = '1', string $datas = '1', string $order = " DESC ", int $limit = 500): array
    {

        $sql = 'SELECT  products.id_product,  products.productName, products.productRef, products.teaser, products.description, products.infos,products.picture, products.status,
                        categories.id_category, categories.categoryName
                FROM ' . $this->table . '
                INNER JOIN categories
                ON products.id_category = categories.id_category
                WHERE ' . $byColumn . ' = ? 
                ORDER BY ' . $this->table . '.' . $this->idName . $order . ' 
                LIMIT ' . $limit;
        return $this->findByQuery($sql, [$datas]);
    }

    /******************************************************************************
     * Ajout d'un nouveau produit suite à la validation puis au controle du formulaire
     * @param array $datas => tableau des propriétés / valeurs du noubel user
     * @return : obj de la classe pdo ou false si pb
     */
    public function addNewProduct(array $datas)
    {
        return $this->create($this->hydrate($datas));
        // var_dump($this->create($this->hydrate($datas)));
        // var_dump($datas);
        // die;
    }

    /**
     * selection d'un produit par id
     */

    public function findOneProduct(int $id)
    {
        return $this->findOne($id);
    }


    /****************************************************************
     * Methode composée pour update de produits 
     */

    public function UpdateProduct(int $id_product, array $datas)
    {
        return $this->update($id_product, ($this->hydrate($datas)));
    }

    /**********************************************************************
     * Recherche des produits actifs pour affichage public
     */

    public function getProductsPublic(string $crit1 = '1', string $crit2 = '1', string $data1 = '1', string $data2 = '1', string $order = " DESC ", int $limit = 500): array
    {
       
        $sql = 'SELECT  products.id_product, products.productName, products.productRef, products.teaser, products.description, products.infos, products.picture,
                items.id_item
                FROM ' . $this->table . '
                LEFT JOIN items
                ON products.id_product = items.id_item
                WHERE ' . $crit1 . ' = ? and ' . $crit2 . ' = ?
                ORDER BY ' . $this->table . '.' . $this->idName . $order . ' 
                LIMIT ' . $limit;            
        return $this->findByQuery($sql, [$data1,$data2]);
    }

    /**********************************************
     * Recherche des produits sur requête Ajax
     */
     public function getProductsAjax($search)
     {
        $sql = 'SELECT  products.id_product, products.productName, products.productRef, products.teaser, products.description, products.infos, products.picture,
                items.id_item
                FROM ' . $this->table . '
                LEFT JOIN items
                ON products.id_product = items.id_item
                WHERE products.productName LIKE ? OR products.description LIKE ?
                ORDER BY ' . $this->table . '.' . $this->idName .' DESC
                LIMIT 50';
        return $this->findByQuery($sql, [$search, $search]);
     }


}
