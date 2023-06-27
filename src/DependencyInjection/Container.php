<?php

namespace App\DependencyInjection;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{

    private array $services = [];

    public function set(string $id, $instance): self
    {
        if ($this->has($id)){
            throw new \LogicException(sprintf('Le service %s est déjà enregistré', $id));
        }
        $this->services[$id] = $instance;
        return $this;
    }

    /**
     * Gets a service from a given service ID
     * @param string $id
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function get(string $id)
    {
        if (!$this->has($id)){
            throw new ServiceNotFoundException($id);
        }
        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}