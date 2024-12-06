<?php

namespace App\Commands\Migrations;

use App\Commands\Contract\Command;
use PDO;
use PDOException;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Exception;
use Throwable;

class Rollback implements Command
{
    const string MIGRATIONS_DIR = BASE_DIR . '/database/migrations';

    public function __construct(public CLI $cli, public array $args = [])
    {
    }

    public function handle(): void
    {
        try {
            db()->beginTransaction();
            $this->cli->info("Rollback process has been started...");

            $this->rollbackMigrations();
            $this->deleteLastMigrationsRecords();

            db()->commit();
            $this->cli->info("Rollback process has been finished...");
        } catch (Throwable $e) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            $this->cli->fatal($e->getMessage());
        }
    }

    protected function rollbackMigrations(): void
    {
        $this->cli->info("");
        $this->cli->info("Rollback migrations...");

        $migrations = $this->getLastMigrations();

        if (empty($migrations)) {
            $this->cli->info('Nothing to rollback');
            exit;
        }

        foreach ($migrations as $fileName) {
            $name = preg_replace('/[\d]+_/', '', $fileName);
            $this->cli->notice("- rollback $name");

            $script = $this->getScript($fileName); # get script

            if (empty($script)) {
                $this->cli->warning("An empty script!");
                continue;
            }

            $query = db()->prepare($script);

            if ($query->execute()) {
                $this->cli->success("- $name was successfully rollbacked!");
            }
        }
    }

    protected function getScript(string $fileName): string
    {
        $obj = null;
        $obj = require_once self::MIGRATIONS_DIR . '/' . $fileName;
        return $obj?->down() ?? '';
    }

    protected function getLastMigrations($column = 'name'): array
    {
        $query = db()->prepare("SELECT $column FROM migrations WHERE batch IN (
            SELECT MAX(batch) as batch FROM migrations
        ) ORDER BY id DESC");
        $query->execute();

        return array_map(fn ($item) => $item[$column], $query->fetchAll(PDO::FETCH_ASSOC));
    }

    protected function deleteLastMigrationsRecords(): void
    {
        $migrations = implode(', ', $this->getLastMigrations('id'));
        $query = db()->prepare("DELETE FROM migrations WHERE id IN ($migrations)");
        $query->execute();
    }
}
