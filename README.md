# Compte-rendu

## Sommaire

- Ce que nous avons amélioré et les problématiques rencontrées
- Ce que le projet nous a apporté
- Ce que nous pourrions améliorer

## Ce que nous avons amélioré

Les améliorations sur lesquelles nous nous sommes penchés sont les suivantes :

- Gestionnaire de session + enregistrement de celui-ci en tant que service
- Enregistrement des repositories de nos entités en tant que service
- Messages flash

### Gestionnaire de session

Dans un premier tant rappelons l'utilité d'une session.

Une session est un mécanisme permettant de stocker des informations spécifiques à un utilisateur pendant sa navigation
sur un site web.

Le gestionnaire permettrait donc, par exemple, de faciliter l'accès/l'enregistrement des données enregistrer dans la
session et ainsi permettre aux développeurs d'accéder à ce gestionnaire sur l'entièreté des services.

Dans notre cas, nous avons créé une classe Session permettant de facilement manipuler cette dernière.
Elle est notamment utilisée pour l'enregistrement et la diffusion des messages flash. Cette classe est instanciée dans
notre fichier principal `main.php`. Ainsi, chaque utilisateur possède sa propre session à son arrivée.

Exemple d'utilisation du Session Manager :
```php
// Instanciation
$sessionManager = new SessionManager();

// Stocker une information dans la session
$sessionManager->set('test', 'ceci est un message de test');

// Récupérer une donnée
$test = $sessionManager->get('test');

// Supprimer une donnée
$sessionManager->remove('test');

// Récupérer toute la session
$all = $sessionManager->all();

// Récupérer l'ID de la session
$sessionManager->getId();

// Tester si une valeur existe dans la session
$sessionManager->has('test');

// Détruire la session
$sessionManager->destroy();
```

### Enregistrement des repositories de nos entités en tant que service

L'objectif de cette amélioration était d'enregistrer les repositories de nos entités en tant que service pour les
controllers.

Imaginons que nous créons une entité `user`. (Afin que celle-ci soit identifiée en tant qu'entité, nous avons besoin
d'ajouter l'attribut PHP `Entity`)

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('users')]
class User
{
    #[Id, GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'string', length: 255)]
    private string $name;

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
```

La première problématique que nous avons rencontrée était de savoir comment créer les repositories.

Dans un premier temps, nous pouvons créer la classe `UserRepository`.

```php
<?php

namespace App\Repository;
use Doctrine\ORM\EntityRepository;

class UserRepository
{

}
```

Dans un second temps, dans la documentation de ORM, il nous est indiqué que pour qu'une classe soit identifiée comme
repository, il suffit de l'étendre à la classe `EntityRepository`.

Notre classe ressemble donc à ça :

```php
<?php

namespace App\Repository;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

}
```

Pour continuer, nous avons besoin que notre repository soit référencé à notre classe `User`. Pour faire cela, nous
devons ajouter dans l'attribut PHP 8 `Entity` le paramètre `repositoyClass` à celle-ci :

```php
...
#[Entity(repositoryClass: UserRepository::class)]
#[Table('users')]
class User
{
    ...
}
```

Une fois ceci fait, le repository de notre entité `User` a été créé et le repository est référencé à celle-ci.

La suite de l'objectif était donc d'instancier les repositories en tant que service à notre classe `Routing`.

Cependant, une problématique nous arrête : comment faire pour connaître les repositories créées par le développeur
sans les renseigner à la main ?

Nous avons décidé de partir sur l'idée de créer une classe `Finder` qui permettra de récupérer la liste de toutes les
entités. Ainsi, grâce à des méthodes de la classe `EntityManager`, nous pourront instancier les repositories dans la
classe `Routing`.

La classe `Finder` :

```php
<?php

namespace App\Utils;

class Finder
{
    private function getClass(string $directoryPath, string $namespace)
    {
        $subscribers = [];
        $files = scandir($directoryPath, SCANDIR_SORT_ASCENDING);

        foreach ($files as $file) {
            // Ignore directories and abstract classes.
            if (is_dir($file) || 0 === stripos($file, 'Abstract')) {
                continue;
            }

// Get the name of the file without the suffix.
            $file = explode('.', $file);
            $file = $file[0];

            $subscribers[] = $namespace . $file;
        }
        return $subscribers;
    }

    public function getEntities(){
        return $this->getClass(__DIR__ . '/../Entity/', 'App\\Entity\\');
    }
}
```

L'algorithme qui nous permet de récupérer nos repositories :

```php
$finder = new Finder();
$entities = $finder->getEntities(); // Permet de récupérer la liste de toutes nos entités.

$repos = [];
foreach ($entities as $key => $entity) {
    $repo = $entityManager->getRepository($entity);
    $repos[$repo::class] = $repo;
}
```

Enregistrement des repositories dans le conteneur et mise dans celui-ci dans la classe `Routing` :

```php
$serviceContainer = new Container();
$serviceContainer->set(Environment::class, $twig);
$serviceContainer->set(EntityManager::class, $entityManager);
foreach ($repos as $key => $repo) {
    $serviceContainer->set($key, $repo);
}
// Appeler un routeur pour lui transférer la requête
$router = new Router($serviceContainer);
```

Nous venons de mettre en place le système permettant d'ajouter nos repositories dans notre conteneur.

### Messages Flash
TODO

## Ce que le projet nous a apporté

Loan : ??

Mathis : en ce qui me concerne, ce cours et ce projet, m'ont permis de mieux comprendre comment le framework Symfony
fonctionne, puisque ce projet MVC est très inspiré de son fonctionnement.

## Ce que nous pourrions améliorer

### Gestionnaire de session

Nous pourrons rendre le gestionnaire de session plus complexe en ajoutant des méthodes permettant la gestion des cookies
et requêtes. Nous pourrions également ajouter des méthodes permettant de gérer les sessions en base de données.

### Enregistrement des repositories en tant que service

À ce stade les repositories sont instanciés en tant que service uniquement pour la classe `Router` et les contrôleurs de notre application. Cependant, nous pourrions améliorer le processus en faisant en sorte que ceux-ci soient généralisées à tous les services.

### Messages Flash
