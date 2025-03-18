<?php

namespace App\CLI;

abstract class Command
{
    protected $args;
    protected $options;

    public function __construct(array $args = [], array $options = [])
    {
        $this->args = $args;
        $this->options = $options;
    }

    abstract public function execute();

    protected function write(string $message, string $color = 'white'): void
    {
        $colors = [
            'white' => "\033[37m",
            'green' => "\033[32m",
            'red' => "\033[31m",
            'yellow' => "\033[33m",
            'blue' => "\033[34m"
        ];

        echo $colors[$color] . $message . "\033[0m";
    }

    protected function error(string $message): void
    {
        $this->write("Error: " . $message, 'red');
        exit(1);
    }

    protected function success(string $message): void
    {
        $this->write($message, 'green');
    }

    protected function info(string $message): void
    {
        $this->write($message, 'blue');
    }

    protected function warning(string $message): void
    {
        $this->write($message, 'yellow');
    }
} 