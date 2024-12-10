<?php

namespace Database\Seeders;

use App\Enums\SQL;
use App\Models\Folder;
use App\Models\Note;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;

class NotesSeeder extends Seeder
{
    #[NoReturn] public function run(): void
    {
        $usersIds = User::select(['id'])->pluck('id');

        foreach($usersIds as $id) {
            $numberOfNotes = rand(0, 4);
            $folders = Folder::where('user_id', value: $id)
                ->or('title', SQL::EQUAL, 'General')
                ->pluck('id');

            if ($numberOfNotes === 0) {
                continue;
            }

            for ($i = 0; $i < $numberOfNotes; $i++) {
                $data = [
                    'user_id' => $id,
                    'folder_id' => $folders[rand(0, array_key_last($folders))],
                    'title' => $this->faker->words(rand(1, 3), true),
                    'content' => $this->faker->boolean() ? $this->faker->sentences(rand(1, 4), true) : null,
                    'pinned' => rand(0, 1),
                    'completed' => rand(0, 1)
                ];

                Note::create($data);
            }
        }
    }
}
