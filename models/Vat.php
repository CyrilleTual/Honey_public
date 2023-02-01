<?php

namespace Models;

class Vat extends Model
{
    protected string $table; // nom de la table -- ne pas toucher
    protected string $idName;  // nom du champ d'identifiant pour les methodes se servant de l'id -- don't touch
    // 
    protected int $id_vat;
    protected string $rate;
    protected int $name;
    protected string $comment;

    public function __construct()
    {
        parent::__construct();  // ne pas oublier sinon destruction de l'instance PDO !
        $this->table = "vat";
        $this->idName = "id_vat";
    }

	public function getId_vat() {
		return $this->id_vat;
	}

	public function setId_vat($value) {
		$this->id_vat = $value;
	}

	public function getRate() {
		return $this->rate;
	}

	public function setRate($value) {
		$this->rate = $value;
	}


	public function getName() : int {
		return $this->name;
	}

	public function setName(int $value) {
		$this->name = $value;
	}

	public function getComment() : string {
		return $this->comment;
	}

	public function setComment(string $value) {
		$this->comment = $value;
	}

	/** Recupération des tva actives 
	 * @param string $byColum : liste sous forme de string des clés des colonnes du WHERE
	 * @param string $datas : listes des valeurs correspondantes aux clés de $byColumn
	 * @param string $order :  ordre du tri 
	 * @param int $ limit : nombre maxi d'enregistrements retournés
	 * @return array : tableau des enregistrements trouvés 
	 */

	public function getVatByQuery(string $byColumn = '1', string $datas = '1', string $order = " ASC ", int $limit = 500): array
	{
		$sql = 'SELECT  *
                FROM ' . $this->table . '
                WHERE ' . $byColumn . ' = ? 
                ORDER BY ' . $this->table . '.' . $this->idName . $order . ' 
                LIMIT ' . $limit;
		return $this->findByQuery($sql, [$datas]);
	}
}