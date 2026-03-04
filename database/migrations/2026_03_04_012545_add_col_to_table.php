<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'note')) {
                $table->text('note')->nullable()->after('table_number');
            }
            if (!Schema::hasColumn('orders', 'cancel_remark')) {
                $table->text('cancel_remark')->nullable()->after('note');
            }

            if (!Schema::hasColumn('orders', 'status')) {
                $table->enum('status', ['pending', 'preparing', 'ready', 'delivered', 'cancelled'])
                      ->default('pending')
                      ->after('cancel_remark');
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'note')) {
                $table->text('note')->nullable()->after('is_select');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('orders', 'cancel_remark')) $columns[] = 'cancel_remark';
            if (Schema::hasColumn('orders', 'status'))        $columns[] = 'status';
            if (Schema::hasColumn('orders', 'note'))          $columns[] = 'note';

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};