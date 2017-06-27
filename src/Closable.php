<?php declare(strict_types = 1);

namespace DaveRandom\ProgramExecutor;

interface Closable
{
    function isOpen(): bool;
    function close();
}
