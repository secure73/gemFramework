<?php

namespace Gemvc\CLI;

abstract class Command
{
    protected array $args;
    protected $options;

    public function __construct(array $args = [], array $options = [])
    {
        $this->args = $args;
        $this->options = $options;
    }

    abstract public function execute();

    private function supportsAnsiColors(): bool
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            // On Windows, check if running in a terminal that supports ANSI
            return (
                false !== getenv('ANSICON') ||
                'ON' === getenv('ConEmuANSI') ||
                'xterm' === getenv('TERM') ||
                'Hyper' === getenv('TERM_PROGRAM')
            );
        }
        return true;
    }

    protected function write(string $message, string $color = 'white'): void
    {
        $colors = [
            'white' => "\033[37m",
            'green' => "\033[32m",
            'red' => "\033[31m",
            'yellow' => "\033[33m",
            'blue' => "\033[34m"
        ];

        if ($this->supportsAnsiColors()) {
            echo $colors[$color] . $message . "\033[0m";
        } else {
            echo $message;
        }
    }

    protected function error(string $message): void
    {
        if ($this->supportsAnsiColors()) {
            echo "\033[31m{$message}\033[0m\n";
        } else {
            echo "Error: {$message}\n";
        }
        exit(1);
    }

    protected function success(string $message): void
    {
        if ($this->supportsAnsiColors()) {
            echo "\033[32m{$message}\033[0m\n";
        } else {
            echo "Success: {$message}\n";
        }
        exit(0);
    }

    protected function info(string $message): void
    {
        if ($this->supportsAnsiColors()) {
            echo "\033[32m{$message}\033[0m\n";
        } else {
            echo "Info: {$message}\n";
        }
    }

    protected function warning(string $message): void
    {
        if ($this->supportsAnsiColors()) {
            echo "\033[33m{$message}\033[0m\n";
        } else {
            echo "Warning: {$message}\n";
        }
    }
} 