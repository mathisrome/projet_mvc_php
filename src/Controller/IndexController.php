<?php

namespace App\Controller;


use App\Notification\FlashType;
use App\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: "homepage")]
    public function home(): string
    {
        $this->flash->create('success', 'Welcome to the homepage');
        return $this->twig->render('index.html.twig');
    }
}
