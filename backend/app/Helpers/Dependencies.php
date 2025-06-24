<?php

namespace App\Helpers;

use RuntimeException;

final class Dependencies
{
    private array $dependencies = [];
    private static ?Dependencies $instance = null;

    private function __construct()
    {
        $this->registre();
    }

    private function registre(): void
    {
        $this->dependencies = [
            // adicione aqui as dependencias
        ];
    }

    public function __clone(): void
    {
        throw new RuntimeException('Cloning is not allowed.');
    }

    public function __wakeup(): void
    {
        throw new RuntimeException('Unserialization is not allowed.');
    }

    public static function getInstance(): Dependencies
    {
        if (self::$instance === null) {
            self::$instance = new Dependencies();
        }
        return self::$instance;
    }

    public function __set(string $name, $value): void
    {
        $this->dependencies[$name] = $value;
    }

    public static function set(string $name, $value): void
    {
        self::getInstance()->dependencies[$name] = $value;
    }

    public static function get(): array
    {
        return self::getInstance()->dependencies;
    }
}