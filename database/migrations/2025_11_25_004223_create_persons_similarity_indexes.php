<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->string('name_normalized')->nullable();
            $table->string('name_metaphone')->nullable();
        });

        DB::statement("
            CREATE OR REPLACE FUNCTION persons_update_normalized()
            RETURNS trigger AS $$
            BEGIN
                NEW.name_normalized := lower(unaccent(NEW.name));
                NEW.name_metaphone := metaphone(lower(unaccent(NEW.name)), 4);
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE TRIGGER trg_persons_normalized
            BEFORE INSERT OR UPDATE OF name ON persons
            FOR EACH ROW
            EXECUTE FUNCTION persons_update_normalized();
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_persons_name_normalized ON persons (name_normalized);");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_persons_name_metaphone ON persons (name_metaphone);");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_persons_name_trgm ON persons USING gin (name_normalized gin_trgm_ops);");
    }

    public function down(): void
    {
        DB::statement("DROP TRIGGER IF EXISTS trg_persons_normalized ON persons;");
        DB::statement("DROP FUNCTION IF EXISTS persons_update_normalized();");

        DB::statement("DROP INDEX IF EXISTS idx_persons_name_normalized;");
        DB::statement("DROP INDEX IF EXISTS idx_persons_name_metaphone;");
        DB::statement("DROP INDEX IF EXISTS idx_persons_name_trgm;");

        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn('name_normalized');
            $table->dropColumn('name_metaphone');
        });
    }
};
