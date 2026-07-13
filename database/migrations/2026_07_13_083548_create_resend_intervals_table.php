<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resend_intervals', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->unsignedInteger('minutes');
            $table->timestamps();
        });

        $now = now();

        DB::table('resend_intervals')->insert([
            ['label' => '1 minute', 'minutes' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['label' => '30 minutes', 'minutes' => 30, 'created_at' => $now, 'updated_at' => $now],
            ['label' => '1 hour', 'minutes' => 60, 'created_at' => $now, 'updated_at' => $now],
            ['label' => '2 hours', 'minutes' => 120, 'created_at' => $now, 'updated_at' => $now],
            ['label' => '4 hours', 'minutes' => 240, 'created_at' => $now, 'updated_at' => $now],
            ['label' => '1 day', 'minutes' => 1440, 'created_at' => $now, 'updated_at' => $now],
            ['label' => '3 days', 'minutes' => 4320, 'created_at' => $now, 'updated_at' => $now],
            ['label' => '7 days', 'minutes' => 10080, 'created_at' => $now, 'updated_at' => $now],
            ['label' => '15 days', 'minutes' => 21600, 'created_at' => $now, 'updated_at' => $now],
            ['label' => '30 days', 'minutes' => 43200, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('resend_intervals');
    }
};
