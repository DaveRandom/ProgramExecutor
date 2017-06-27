<?php declare(strict_types = 1);

namespace DaveRandom\ProgramExecutor;

final class ExecutionResult
{
    private $startTime;
    private $endTime;
    private $code;
    private $output;
    private $errors;

    public function __construct(
        \DateTimeImmutable $startTime, \DateTimeImmutable $endTime,
        int $code, string $output, string $errors
    ) {
        $this->code = $code;
        $this->output = $output;
        $this->errors = $errors;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public function getStartTime(): \DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): \DateTimeImmutable
    {
        return $this->endTime;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function getErrors(): string
    {
        return $this->errors;
    }
}
