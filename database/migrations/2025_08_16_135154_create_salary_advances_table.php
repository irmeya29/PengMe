<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('salary_advances', function (Blueprint $table) {
      $table->id();
      $table->foreignId('company_id')->constrained()->cascadeOnDelete();
      $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
      $table->unsignedInteger('amount_requested'); // 10k-50k
      $table->unsignedInteger('fee_fixed')->default(2000); // param global
      $table->unsignedInteger('amount_final'); // requested + 2000
      $table->unsignedInteger('total_repayable'); // = amount_final (MVP)
      $table->enum('status', ['pending','approved','rejected','paid'])->default('pending');
      $table->json('meta')->nullable();
      $table->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('salary_advances'); }
};
