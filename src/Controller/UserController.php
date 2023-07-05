<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use Doctrine\ORM\EntityManager;

class UserController extends AbstractController
{

    #[Route("/user/create", name: "user_create")]
    public function create(EntityManager $em)
    {
        $user = new User();
        $user->setName('Mathis ROME');
        $em->persist($user);
        $em->flush();
        return $this->twig->render('user/create_confirm.html.twig', ['user' => $user]);
    }

    #[Route("/user/list", name: "user_list")]
    public function list(UserRepository $userRepository): string
    {
        $users = $userRepository->findAll();
        return $this->twig->render('user/list.html.twig', [
            'users' => $users
        ]);
    }
}
