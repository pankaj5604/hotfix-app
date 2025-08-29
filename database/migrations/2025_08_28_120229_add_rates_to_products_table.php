<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('product_rate', 10, 2)->default(0)->after('type');
            $table->decimal('employee_rate', 10, 2)->default(0)->after('product_rate');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['product_rate', 'employee_rate']);
        });
    }

};
