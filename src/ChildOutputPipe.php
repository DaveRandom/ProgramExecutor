<?php declare(strict_types = 1);

namespace DaveRandom\ProgramExecutor;

final class ChildOutputPipe extends ChildPipe
{
    private $shouldBuffer;
    private $passthruPipe;
    private $output = '';

    public function __construct($pipe, bool $shouldBuffer, $passthruPipe)
    {
        parent::__construct($pipe);

        $this->shouldBuffer = $shouldBuffer;
        $this->passthruPipe = $passthruPipe;
    }

    public function read(int $length = 1024): string
    {
        if (!$this->isOpen()) {
            throw new \LogicException("Cannot read from pipe #{$this->getId()}: pipe closed");
        }

        if (false === $data = \fread($this->getPipe(), $length)) {
            throw new \RuntimeException("Failed to read from pipe #{$this->getId()}");
        }

        if ($data === '') {
            return '';
        }

        if ($this->shouldBuffer) {
            $this->output .= $data;
        }

        if ($this->passthruPipe !== null) {
            \fwrite($this->passthruPipe, $data);
        }

        return $data;
    }

    public function getOutput(): string
    {
        return $this->output;
    }
}
