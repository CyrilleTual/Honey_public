Projet site vitrine de Vincent Petit - Apiculteur -

Il s'agit dans un premier temps uniquement d'un site vitrine pour présenter les produits et accroître 
L’aspect "professionnel" de Vincent Petit.
La distribution des produits est assurée par un réseau de magasins partenaires.
 MAIS Vincent Petit souhaite que l'on  puisse éventuellement faire évoluer vers des fonctionnalités de site marchand.
Le projet a été mené dans ce sens. 

Site en fonction : https://vincentpetit.000webhostapp.com/index.php

Attention : lors de l'accès administrateur il y aura un message avertissant des dernières erreurs "graves"
Ceci est uniquement pour montrer cette fonctionnalité de log des erreurs, vous pouvez vider ce fichier par 
Administration -> Erreurs -> Supprimer le fichier de log 


files to set in a config folder :

config.php :
---
```php
<?php

declare(strict_types=1);

// pour mail sur erreur grave ( page errorsWebSite.phtml)
const ADMIN_ADDRESS = '************************';
const ADMIN_ID = '************';

?>
```


database_dist.php 
---
```php
<?php

// variables de connexion à adapter selon la configuration 
// utilisées par Models/Model 
return [
  'host'     => 'www_mysql_projet',
  'port'     => '3306',
  'db_utf'   => 'utf8',
  'dbname'   => 'honey',
  'username' => '***',
  'password' => '********'
];

?>
````



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
 
2. Méthodes de type READ : 

3. Méthode de type UPDATE :
  
4. Méthode de type DELETE :
  

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

Gestion des erreurs : Les erreurs "graves" font l'objet d'une journalisation par un fichier logErrors.log
Si il a y des erreurs, lors de sa connexion l'administrateur est prévenu
Il peut effacer directement le fichier de log
Ceci concerne les erreurs non lièes à l'utilisateur : 
- problèmes d'accès à la Db
- problèmes de requètes SQL (tables indisponibles ...)
- problèmes lors des requètes AJAX

---


