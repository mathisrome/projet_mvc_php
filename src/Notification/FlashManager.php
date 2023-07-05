<?php

namespace App\Notification;

class FlashManager
{
    public function create(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = ['message' => $message];
    }
}