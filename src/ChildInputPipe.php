<?php declare(strict_types = 1);

namespace DaveRandom\ProgramExecutor;

final class ChildInputPipe extends ChildPipe
{
    public function __construct($pipe)
    {
        parent::__construct($pipe);
    }

    public function write(string $data): void
    {
        if ($this->isOpen()) {
            throw new \LogicException("Cannot write to pipe #{$this->getId()}: pipe closed");
        }

        if (\fwrite($this->getPipe(), $data) !== \strlen($data)) {
            throw new \RuntimeException("Failed to write to pipe #{$this->getId()}");
        }
    }
}
