<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password',255)->default(NULL)->nullable()->change();
            $table->string('mobile',20)->default(NULL)->nullable()->after('email');
            $table->enum('type',['admin','user','tutor'])->after('mobile');
            $table->string('username')->nullable()->after('type');
            $table->string('name')->nullable()->change();
            $table->enum('ticket_level',['no_ticket','language_lounge_gold','language_lounge_platinum'])->nullable()->after('username');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mobile');
            $table->dropColumn('social_id');
            $table->dropColumn('social_type');
            $table->dropColumn('avatar');
            $table->dropColumn('code');
            $table->dropSoftDeletes();
        });
    }
};
