<?php

namespace Models;

class CarouselPics extends Model
{
    protected string $table; // nom de la table -- ne pas toucher
    protected string $idName;  // nom du champ d'identifiant pour les methodes se servant de l'id -- don't touch
    // 
    protected int $id_carousel;
    protected string $description;
    protected string $picture;
    protected string $status;

    public function __construct()
    {
        parent::__construct();  // ne pas oublier sinon destruction de l'instance PDO !
        $this->table = "carousel";
        $this->idName = "id_carousel";
    }

	public function getId_carousel() {
		return $this->id_carousel;
	}

	public function setId_carousel($value) {
		$this->id_carousel = $value;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($value) {
		$this->description = $value;
	}

	public function getPicture() : string {
		return $this->picture;
	}

	public function setPicture(string $value) {
		$this->picture = $value;
	}

	public function getStatus() : string {
		return $this->status;
	}

	public function setStatus(string $value) {
		$this->status = $value;
	}


	/** Ajout d'une nouvelle photo dans le carroussel suite à la validation puis au controle du formulaire
	 * @param array $datas => tableau des propriétés / valeurs  
	 */
	public function addNewPic(array $datas) 
	{
		$this->create($this->hydrate($datas));
	}


	/** recupération des photos avec eventuel critère
	 * @param string $byColumn : critère de selection (where)
	 * @param string $datas : valeur du critère
	 * @param string $order : ordre de tri
	 * @param int $limit : nombre de valeurs retournées
	 * @return array enregistrement trouvé 
	 */
	public function getPicsByQuery(string $byColumn = '1', string $datas = '1', string $order = " DESC ", int $limit = 500): array | false
	{
		$sql = 'SELECT  
                        *
                FROM ' . $this->table . '
                WHERE ' . $byColumn . ' = ? 
                ORDER BY ' . $this->table . '.' . $this->idName . $order . ' 
                LIMIT ' . $limit;
		return $this->findByQuery($sql, [$datas]);
	}

	/** selection d'une photo par id
	 * @param int $id : id de la photo cherchée
	 * @return array : l'enregistrement trouvé 
	 */

	public function findOnePic(int $id) :array | false
	{
		return $this->findOne($id);
	}

	/**  Methode pour update de photos
	 * @param int $id_product : id du produit
	 * @param array $datas : tableau des proriétés/valeurs de l'objet
	 */
	public function updatePic(int $id_product, array $datas) :void
	{
		 $this->update($id_product, ($this->hydrate($datas)));
	}


	/** Effacement d'un enregistrement dans la Base
	 * @param int $id : id du produit
	 * @return bool : 1 si 1 produit deleté
	 */

	public function delOnePic(int $id) :bool
	{
		return $this->delete($id);
	}



    
}