<?php

namespace Database\Seeders;

use Faker\Factory;
use Faker\Generator;

abstract class Seeder
{
    protected Generator $faker;

    static public array $seeds = [
        UsersSeeder::class,
        FoldersSeeder::class,
        NotesSeeder::class,
    ];

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    abstract public function run(): void;
}