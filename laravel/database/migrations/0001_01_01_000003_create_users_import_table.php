<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_imports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ext_id');

            $table->string('name');
            $table->date('date'); // @todo возможно добавить индекс

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_imports');
    }
};
