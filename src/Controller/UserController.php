<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Twig\Environment;

class UserController extends AbstractController
{
    public function create(EntityManager $em)
    {
        $user = new User();
        $user->setName('Mathis ROME');
        $em->persist($user);
        $em->flush();
        return $this->twig->render('user/create_confirm.html.twig', ['user' => $user]);
    }
}
