<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldStatusToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // tambahkan field weight dengan tipe boolean
            $table->boolean('status')->default(true)->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // apabila rollback atau refresh dijalankan
        Schema::table('products', function (Blueprint $table) {
            //maka akan menghapus field status dari table products
            $table->dropColumn('status');
        });
    }
}
