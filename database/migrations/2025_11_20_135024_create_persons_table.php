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
        Schema::create('persons', function (Blueprint $table)
        {
            $table->id();
            $table->string('login')->unique();
            $table->string('name');
            $table->string('cpf');
            $table->string('email');
            $table->string('address')->nullable();
            $table->string('password');
            $table->string('type')->comment('Categoriza entre cliente e funcionário');
            $table->string('document_path')->nullable()->comment('Caminho do arquivo do documento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
