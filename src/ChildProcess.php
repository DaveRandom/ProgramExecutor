<?php declare(strict_types = 1);

namespace DaveRandom\ProgramExecutor;

final class ChildProcess implements Closable
{
    private $exitCode;
    private $pid;
    private $handle;

    /**
     * @var ChildPipe[]
     */
    private $pipes = [];

    public static function start(Command $command, ChildProcessOptions $options = null): self
    {
        $handle = \proc_open($command->getCommandLine(), [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes);

        if ($handle === false) {
            throw new \RuntimeException("Failed to start child process: {$command}");
        }

        $options = $options ?? new ChildProcessOptions;

        return new self(
            $handle,
            new ChildInputPipe($pipes[0]),
            new ChildOutputPipe($pipes[1], $options->shouldBufferStdOut(), $options->getStdOutPassthru()),
            new ChildOutputPipe($pipes[2], $options->shouldBufferStdErr(), $options->getStdErrPassthru())
        );
    }

    public function __construct($handle, ChildInputPipe $stdIn, ChildOutputPipe $stdOut, ChildOutputPipe $stdErr)
    {
        if (!$status = \proc_get_status($handle)) {
            throw new \RuntimeException("Failed to retrieve process status");
        }

        $this->pid = (int)$status['pid'];
        $this->handle = $handle;
        $this->pipes = [$stdIn, $stdOut, $stdErr];
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getStatus(): ChildProcessStatus
    {
        if (!$status = \proc_get_status($this->handle)) {
            throw new \RuntimeException("Failed to retrieve process status");
        }

        if (!$status['running'] && $this->exitCode === null) {
            $this->exitCode = (int)$status['exitcode'];
        }

        return new ChildProcessStatus(
            (bool)$status['running'],
            (bool)$status['signaled'],
            (bool)$status['stopped'],
            $this->exitCode,
            $status['signaled'] ? (int)$status['termsig'] : null,
            $status['stopped'] ? (int)$status['stopsig'] : null
        );
    }

    public function getStdIn(): ChildInputPipe
    {
        return $this->pipes[0];
    }

    public function getStdOut(): ChildOutputPipe
    {
        return $this->pipes[1];
    }

    public function getStdErr(): ChildOutputPipe
    {
        return $this->pipes[2];
    }

    public function isOpen(): bool
    {
        return $this->handle !== null;
    }

    public function getExitCode(): int
    {
        if ($this->exitCode === null && $this->getStatus()->isRunning()) {
            throw new \LogicException('Cannot retrieve exit code from a running process');
        }

        return (int)$this->exitCode;
    }

    public function close(): int
    {
        if (!$this->isOpen()) {
            throw new \LogicException("Cannot close process #{$this->pid}: already closed");
        }

        foreach ($this->pipes as $pipe) {
            if ($pipe->isOpen()) {
                $pipe->close();
            }
        }

        return $this->exitCode = \proc_close($this->handle);
    }
}
