<?php

return new class implements \App\Commands\Contract\Migration
{
    /**
    * Run migration script 
    * @return string
    */
    public function up(): string
    {
        return '';
    }

    /**
    * Rollback migration script
    * @return string
    */
    public function down(): string
    {
        return '';
    }
};
