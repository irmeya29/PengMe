<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('companies', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('rccm')->nullable();
      $table->string('ifu')->nullable();
      $table->string('email')->unique();
      $table->string('password');
      $table->string('code')->unique(); // code-entreprise
      $table->string('phone')->nullable();
      $table->string('logo_path')->nullable();
      $table->boolean('is_active')->default(true);
      $table->rememberToken();
      $table->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('companies'); }
};
