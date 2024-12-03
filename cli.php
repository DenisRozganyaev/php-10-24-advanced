<?php
const BASE_DIR = __DIR__; // /var/www/html

require_once BASE_DIR . '/vendor/autoload.php';

use App\Commands\Contract\Command;
use Dotenv\Dotenv;
use splitbrain\phpcli\CLI as BaseCli;
use splitbrain\phpcli\Options;

Dotenv::createUnsafeImmutable(BASE_DIR)->load();

class CLI extends BaseCli
{
    const COMMANDS_FILEPATH = BASE_DIR . '/configs/commands.php';

    protected array $commands = [], $setup = [];

    public function __construct($autocatch = true)
    {
        $config = require_once self::COMMANDS_FILEPATH;

        $this->commands = $config['commands'];
        $this->setup = $config['setup'];

        parent::__construct($autocatch);
    }

    protected function setup(Options $options)
    {
        foreach ($this->setup as $key => $setupData) {
            $options->registerCommand($setupData['command'], $setupData['description'] ?? '');

            if (!empty($setupData['arguments'])) {
                foreach ($setupData['arguments'] as $argument) {
                    $options->registerArgument(
                        $argument['name'],
                        $argument['description'] ?? '',
                        $argument['required'] ?? false,
                        $setupData['command']
                    );
                }
            }
        }
    }

    protected function main(Options $options)
    {
        if (array_key_exists($options->getCmd(), $this->commands)) {
            $cmd = new $this->commands[$options->getCmd()]($this, $options->getArgs());
            if ($cmd instanceof Command) {
                $cmd->handle();
            }
        } else {
            echo $options->help();
        }
    }
}

(new CLI())->run();
