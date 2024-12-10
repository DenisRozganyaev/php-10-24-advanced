<?php

namespace App\Commands;

use App\Commands\Contract\Command;
use Database\Seeders\Seeder;
use Exception;
use PDOException;

class Seed implements Command
{

    public function __construct(public \splitbrain\phpcli\CLI $cli, public array $args = [])
    {
    }

    public function handle(): void
    {
        try {
            db()->beginTransaction();
            $this->cli->info("Seed process has been start...");

            $this->runSeeds();

            db()->commit();
            $this->cli->success("Seed process has been done!");
        } catch (PDOException $exception) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            $this->cli->fatal($exception->getMessage());
        } catch (Exception $exception) {
            $this->cli->fatal($exception->getMessage());
        }
    }

    protected function runSeeds(): void
    {
        if (!empty(Seeder::$seeds)) {
            foreach(Seeder::$seeds as $seedClass) {
                /**
                 * @var Seeder $seed
                 */
                $seed = new $seedClass;
                $seed->run();
            }
        }
    }
}