<?php declare(strict_types = 1);

namespace DaveRandom\ProgramExecutor;

final class ChildProcessStatus
{
    private $running;
    private $signalled;
    private $stopped;
    private $exitCode;
    private $termSig;
    private $stopSig;

    public function __construct(bool $running, bool $signalled, bool $stopped, ?int $exitCode, ?int $termSig, ?int $stopSig)
    {
        $this->running = $running;
        $this->signalled = $signalled;
        $this->stopped = $stopped;
        $this->exitCode = $exitCode;
        $this->termSig = $termSig;
        $this->stopSig = $stopSig;
    }

    public function isRunning(): bool
    {
        return $this->running;
    }

    public function isSignalled(): bool
    {
        return $this->signalled;
    }

    public function isStopped(): bool
    {
        return $this->stopped;
    }

    public function getExitCode(): ?int
    {
        return $this->exitCode;
    }

    public function getTermSig(): ?int
    {
        return $this->termSig;
    }

    public function getStopSig(): ?int
    {
        return $this->stopSig;
    }
}
