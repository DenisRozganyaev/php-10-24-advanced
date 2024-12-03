<?php

namespace App\Commands\Migrations;

use App\Commands\Contract\Command;
use splitbrain\phpcli\CLI;

class Run implements Command
{

    public function __construct(CLI $cli, array $args = [])
    {
    }

    public function handle(): void
    {
        // TODO: Implement handle() method.
    }
}
