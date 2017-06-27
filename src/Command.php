<?php declare(strict_types = 1);

namespace DaveRandom\ProgramExecutor;

class Command
{
    private $command;
    private $args;
    private $commandLine;

    private static function buildCommandLine(string $command, array $args)
    {
        $command = \escapeshellarg($command);

        foreach ($args ?? [] as $arg) {
            $arg = \trim((string)$arg);

            if ($arg !== null) {
                $command .= ' ' . \escapeshellarg($arg);
            }
        }

        return $command;
    }

    public function __construct(string $command, array $args = null)
    {
        $this->command = $command;
        $this->args = \array_map('strval', $args ?? []);

        $this->commandLine = self::buildCommandLine($command, $this->args);
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getCommandLine(): string
    {
        return $this->commandLine;
    }

    public function __toString()
    {
        return $this->commandLine;
    }
}
