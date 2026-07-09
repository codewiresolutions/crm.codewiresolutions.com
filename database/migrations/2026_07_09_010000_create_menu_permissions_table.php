<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_permissions', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['admin', 'manager', 'user']);
            $table->string('menu_key');
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->unique(['role', 'menu_key']);
        });

        $menuKeys = ['dashboard', 'whatsapp', 'customers'];
        $defaults = [
            'manager' => ['dashboard' => true, 'whatsapp' => true, 'customers' => true],
            'user' => ['dashboard' => true, 'whatsapp' => false, 'customers' => false],
        ];

        $rows = [];
        foreach ($defaults as $role => $visibility) {
            foreach ($menuKeys as $key) {
                $rows[] = [
                    'role' => $role,
                    'menu_key' => $key,
                    'is_visible' => $visibility[$key],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('menu_permissions')->insert($rows);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_permissions');
    }
};