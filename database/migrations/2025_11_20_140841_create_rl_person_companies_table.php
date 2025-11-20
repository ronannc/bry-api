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
        Schema::create('rl_persons_companies', function (Blueprint $table)
        {
            $table->foreignId('person_id')->constrained('persons');
            $table->foreignId('company_id')->constrained();
            $table->primary(['person_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rl_persons_companies');
    }
};
