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
        if (!Schema::hasTable('deployments')) {
            Schema::create('deployments', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_enabled')->default(true)->comment('disable to avoid this deployment');
                $table->string('code', 255)->nullable()->unique()->comment('unique dotted namespace');
                $table->string('label', 255)->nullable()->comment('label/short description');
                $table->mediumText('var_list')->nullable()->comment('json of vars merged with task vars');
                $table->mediumText('description')->nullable()->comment('description what this task will do');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('deployment_tasks')) {
            Schema::create('deployment_tasks', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_enabled')->default(true)->comment('disable this task for all deployments');
                $table->string('code', 255)->nullable()->unique()->comment('unique dotted namespace');
                $table->string('label', 255)->nullable()->comment('label/short description');
                $table->mediumText('description')->nullable()->comment('description what this task will do');
                $table->mediumText('command_list')->nullable()->comment('json of commands');
                $table->mediumText('var_list')->nullable()->comment('json of vars merged with deployment vars');
                $table->timestamps();
            });
        }
        // relation table: alphabetical order, singular, underline seperated
        if (!Schema::hasTable('deployment_deployment_task')) {
            Schema::create('deployment_deployment_task', function (Blueprint $table) {
                $table->unsignedBigInteger('deployment_id')->unsigned();
                $table->unsignedBigInteger('deployment_task_id')->unsigned();
                $table->boolean('is_enabled')->default(true)->comment('disable to avoid this task');
                $table->integer('position')->default(1000)->comment('Position in list. Lower values first.');
                $table->mediumText('var_list')->nullable()->comment('json of vars merged with task vars');

                $table->unique(['deployment_id', 'deployment_task_id'], 'u_depl_task_id');
                $table->foreign('deployment_id', 'fk_depl_task_d_id')
                    ->references('id')
                    ->on('deployments')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->foreign('deployment_task_id', 'fk_depl_task_t_id')
                    ->references('id')
                    ->on('deployment_tasks')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployment_deployment_task');
        Schema::dropIfExists('deployment_tasks');
        Schema::dropIfExists('deployments');
    }
};
