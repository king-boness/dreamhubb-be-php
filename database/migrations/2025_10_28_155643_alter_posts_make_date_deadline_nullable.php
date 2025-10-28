<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE posts ALTER COLUMN date_deadline DROP NOT NULL;');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE posts ALTER COLUMN date_deadline SET NOT NULL;');
    }
};
