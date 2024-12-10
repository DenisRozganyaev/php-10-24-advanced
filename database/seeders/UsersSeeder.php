<?php

namespace Database\Seeders;

use App\Models\User;
use JetBrains\PhpStorm\NoReturn;

class UsersSeeder extends Seeder
{
    #[NoReturn] public function run(): void
    {
        $existEmails = User::select(['email'])->pluck('email');

        for ($i = 0; $i < 5; $i++) {
            $email = $this->generateEmail($existEmails);
            $data = [
                'email' => $email,
                'password' => password_hash('test1234', PASSWORD_BCRYPT)
            ];

            User::create($data);
        }
    }

    protected function generateEmail(array $existingEmails): string
    {
        $email = $this->faker->unique()->email();

        if (in_array($email, $existingEmails)) {
            $email = $this->generateEmail($existingEmails);
        }

        return $email;
    }
}
