/\*\*

- Utilisation des méthodes de la classe Model :

  -> Le Model "Model" contient les methodes generiques de requêtes à la base de données
  pour les utiliser il faut que les classes extends cette Classe.
  -> Il faut impérativement que toutes les classes contiennent en propriété
  -> le nom de la table et le nom du champ servant d'id
  -> une fonction constructeur pour setter le nom de la table et le nom de la clé qui aura le rôle d'id unique ( par ex id ou id_truc ou idChose )
  -> la liste de tous les champs sous forme de propriétés "protected" ce qui permet l'utilisation de la methode "hydrate" qui permet d'hydrater en une seule fois par un recours automatique à tous les SETTER disponibles.

---

exemple :
class Users extends Model
{
public $table;
publis $id;
public $lastName; (par exemple)
public $firstName; (par exemple)
etc.....

    public function __construct()
    {
      parent::__construct();  // ne pas oublier sinon problème avec instance PDO !
      $this->table = "users";  // nom de la table
      $this->idName = "id"; // nom de la colonne servant d'id dans la table
    }

---

Méthodes disponibles à travers la classe 'MODEL' :

1. Méthode de type CREATE :
   $this->create($model);

2. Méthodes de type READ :

   - $this->findAll();
   - $this->findBy($params);
     Methode à utiliser avec un tableau de paramètres :
     $params = [
     'id' => 28,
     'lastName' => "*BRAVO mon gars! **"
     ];

3. Méthode de type UPDATE :
   $this->update($valID, $model);
   A utiliser en fournissant l'id de l'enregistrement à updater ex: $valID = 33;

4. Méthode de type DELETE :
   $this->delete($valID); avec par ex: $valID = 33;

5. Méthode spéciale : hydratation automatique
   $this->hydrate($datas);
   S'utilise à partir d'un tableau préparé sous le format : propriété -> valeur
   Par exemple :
   $datas = [
            'lastName' => 'Bonaparte',
            'firstName' => 'Napoléon',
        ];
  avec la méthode :  $this->hydrate($datas) les propiétés seront toutes hydratées en même temps si les setter sont trouvés.
   Il est important de déclarer les propriétés dans le bon ordre

Remarque : on peut appmliquer en une seule ligne " $this->create($this->hydrate($datas));" ce qui aura pour conséquence d'inserer en base de donnée un nouvel enregistrement en même temps que l'hydratation.

---

Gestion des erreurs : on dispose d'un model permetant de gérer le texte des messages d'erreur.
Voir dans models/ErrorMessages.php pour mode d'emploi

---

Securisation des formulaires

-> au niveau de la saisie, dans le HTML formulaire d'enregistrement:
Champs nom et premom : entre 2 et 30 caractères - patern : lettres, espaces, tirets et apostrophes
email :  type email (plus de valeur semantique que pattern)
Mot de passe au mini 6 caractères 1 Maj, 1Min, 1 chiffre
Tous les champs sont Required
