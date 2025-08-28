<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_work', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('product_id');

            // Work details
            $table->decimal('employee_rate', 10, 2)->nullable();
            $table->decimal('product_rate', 10, 2)->nullable();

            $table->integer('total_sadi')->default(0);

            $table->decimal('employee_total', 10, 2)->nullable();
            $table->decimal('product_total', 10, 2)->nullable();

            $table->date('work_date');

            $table->timestamps();

            // Foreign Keys (optional if you want constraints)
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_work');
    }
};
