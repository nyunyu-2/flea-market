<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTradeRatingsToPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedTinyInteger('buyer_rating')->nullable()->after('status');
            $table->string('buyer_comment')->nullable()->after('buyer_rating');
            $table->unsignedTinyInteger('seller_rating')->nullable()->after('buyer_comment');
            $table->string('seller_comment')->nullable()->after('seller_rating');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['buyer_rating', 'buyer_comment', 'seller_rating', 'seller_comment']);
        });
    }
}
