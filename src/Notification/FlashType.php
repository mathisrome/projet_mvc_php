<?php

namespace App\Notification;
enum FlashType: string {
    case SUCCESS = 'success';
    case INFO = 'info';
    case WARNING = 'warning';
    case ERROR = 'error';
}
