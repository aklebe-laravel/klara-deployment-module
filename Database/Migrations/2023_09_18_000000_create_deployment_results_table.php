<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deployment_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deployment_id')->unsigned();
            $table->mediumText('results')->nullable()->comment('json of result');
            $table->foreign('deployment_id', 'fk_depl_result_d_id')
                ->references('id')
                ->on('deployments')
                ->onDelete('cascade')
                ->onUpdate('cascade');
//            $table->timestamp('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployment_results');
    }
};
