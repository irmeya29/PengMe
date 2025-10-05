<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('payouts', function (Blueprint $table) {
      $table->id();
      $table->foreignId('salary_advance_id')->constrained()->cascadeOnDelete();
      $table->enum('method', ['orange_money']); // extensible
      $table->enum('status', ['pending','success','failed'])->default('pending');
      $table->string('reference')->nullable(); // ref fictive MVP
      $table->json('meta')->nullable();
      $table->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('payouts'); }
};
