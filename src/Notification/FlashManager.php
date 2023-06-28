<?php

namespace App\Notification;

use FlashType;

class FlashManager
{
    /**
     * Create a flash message
     *
     * @param FlashType $type
     * @param string $message
     * @return void
     */
    public function create(FlashType $type, string $message): void
    {
        $_SESSION['flash'][$type->value][] = ['message' => $message];
    }
}