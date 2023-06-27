<?php

namespace App\Notification;

class FlashManager
{
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';

    /**
     * Create a flash message
     *
     * @param string $message
     * @param string $type
     * @return void
     */
    public function create(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = ['message' => $message];
    }
}