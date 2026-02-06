<?php

namespace App\Lib\Commands;

abstract class AbstractCommand
{
    abstract public function execute(): void;
}
