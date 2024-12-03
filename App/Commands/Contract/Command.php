<?php

namespace App\Commands\Contract;

use splitbrain\phpcli\CLI;

interface Command
{
    public function __construct(CLI $cli, array $args = []);

    public function handle(): void;
}
