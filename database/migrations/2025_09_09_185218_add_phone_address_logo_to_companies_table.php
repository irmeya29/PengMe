<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('companies', function (Blueprint $table) {
      if (!Schema::hasColumn('companies','phone')) {
        $table->string('phone', 50)->nullable()->after('email');
      }
      if (!Schema::hasColumn('companies','address')) {
        $table->string('address', 255)->nullable()->after('phone');
      }
      if (!Schema::hasColumn('companies','logo_path')) {
        $table->string('logo_path', 255)->nullable()->after('address');
      }
    });
  }
  public function down(): void {
    Schema::table('companies', function (Blueprint $table) {
      if (Schema::hasColumn('companies','logo_path')) $table->dropColumn('logo_path');
      if (Schema::hasColumn('companies','address'))   $table->dropColumn('address');
      if (Schema::hasColumn('companies','phone'))     $table->dropColumn('phone');
    });
  }
};
