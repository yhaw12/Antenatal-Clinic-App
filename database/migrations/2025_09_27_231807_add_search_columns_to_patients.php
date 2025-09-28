<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSearchColumnsToPatients extends Migration
{
    // public function up()
    // {
    //     Schema::table('patients', function (Blueprint $table) {
    //         $table->string('name_search')->nullable()->index();
    //         $table->string('phone_search')->nullable()->index();
    //     });
    // }

    // public function down()
    // {
    //     Schema::table('patients', function (Blueprint $table) {
    //         $table->dropIndex(['name_search']);
    //         $table->dropIndex(['phone_search']);
    //         $table->dropColumn(['name_search','phone_search']);
    //     });
    // }
}
