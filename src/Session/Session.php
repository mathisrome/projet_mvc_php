<?php

namespace App\Session;

use Exception;

class Session
{
    protected $sessionId;

    /**
     * Session constructor.
     */
    public function __construct()
    {
        if(!isset($_SESSION)) {
            session_start();
        }

        $this->sessionId = session_id();
    }

    public function all(): array
    {
        return $_SESSION;
    }

    /**
     * @return false|string
     */
    public function getId(): bool|string
    {
        return $this->sessionId;
    }

    /**
     * @throws Exception
     */
    public function set($key, $value): static
    {
        if (!isset($key)) {
            throw new Exception("Session key cannot be empty", 1);
        }

        if (is_array($key)) {
            foreach ($key as $sessionKey => $sessionValue) {
                $_SESSION[$sessionKey] = $sessionValue;
            }
        } else {
            $_SESSION[$key] = $value;
        }

        return $this;
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public function get($key): mixed
    {
        if (!isset($key)) {
            throw new Exception("Session key cannot be empty", 1);
        }

        return $_SESSION[$key];
    }

    /**
     * @param $key
     * @return bool
     * @throws Exception
     */
    public function has($key): bool
    {
        if (!isset($key)) {
            throw new Exception("Session key cannot be empty", 1);
        }

        return isset($_SESSION[$key]);
    }

    /**
     * @param $key
     * @return $this
     * @throws Exception
     */
    public function remove($key): static
    {
        if (!isset($key)) {
            throw new Exception("Session key cannot be empty", 1);
        }

        unset($_SESSION[$key]);

        return $this;
    }

    /**
     * @return bool
     */
    public function destroy(): bool
    {
        return session_destroy();
    }
}