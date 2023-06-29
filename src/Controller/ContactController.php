<?php

namespace App\Controller;

use App\Routing\Attribute\Route;

class ContactController
{
    #[Route('/contact', 'contact_page')]
  public function contact()
  {
    echo "Page de contact";
  }
}
