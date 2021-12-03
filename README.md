# KZU
## Le code
Un framework étant un outil de partage de code, **il est dans le but de Kzu de rendre de manière simple et disponible des bouts de code utile à tout type de projet.**

**Kzu** est un programme facilitant la création d'application web et console. Le principe de Kzu réside dans l'utilisation des [traits](https://www.php.net/manual/fr/language.oop5.traits.php) et [les fonctions statiques](https://www.php.net/manual/fr/language.oop5.static.php) ainsi qu'une nouvelle façon de gérer les fichiers de configurations et de bases de données. 

En effet, comparé aux Class utilisés dans la plus part des projets, les **Traits résout le problème de duplication des méthodes**, donc ils évitent des incoréhence dans l'organisation du code lors de l'utilisation de Class classique.
```php
<?php
Trait Temperature {
	public $temperature;
    public function getChaleur() {
    	if ($this->temperature < 5):
        	return "Froid";
        elseif ($this->temperature > 5):
        	return "Chaud";
        endif;
	}
}

Class Dessert { 
	public function getName() {
		return $this->name;
	}
}

Class Fraise extends Dessert { 
	use Temperature;
	public $name = "Fraise";
}
Class Chocolat extends Dessert { 
	use Temperature;
	public $name = "Chocolat";
}

$dessert = new Fraise();
$dessert->temperature = "4";
echo $dessert->getChaleur(); # Froid

$dessert = new Chocolat();
$dessert->temperature = "28";
echo $dessert->getChaleur(); # Chaud
```
Un Trait ne pouvant être instancié, ceux-ci sont alors **optimisés grâce aux fonctions statiques** le plus souvent possible afin d'avoir des appelles rapides des valeurs / méthodes demandés. Bien sûr, **un trait peu utiliser un ou plusieurs autres traits**.

**La conception d'application en est simplifié par sa flexibilité.** Cela étant dit, rien ne vous empèches après de construire vos Class, tout en utilisant les méthodes statiques des Traits de Kzu.
```bash
composer create-project kzu/kzu project_name
```
### Configuration
Vous pouvez retrouver l'enssemble des configuration dans le dossier `./config`, celles-ci sont automatiquement chargées lors de l'éxécution du code et accessible via le trait `Kzu\Config`. Les fichiers de configurations sont interprétées en [yaml](https://yaml.org/), il faudra donc respecter la synthaxe et les tabulations.
```bash
echo "test_config: true" > ./config/test.yaml
```
```php
<?php
use Kzu\Config;
$env = Config::get('test', 'test_config'); # return true
```
### Administration 
Kzu intègre un module d'administration via une interface graphique, facilitant la gestion de vos configurations et bases de données.

<img src="https://api.cloudfile.tech/show/60b50ca5382f1?apikey=KZtaM8HhzNrRU1vPTB9ExGWyJ2fLC6dw" class="img-">

## Composants
Les composants de Kzu peuvent être utilisés manière indépendant du framework dans n'importe quel projet.

<img src="https://api.cloudfile.tech/show/60b516dbae147?apikey=Ke9NctQ7VB0RDqJZu14jrCzwlXoibFYP" class="img-fluid">
<hr>

### Security 
Le module de sécurité vous offres une technique de chiffrement et de déchiffrement de vos fichiers ou chaîne de caractères.
Ce module est basé sur la librairie [phpseclib](https://github.com/phpseclib/phpseclib).
```bash
composer require kzu/security
```
```php
<?php
use Kzu\Security\Crypto;
$encrypted = Crypto::encrypt("Ceci est un test d'encryption", 'passphrase');
$decrypted = Crypto::decrypt($encrypted, 'passphrase');
```
### Filesystem 
Le filesystem reste à la base de toute application logiciel, c'est pourquoi il est au centre du projet Kzu. Que ça soit la lecture ou l'écriture de fichier, le module ce doit-être performant et sécurisé.
```bash
composer require kzu/filesystem
```
```php
<?php
use Kzu\Filesystem\Filesystem;

Filesystem::mkdir($directory = './config');
$files = Filesystem::find($directory, $extensions = ['yaml', 'yml']);
$content = Filesystem::read($file = './config/test.yaml');
$write = Filesystem::write($file, $content = "config_test: true", $encrypted = false);
Filesystem::delete('./var/cache');
```
### Normalizer 
Le normalizer est le module dédié à la gestion des chaînes de caractères en ce qui concerne la conversion et leurs visibilités dans tout type de situation. Il s'occupera aussi bien de la slugification d'un ensemble de mots, comme de la formation d'un document yaml provenant d'un tableau de valeurs.
```bash
composer require kzu/normalizer
```
#### Text
```php
<?php
use Kzu\Normalizer\Text;
$slugify = Text::slugify('Mon titre'); # return 'mon-titre'
```
#### Table
```php
<?php
use Kzu\Normalizer\Table;
$array = ['user' => [
      'id' => 1, 
      'roles' => ['admin' => true, 'user' => true]
	]
];
$array_one_line = Table::arrayOneLine($array); # return ['user.id' => 1, 'user.roles.admin' => true, 'user.roles.user' => true];
$all_keys = Table::getAllKeys($array); # return ['user', 'id', 'roles'];
```
#### Yaml
```php
<?php
use Kzu\Normalizer\Yaml;
$yaml = Yaml::dump(['title' => 'Ceci est un titre']); # return Yaml
$array = Yaml::parse($yaml); # return ['title' => 'Ceci est un titre'];
$yaml_file = Yaml::parseFile('./config/text.yaml'); # return Yaml
```
### Database 
Le module database s'occupe de tout ce qui est lecture, écriture et récherche dans un ensemble de données utilie à l'application. les données sont inscrites dans un document `yaml` pouvant être sécurisé via un chiffrement SHA-250 géré de manière natif.
```bash
composer require kzu/database
```
```php
<?php
use Kzu\Database\Database;
use Kzu\Database\DatabaseQuery;

Database::create('database_name', $model = ['id', 'title', 'online'], $encrypted = true);
DatabaseQuery::insert('database_name',[
	0 => ['id' => 1, 'title' => 'test 1', 'online' => true],
	1 => ['id' => 2, 'title' => 'test 2', 'online' => false],
	2 => ['id' => 3, 'title' => 'test 3', 'online' => true]
]);

$tests = DatabaseQuery::findBy('database_name', ['online' => true]);
$test_1 = DatabaseQuery::findOneBy('database_name', ['id' => 1]);
```
### Storage 
Storage est dépendant des modules de base de données et de filesystem. Ce module propose aussi le composant de gestion des sessions PHP.
```bash
composer require kzu/storage
```
```php
<?php
use Kzu\Storage\Session;

Session::set('username', 'MarquandT');
$username = Session::get('username'); # return 'MarquandT'
```
### Http 
Http fournie les outils nécessaires aux requêtes entrantes et réponses de vos applications.
```bash
composer require kzu/http
```
#### Resquest
#### Response
### Web 
Le composant web implémente toutes les librairies utiles à la création d'applications web (Http, Storage, Database, Normalizer, Filesystem, Security).
```bash
composer require kzu/web
```
#### Flash
En utilisant les sessions de PHP, Flash implémente une logique de messages temporaires, utile lors de la validation de formulaire par exemple.
```php
<?php
use Kzu\Web\Flash;
Flash::add('success', 'Librairie bien prise en compte');
$flash = Flash::get('success'); # return [0 => 'Librairie bien prise en compte']
```
#### Route
Bien sûr, qu'est-ce que serait un site sans sa gestion de routes ?
```php
<?php
use Kzu\Web\Route;
Route::$routes = [
	'user_edit' => [
    	'path' => '/user/edit/{user_id}',
        'controller' => 'App\Controller\User::edit'
	]
];
$current_route = Route::getRoute();
$generate_route_path = Route::getRoutePath('user_edit', ['user_id' => '15b75756c');
```
#### Twig
Pour finir nous avons **le moteur de rendu** ce propulsant avec [Twig](https://twig.symfony.com/).
