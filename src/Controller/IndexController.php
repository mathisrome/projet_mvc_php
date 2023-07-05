<?php

namespace App\Controller;

class IndexController extends AbstractController
{
    public function home(): string
    {
        $this->flash->create('success', 'Welcome to the homepage');
        return $this->twig->render('index.html.twig');
    }
}
