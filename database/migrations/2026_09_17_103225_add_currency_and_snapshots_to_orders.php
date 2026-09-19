<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('currency', 3)
                ->default('USD')
                ->after('total');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_name')
                ->nullable()
                ->after('product_id');

            $table->string('currency', 3)
                ->nullable()
                ->after('price');

            $table->decimal('converted_price', 10, 2)
                ->nullable()
                ->after('currency');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')
                ->nullable()
                ->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')
                ->nullable(false)
                ->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();

            $table->dropColumn([
                'product_name',
                'currency',
                'converted_price',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
