<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasColumn('deployments', 'rating')) {
            Schema::table('deployments', function (Blueprint $table) {
                $table->integer('rating')->default(5000)->after('is_enabled');
            });
        }
        if (!Schema::hasColumn('deployment_tasks', 'rating')) {
            Schema::table('deployment_tasks', function (Blueprint $table) {
                $table->integer('rating')->default(5000)->after('is_enabled');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
        Schema::table('deployment_tasks', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }

};
