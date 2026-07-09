<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('menu_permissions')->insert([
            ['role' => 'manager', 'menu_key' => 'csv', 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()],
            ['role' => 'user', 'menu_key' => 'csv', 'is_visible' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menu_permissions')->where('menu_key', 'csv')->delete();
    }
};