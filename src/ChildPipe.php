<?php declare(strict_types = 1);

namespace DaveRandom\ProgramExecutor;

abstract class ChildPipe implements Closable
{
    private $id;
    private $pipe;

    protected function __construct($pipe)
    {
        if (!\is_resource($pipe)) {
            throw new \LogicException("Invalid pipe");
        }

        $this->id = (int)$pipe;
        $this->pipe = $pipe;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPipe()
    {
        return $this->pipe;
    }

    public function isOpen(): bool
    {
        return $this->pipe !== null;
    }

    public function setBlocking(bool $shouldBlock): void
    {
        if (!$this->isOpen()) {
            throw new \LogicException("Cannot set blocking mode of pipe #{$this->id}: pipe closed");
        }

        if (!\stream_set_blocking($this->pipe, $shouldBlock)) {
            throw new \RuntimeException("Failed to set blocking mode of pipe #{$this->id}");
        }
    }

    public function close(): void
    {
        if (!$this->isOpen()) {
            throw new \LogicException("Cannot close pipe #{$this->id}: already closed");
        }

        if (!\fclose($this->pipe)) {
            throw new \RuntimeException("Failed to close pipe #{$this->id}");
        }

        $this->pipe = null;
    }
}
