<?php

namespace App\Session;

use Exception;

class SessionManager
{
    protected $sessionId;

    /**
     * SessionManager constructor.
     */
    public function __construct()
    {
        if (!isset($_SESSION)) {
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

    public function set($key, $value): static
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public function get($key): mixed
    {
        if (!$this->has($key)) {
            throw new Exception("{$key} not found in session");
        }
        return $_SESSION[$key];
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * @param $key
     * @return $this
     */
    public function remove($key): static
    {
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