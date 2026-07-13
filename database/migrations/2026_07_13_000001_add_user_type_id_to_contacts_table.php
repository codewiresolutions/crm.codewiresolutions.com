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
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('user_type_id')->nullable()->after('description')->constrained('user_types')->nullOnDelete();
        });

        $userTypes = DB::table('user_types')->pluck('id', 'name');

        foreach ($userTypes as $name => $id) {
            DB::table('contacts')->whereRaw('LOWER(user_type) = ?', [strtolower($name)])->update(['user_type_id' => $id]);
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('user_type')->default('customer')->after('description');
        });

        $userTypes = DB::table('user_types')->pluck('name', 'id');

        foreach ($userTypes as $id => $name) {
            DB::table('contacts')->where('user_type_id', $id)->update(['user_type' => strtolower($name)]);
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_type_id');
        });
    }
};
