<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('employees', function (Blueprint $table) {
      $table->id();
      $table->foreignId('company_id')->constrained()->cascadeOnDelete();
      $table->string('matricule')->index();
      $table->string('first_name');
      $table->string('last_name');
      $table->string('email')->nullable()->unique();
      $table->string('phone')->nullable()->unique();
      $table->unsignedInteger('monthly_salary'); // FCFA
      $table->string('employee_code')->nullable();
      $table->boolean('eligible')->default(false);
      $table->string('password'); // login mobile
      $table->rememberToken();
      $table->timestamps();

      $table->unique(['company_id','matricule']);
    });
  }
  public function down(): void { Schema::dropIfExists('employees'); }
};
