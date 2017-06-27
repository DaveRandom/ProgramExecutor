<?php declare(strict_types = 1);

namespace DaveRandom\ProgramExecutor;

final class CommandExecutor
{
    public const OUTPUT_PASSTHRU  = 0b01;
    public const OUTPUT_NO_BUFFER = 0b10;

    private function processOutput(ChildProcess $process): void
    {
        $stdOut = $process->getStdOut();
        $stdErr = $process->getStdErr();

        $stdOut->setBlocking(false);
        $stdErr->setBlocking(false);

        /** @var ChildOutputPipe[] $wrappers */
        $wrappers = [$stdOut->getId() => $stdOut, $stdErr->getId() => $stdErr];
        $pipes = [$stdOut->getId() => $stdOut->getPipe(), $stdErr->getId() => $stdErr->getPipe()];

        $w = $e = null;

        do {
            $r = $pipes;
            \stream_select($r, $w, $e, null);

            foreach ($r as $pipe) {
                if ('' === $wrappers[(int)$pipe]->read()) {
                    unset($pipes[(int)$pipe]);
                }
            }
        } while (!empty($pipes));
    }

    private function createOptionsFromFlags(int $flags): ChildProcessOptions
    {
        $options = new ChildProcessOptions;

        if ($flags & self::OUTPUT_PASSTHRU) {
            $options
                ->setStdOutPassthru(\STDOUT)
                ->setStdErrPassthru(\STDERR);
        }

        if ($flags & self::OUTPUT_NO_BUFFER) {
            $options
                ->bufferStdOut(false)
                ->bufferStdErr(false);
        }

        return $options;
    }

    public function run(Command $command, string $input = null, int $flags = 0): ExecutionResult
    {
        $options = $this->createOptionsFromFlags($flags);

        $startTime = new \DateTimeImmutable;
        $process = ChildProcess::start($command, $options);

        if ($input !== null) {
            $process->getStdIn()->write($input);
        }

        $process->getStdIn()->close();

        $this->processOutput($process);

        $code = $process->close();
        $endTime = new \DateTimeImmutable;

        return new ExecutionResult(
            $startTime, $endTime,
            $code, $process->getStdOut()->getOutput(), $process->getStdErr()->getOutput()
        );
    }
}
