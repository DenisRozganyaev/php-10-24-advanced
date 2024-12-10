<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;

class FoldersSeeder extends Seeder
{
    #[NoReturn] public function run(): void
    {
        $usersIds = User::select(['id'])->pluck('id');

        foreach($usersIds as $id) {
            $numberOfFolders = rand(0, 4);

            if ($numberOfFolders === 0) {
                continue;
            }

            for ($i = 0; $i < $numberOfFolders; $i++) {
                $data = [
                    'user_id' => $id,
                    'title' => $this->faker->words(rand(1, 3), true)
                ];

                Folder::create($data);
            }
        }
    }
}
