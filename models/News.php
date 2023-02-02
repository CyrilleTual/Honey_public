<?php

namespace Models;

class News extends Model
{
    protected string $table; // nom de la table -- ne pas toucher
    protected string $idName;  // nom du champ d'identifiant pour les methodes se servant de l'id -- don't touch
    // 
    protected int $id_news;
    protected string $title;
    protected string $text;
    protected string $picture;
    protected string $status;
    protected string $date;

    public function __construct()
    {
        parent::__construct();  // ne pas oublier sinon destruction de l'instance PDO !
        $this->table = "news";
        $this->idName = "id_news";
    }

    public function setTitle(string $value)
    {
        $this->title = $value;
    }

    public function setText(string $value)
    {
        $this->text = $value;
    }

    public function setPicture(string $value)
    {
        $this->picture = $value;
    }

    public function setStatus(string $value)
    {
        $this->status = $value;
    }

    public function setDate(string $value)
    {
        $this->date = $value;
    }

    /** recupération des news avec eventuel critère
     * @param string $byColumn : critère de selection (where)
     * @param string $datas : valeur du critère
     * @param string $order : ordre de tri
     * @param int $limit : nombre de valeurs retournées
     * @return array enregistrement trouvé
     */
	public function getNewsByQuery(string $byColumn = '1', string $datas = '1', string $order = " DESC ", int $limit = 500): array
	{
		$sql = 'SELECT  
                        *
                FROM ' . $this->table . '
                WHERE ' . $byColumn . ' = ? 
                ORDER BY ' . $this->table . '.' . $this->idName . $order . ' 
                LIMIT ' . $limit;
		return $this->findByQuery($sql, [$datas]);
	}

    /** selection d'une news par id
     * @param int $id : id de la news cherchée
     * @return array : news trouvée
	 */
	public function findOneNews(int $id)
	{
		return $this->findOne($id);
	}

    /**  Ajout d'une nouvelle news suite à la validation puis au controle du formulaire
     * @param array $datas => tableau des propriétés / valeurs  
     */
    public function addNewNews(array $datas): void
    {
        $this->create($this->hydrate($datas));
    }

    /** Effacement d'un enregistrement dans la Base
     * @param int $id: id de la news à effacer
     * @return boolean : true ou false si problème d'execution
     */
    public function delOneNews(int $id) :bool
    {
        return ($this->delete($id));
    }

    /** Methode composée pour update 
     * @param int $id_product : id du produit 
     * @param array $datas : tableau des propriétés / valeurs  
     */
	public function updateNews(int $id_product, array $datas): void
	{
		$this->update($id_product, ($this->hydrate($datas)));
	}
}