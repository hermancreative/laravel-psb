<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMapelToCalonSiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calon_siswas', function (Blueprint $table) {
            $table->integer('matematika');
            $table->integer('ipa');
            $table->integer('bhs_indonesia');
            $table->integer('bhs_inggris');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calon_siswas', function (Blueprint $table) {
            $table->dropColumn('matematika');
            $table->dropColumn('ipa');
            $table->dropColumn('bhs_indonesia');
            $table->dropColumn('bhs_inggris');
        });
    }
}