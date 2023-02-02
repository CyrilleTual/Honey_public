<?php

namespace Models;

class Items extends Model
{
    protected string $table; // nom de la table -- ne pas toucher
    protected string $idName;  // nom du champ d'identifiant pour les methodes se servant de l'id -- don't touch
    // 
    protected int $id_item;
    protected string $itemRef;
    protected int $id_product;
    protected string $pack;
    protected float $price;
    protected int $stock;
    protected int $id_vat;
    protected string $status;

    public function __construct()
    {
        parent::__construct();  // ne pas oublier sinon destruction de l'instance PDO !
        $this->table = "items";
        $this->idName = "id_item";
    }

	public function getId_item() : int {
		return $this->id_item;
	}

	public function setId_item(int $value) {
		$this->id_item = $value;
	}

	public function getItemRef() : string {
		return $this->itemRef;
	}

	public function setItemRef(string $value) {
		$this->itemRef = $value;
	}

	public function getId_product() : int {
		return $this->id_product;
	}

	public function setId_product(int $value) {
		$this->id_product = $value;
	}

	public function getPack() : string {
		return $this->pack;
	}

	public function setPack(string $value) {
		$this->pack = $value;
	}

	public function getPrice() : float {
		return $this->price;
	}

	public function setPrice(float $value) {
		$this->price = $value;
	}

	public function getStock() : int {
		return $this->stock;
	}

	public function setStock(int $value) {
		$this->stock = $value;
	}

	public function getId_vat() : int {
		return $this->id_vat;
	}

	public function setId_vat(int $value) {
		$this->id_vat = $value;
	}

	public function getStatus() : string {
		return $this->status;
	}

	public function setStatus(string $value) {
		$this->status = $value;
	}

    /** recherche  des items dispo 
     * @param string $byColum : liste sous forme de string des clés des colonnes du WHERE
     * @param string $datas : listes des valeurs correspondantes aux clés de $byColumn
     * @param string $order :  ordre du tri 
     * @param int $ limit : nombre maxi d'enregistrements retournés
     * @return array : tableau des enregistrements trouvés 
     */
    public function getItemsByQuery(string $byColumn = '1', string $datas = '1', string $order = " DESC ", int $limit = 500): array
    {
        $sql = 'SELECT  items.id_item, items.itemRef, items.pack, items.price, items.stock, items.status, products.productName, categories.categoryName, vat.name
                FROM `items` 
                INNER JOIN products
                ON items.id_product = products.id_product
                INNER JOIN categories
                ON products.id_category = categories.id_category
                INNER JOIN vat
                ON items.id_vat = vat.id_vat
                WHERE ' . $byColumn . ' = ? ORDER BY ' . $this->table . '.' . $this->idName . $order . ' LIMIT ' . $limit;    
        return $this->findByQuery($sql, [$datas]);
    }


	/** Ajout d'un nouvel item suite à la validation puis au controle du formulaire
	 * @param array $datas => tableau des propriétés / valeurs du noubel item
	 */
	public function addNewItem(array $datas) :void
	{
		$this->create($this->hydrate($datas));
	}

	/** trouve un item par son id
	 * @param int $id
	 * @return array : enregistrement trouvé 
	 */

	public function findOneItem(int $id) :array
	{
		return $this->findOne($id);
	}

	/**  Methode composée pour update de items
	 *  @param int $id_item : id de l'item
	 *  @param array $datas : tableau des clés/valeurs de l'item
	 */
	public function updateItem(int $id_item, array $datas)
	{
		$this->update($id_item, ($this->hydrate($datas)));
	}


	/** recherche des items actif d'un produit
	 * @param string $crit1 : premier critère
	 * @param string $crit2 : second critère
	 * @param string $data1 : valeur premier critère
	 * @param string $data2 : valeur second critère
	 * @param string $order : ordre de tri
	 * @param int $limit : nombre de valeurs rerournées
	 * @return array enregistrements trouvés
	 */
	public function getItemsPublic(string $crit1 = '1', string $crit2 = '1', string $data1 = '1', string $data2 = '1', string $order = " DESC ", int $limit = 500): array
	{
		$sql = 'SELECT  items.id_product,items.id_item, items.itemRef, items.pack, items.price
                FROM ' . $this->table . '                
                WHERE ' . $crit1 . ' = ? and ' . $crit2 . ' = ?
                ORDER BY ' . $this->table . '.' . $this->idName . $order . ' 
                LIMIT ' . $limit;
		return $this->findByQuery($sql, [$data1, $data2]);
	}

	/**  Methode pour la requète ajax de mise a jour de prix
	 * @param string $byColumn : critère de selection (where)
	 * @param string $datas : valeur du critère
	 * @param string $order : ordre de tri
	 * @param int $limit : nombre de valeurs rerournées
	 * @return array enregistrement trouvé
	 */
	public function getItemPrice(string $byColumn = '1', string $datas = '1', string $order = " DESC ", int $limit = 500): array
	{
		$sql = 'SELECT  items.price 
                FROM ' . $this->table . '
                WHERE ' . $byColumn . ' = ? 
                ORDER BY ' . $this->table . '.' . $this->idName . $order . ' 
                LIMIT ' . $limit;
		return $this->findOneByQuery($sql, [$datas]);
	}


}