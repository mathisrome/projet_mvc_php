# Compte-rendu

## Sommaire

- Ce que nous avons amélioré et les problématiques rencontrées
- Ce que le projet nous a apporté
- Ce que nous pourrions améliorer

## Ce que nous avons amélioré

Les améliorations sur lesquelles nous nous sommes penchés sont les suivantes : 

- Gestionnaire de session + enregistrement de celui-ci en tant que service
- Enregistrement des repositories de nos entités en tant que service

### Gestionnaire de session

Dans un premier tant rappelons l'utilité d'une session. 

Une session est un mécanisme permmettant de stocker des informations spécifiques à un utilisateur pendant sa navigation sur un site web.

Le gestionnaire permetterai donc, par exemple, de faciliter l'accès/l'enregistrement des donneés enregistrer dans la session et ainsi permettre aux développeurs d'accéder à ce gestionnaire sur l'entièreté des servcies.

... Écrire la suite car pas d'idée

### Enregistrement des repositories de nos entités en tant que service

L'objectif de cette amélioration était d'enregistrer les repositories de nos entités en tant que service pour les contrôleurs.

Imaginons que nous créons une entité `user`. (Afin que celle-ci soit identifier en tant qu'entité nous avons besoin d'ajouter l'attribut PHP `Entity`)

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

Dans un premier temps, nous pouvons donc créer la classe `UserRepository`.

```php
<?php

namespace App\Repository;
use Doctrine\ORM\EntityRepository;

class UserRepository
{

}
```

Dans un second temps, dans la documentation de ORM, il nous est indiqué que pour qu'une classe soit identifiée comme repository, il suffit de l'étendre à la classe `EntityRepository`.  

Notre classe ressemble donc maintenant à ça : 

```php
<?php

namespace App\Repository;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

}
```

Troisièmement nous avons besoin que notre repository soit référencé à notre classe `User` pour faire cela, nous devons ajouter dans l'attribut PHP 8 `Entity` le paramètre `repositoyClass` à celle-ci.

```php
...
#[Entity(repositoryClass: UserRepository::class)]
#[Table('users')]
class User
{
    ...
}
```

Une fois, ceci fait, ça y est le repository de notre entité `User` a été créer et le repository est référencé à celle-ci.

La suite de l'objectif était donc d'instancier les repositories en tant que service à notre classe `Routing`.

Cependant, une problématique nous arrête : comment faire pour connaître les repositories créer par le développeur sans les renseigner à la main ?

Nous avons décidé de partir sur l'idée de créer une classe `Finder` qui permettra de récupérer la liste de toutes les entités et ainsi grâce à des méthodes de la classe `EntityManager` d'instancier les repositories dans la classe `Routing`

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

Enregistrement des repositories dans le container et mise dans celui-ci dans la classe `Routing` :

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

Et voilà nous avons mis en place le système permettant d'ajouter nos repositories dans notre container.

## Ce que le projet nous a apporté

Loan : ??

Mathis : en ce qui me concerne, ce cours et ce projet, m'a permis de mieux comprendre comment le framework Symfony fonctionne, puisque ce projet MVC est très inspiré de son fonctionnement.

## Ce que nous pourrions améliorer

### Gestionnaire de session

Concernant les gestionnaire de session...

### Enregistrement des repositories en tant que service

À ce stade les repositories sont instanciés en tant que service uniquement pour la classe `Router` et les contrôleurs de notre application. Cependant, nous pourrions améliorer le processus en faisant en sorte que ceux-ci soient généralisées à tous les services.
