<?php

namespace App\Controller;

use App\Notification\FlashManager;
use Twig\Environment;

abstract class AbstractController
{
    public function __construct(
        protected Environment  $twig,
        protected FlashManager $flash,
    )
    {
    }
}
