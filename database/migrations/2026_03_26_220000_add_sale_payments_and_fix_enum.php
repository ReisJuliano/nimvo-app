<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE sales
            MODIFY COLUMN payment_method
            ENUM('dinheiro','cartao_credito','cartao_debito','pix','fiado','misto')
            NOT NULL DEFAULT 'dinheiro'
        ");

        if (!Schema::hasTable('sale_payments')) {
            Schema::create('sale_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sale_id');
                $table->enum('payment_method', ['dinheiro','cartao_credito','cartao_debito','pix','fiado']);
                $table->decimal('valor', 10, 2)->default(0);
                $table->timestamp('created_at')->useCurrent();
                $table->index('sale_id', 'idx_sale_payments_sale_id');
            });
        }

        DB::statement("
            INSERT INTO sale_payments (sale_id, payment_method, valor)
            SELECT s.id, s.payment_method, s.total
            FROM sales s
            WHERE s.payment_method != 'misto'
              AND s.total > 0
              AND NOT EXISTS (
                  SELECT 1 FROM sale_payments sp WHERE sp.sale_id = s.id
              )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');

        DB::statement("
            ALTER TABLE sales
            MODIFY COLUMN payment_method
            ENUM('dinheiro','cartao_credito','cartao_debito','pix','fiado')
            NOT NULL DEFAULT 'dinheiro'
        ");
    }
};
