<?php

use Symfony\Component\Console\Output\ConsoleOutput;
use function Termwind\renderUsing;

uses()
    ->beforeEach(fn () => renderUsing($this->output = Mockery::mock(ConsoleOutput::class)))
    ->in(__DIR__);
