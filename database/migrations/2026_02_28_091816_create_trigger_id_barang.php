<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared('
            CREATE OR REPLACE FUNCTION generate_id_barang()
            RETURNS TRIGGER AS $$
            DECLARE
                nr INTEGER;
            BEGIN
                SELECT COUNT(*) + 1 INTO nr
                FROM barang
                WHERE DATE(created_at) = CURRENT_DATE;

                NEW.id_barang :=
                    TO_CHAR(CURRENT_TIMESTAMP, \'YYMMDD\') ||
                    LPAD(nr::TEXT, 2, \'0\');

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trigger_id_barang ON barang;

            CREATE TRIGGER trigger_id_barang
            BEFORE INSERT ON barang
            FOR EACH ROW
            EXECUTE FUNCTION generate_id_barang();
        ');
    }

    public function down(): void
    {
        DB::unprepared('
            DROP TRIGGER IF EXISTS trigger_id_barang ON barang;
            DROP FUNCTION IF EXISTS generate_id_barang;
        ');
    }
};