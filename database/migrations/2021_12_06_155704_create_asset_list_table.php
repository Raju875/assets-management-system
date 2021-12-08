<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_list', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->nullable();
            $table->integer('asset_id');
            $table->integer('cat_id');
            $table->integer('sub_cat_id');
            $table->tinyInteger('is_assign')->default(0)->comment('0=not assign, 1=assign');
            $table->integer('assign_user_id')->nullable();
            $table->integer('assign_dept_id')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=inactive, 1=active');
            $table->timestamps();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_list');
    }
}
