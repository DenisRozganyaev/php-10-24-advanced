<?php

return new class implements \App\Commands\Contract\Migration
{
    /**
    * Run migration script 
    * @return string
    */
    public function up(): string
    {
        return 'ALTER TABLE users MODIFY COLUMN token_expired_at BIGINT';
    }

    /**
    * Rollback migration script
    * @return string
    */
    public function down(): string
    {
        return 'ALTER TABLE users MODIFY COLUMN token_expired_at DATETIME';
    }
};
