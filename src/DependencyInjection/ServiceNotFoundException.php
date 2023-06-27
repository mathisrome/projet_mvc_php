<?php

namespace App\DependencyInjection;

use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundException extends \InvalidArgumentException implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        $this->message = sprintf("Le service %s n'existe pas", $id);
    }
}