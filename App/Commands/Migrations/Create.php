<?php

namespace App\Commands\Migrations;

use App\Commands\Contract\Command;
use App\Commands\Contract\Migration;
use splitbrain\phpcli\CLI;
use Throwable;

class Create implements Command
{
    const string MIGRATIONS_DIR = BASE_DIR . '/database/migrations';

    public function __construct(protected CLI $cli, protected array $args = [])
    {
    }

    public function handle(): void
    {
        $this->createDir();
        $this->createMigration();
    }

    protected function createMigration(): void
    {
        $name = time() . '_' . $this->args[0];
        $fullPath = self::MIGRATIONS_DIR . "/$name.php";

        try {
            file_put_contents($fullPath, Migration::TEMPLATE, FILE_APPEND);
            $this->cli->info("Migration [" . $this->args[0] . "] was created");
        } catch (Throwable $exception) {
            $this->cli->error($exception->getMessage());
        }
    }

    protected function createDir(): void
    {
        if (!file_exists(self::MIGRATIONS_DIR)) {
            mkdir(self::MIGRATIONS_DIR, recursive: true);
            $this->cli->info("Migrations directory created");
        }
    }
}
