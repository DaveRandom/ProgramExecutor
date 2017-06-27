<?php declare(strict_types = 1);

namespace DaveRandom\ProgramExecutor;

final class ChildProcessOptions
{
    private $shouldBufferStdOut = true;
    private $shouldBufferStdErr = true;
    private $stdOutPassthru = null;
    private $stdErrPassthru = null;

    public function shouldBufferStdOut(): bool
    {
        return $this->shouldBufferStdOut;
    }

    public function bufferStdOut(bool $shouldBuffer): ChildProcessOptions
    {
        $this->shouldBufferStdOut = $shouldBuffer;
        return $this;
    }

    public function shouldBufferStdErr(): bool
    {
        return $this->shouldBufferStdErr;
    }

    public function bufferStdErr(bool $shouldBuffer): ChildProcessOptions
    {
        $this->shouldBufferStdErr = $shouldBuffer;
        return $this;
    }

    public function getStdOutPassthru()
    {
        return $this->stdOutPassthru;
    }

    public function setStdOutPassthru($stdOutPassthru): ChildProcessOptions
    {
        if (!\is_resource($stdOutPassthru)) {
            throw new \LogicException('Invalid stream resource for stdout passthru');
        }

        $this->stdOutPassthru = $stdOutPassthru;
        return $this;
    }

    public function getStdErrPassthru()
    {
        return $this->stdErrPassthru;
    }

    public function setStdErrPassthru($stdErrPassthru): ChildProcessOptions
    {
        if (!\is_resource($stdErrPassthru)) {
            throw new \LogicException('Invalid stream resource for stdout passthru');
        }

        $this->stdErrPassthru = $stdErrPassthru;
        return $this;
    }
}
