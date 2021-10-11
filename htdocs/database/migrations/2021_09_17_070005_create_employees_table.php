<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->unsignedMediumInteger('id',true);
            $table->unsignedSmallInteger('position_id')->nullable();
            $table->unsignedMediumInteger('parent_id')->nullable();
            $table->boolean('topmanager',1)->nullable();
            $table->unsignedTinyInteger('level')->nullable();
            $table->unsignedTinyInteger('subs')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('admin_created_id')->nullable();
            $table->unsignedBigInteger('admin_updated_id')->nullable();
            $table->string('full_name',256)->default('');
            $table->unsignedInteger('timestamp_start')->nullable(); // 2038
            $table->char('phone', 13)->nullable(); // only ukrainian valid phone numbers
            $table->string('email',320)->nullable(); // 64 + 1 + 255 (rfc822)
            $table->unsignedDecimal('salary',6,3)->nullable(); // 0 - 500,000
            $table->string('filename',50)->nullable(); // photo file (shard by id thousands)
            $table->string('filename_thumb',50)->nullable(); // photo file thumb

            $table->index('full_name','employee_fullname_idx');
            $table->index('email','employee_email_idx');
            $table->index('phone','employee_phone_idx');
            $table->index('topmanager','employee_level_idx');
            $table->index('position_id','employee_position_idx');
            $table->foreign('position_id','employee_position_fk')->on('positions')->references('id');

        });

        DB::statement('ALTER TABLE employees MODIFY COLUMN level TINYINT (1);');
        DB::statement('ALTER TABLE employees MODIFY COLUMN subs TINYINT (2);');

        //$table->unsignedTinyInteger('level',['length=>1'])->nullable();
        //$table->unsignedTinyInteger('subs',['length=>2'])->nullable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
