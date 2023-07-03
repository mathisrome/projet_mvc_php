# Compte-rendu

## Sommaire

- Ce que nous avons amélioré et les problématiques rencontrées
- Ce que le projet nous a apporté
- Ce que nous pourrions améliorer

## Ce que nous avons amélioré

Les améliorations sur lesquels nous nous sommes penchés sont les suivantes : 

- Gestionnaire de session + enregistrement de celui-ci en tant que service
- Enregistrement des repositories de nos entités en tant que service

### Gestionnaire de session

Dans un premier tant rappelons l'utilité d'une session. 

Une session est un mécanisme permmettant de stocker des informations spécifiques à un utilisateur pendant sa navigation sur un site web.

Le gestionnaire permetterai donc, par exemple, de faciliter l'accès/l'enregistrement des donneés enregistrer dans la session et ainsi permettre aux développeurs d'accéder à ce gestionnaire sur l'entièreté des servcies.

... Écrire la suite car pas d'idée

### Enregistrement des repositories de nos entités en tant que service

L'objectif de cette amélioration était d'enregistrer les repositories de nos entités en tant que service pour les controlleurs.

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

La première problématique que nous avons rencontré était de savoir comment créer les repositories.

Dans un premier temps nous pouvons donc créer la classe `UserRepository`.

```php
<?php

namespace App\Repository;
use Doctrine\ORM\EntityRepository;

class UserRepository
{

}
```

Dans un second temps, dans la documentation de ORM, il nous ai indiqué que pour qu'une classe soit identifié comme repository, il suffit de l'étendre à la classe `EntityRepository`.  

Notre classe ressemble donc maintenant à ça : 

```php
<?php

namespace App\Repository;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

}
```

Troisièmement nous avons besoin que notre repository soit référencé à notre classe `User` pour faire cela, nous devons ajouter dans l'attribut PHP 8 `Entity` le paramèrte `repositoyClass` à celle-ci.

```php
...
#[Entity(repositoryClass: UserRepository::class)]
#[Table('users')]
class User
{
    ...
}
```

Une fois ceci fait, ça y est le repository de notre entité `User` a été créer et le repository est référencé à celle-ci.

La suite de l'objectif était donc d'instancier les repositories en tant que service à notre classe `Routing`.

Cependant une problématique nous arrête : comment faire pour connaître les repositories créer par le développeur sans les renseigner à la main ?

Nous avons décider de partir sur l'idée de créer 