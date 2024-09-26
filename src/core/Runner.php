<?php
namespace Gemvc\Core;

class GemvcRunner
{
    /**
     * @var array<mixed> $commands
     */
    private $commands = [];

    public function __construct()
    {
        $this->registerDefaultCommands();
    }

    private function registerDefaultCommands():void
    {
        $this->commands['Migrate'] = [$this, 'handleMigrate'];
    }

    public function run($argv):void
    {
        if (count($argv) < 3) {
            echo "Usage: Gemvc <Command> <ClassName>\n";
            exit(1);
        }

        $command = $argv[1];
        $className = $argv[2];

        if (!isset($this->commands[$command])) {
            echo "Invalid command '$command'.\n";
            $this->printAvailableCommands();
            exit(1);
        }

        call_user_func($this->commands[$command], $className);
    }
    /**
     * Summary of handleMigrate
     * @param string $className
     * @return void
     */
    private function handleMigrate($className):void
    {
        if (!class_exists($className)) {
            echo "Class '$className' not found.\n";
            exit(1);
        }

        // Create instance of the class
        $object = new $className();

        // Generate the table
        $tableGenerator = new TableGenerator();
        $result = $tableGenerator->createTableFromObject($object);

        if ($result === false) {
            echo "The provided object is not a valid table instance.";
        }
    }

    private function printAvailableCommands():void
    {
        echo "Available commands:\n";
        foreach (array_keys($this->commands) as $command) {
            echo "  - $command\n";
        }
    }
}
?>
