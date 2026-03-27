<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 100)->unique()->nullable()->after('name');
            $table->enum('role', ['admin','operator'])->default('operator')->after('username');
            $table->tinyInteger('active')->default(1)->after('role');
            $table->tinyInteger('must_change_password')->default(0)->after('active');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('cnpj', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->tinyInteger('active')->default(1);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->nullable();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('unit', 20)->default('UN');
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->decimal('stock_quantity', 10, 3)->default(0);
            $table->decimal('min_stock', 10, 3)->default(0);
            $table->string('barcode', 50)->nullable();
            $table->string('ncm', 10)->default('22030000');
            $table->tinyInteger('active')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->enum('type', ['entrada','saida','ajuste','venda']);
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('cpf_cnpj', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->decimal('credit_limit', 10, 2)->default(0);
            $table->tinyInteger('active')->default(1);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number', 20)->unique()->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('cost_total', 10, 2)->default(0);
            $table->decimal('profit', 10, 2)->default(0);
            $table->enum('payment_method', ['dinheiro','cartao_credito','cartao_debito','pix','fiado','misto'])->default('dinheiro');
            $table->enum('status', ['aberta','finalizada','cancelada'])->default('finalizada');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('profit', 10, 2)->default(0);
        });

        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->enum('payment_method', ['dinheiro','cartao_credito','cartao_debito','pix','fiado']);
            $table->decimal('valor', 10, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->index('sale_id', 'idx_sale_payments_sale_id');
        });

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_number', 50)->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->date('purchase_date')->nullable();
            $table->enum('status', ['pendente','recebido','cancelado'])->default('recebido');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
        });

        Schema::create('caixas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['aberto','fechado'])->default('aberto');
            $table->decimal('valor_abertura', 10, 2)->default(0);
            $table->decimal('valor_fechamento', 10, 2)->nullable();
            $table->text('observacao_abertura')->nullable();
            $table->text('observacao_fechamento')->nullable();
            $table->dateTime('aberto_em');
            $table->dateTime('fechado_em')->nullable();
        });

        Schema::create('caixa_movimentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caixa_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('tipo', ['sangria','suprimento']);
            $table->decimal('valor', 10, 2);
            $table->text('motivo')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });

        Schema::create('fiado_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('valor', 10, 2);
            $table->enum('forma_pagamento', ['dinheiro','pix','cartao_debito','cartao_credito'])->default('dinheiro');
            $table->unsignedBigInteger('caixa_id')->nullable();
            $table->text('observacao')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });

        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('comanda_codigo', 30);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['aberto','fechando','finalizado','cancelado'])->default('aberto');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('mesa', 30)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index('comanda_codigo', 'idx_comanda_codigo');
            $table->index('status', 'idx_status');
        });

        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('pedido_id', 'idx_pedido_id');
        });

        Schema::create('pdv_carrinho', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name', 200);
            $table->string('unit', 20)->default('UN');
            $table->decimal('quantity', 10, 3)->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('payment_method', 30)->default('dinheiro');
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('notes', 255)->nullable();
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index('user_id', 'idx_user_id');
        });

        DB::table('users')->insert([
            'name'                 => 'Administrador',
            'username'             => 'admin',
            'password'             => Hash::make('admin'),
            'role'                 => 'admin',
            'active'               => 1,
            'must_change_password' => 1,
            'email'                => 'admin@nimvo.com',
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pdv_carrinho');
        Schema::dropIfExists('pedido_items');
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('fiado_pagamentos');
        Schema::dropIfExists('caixa_movimentos');
        Schema::dropIfExists('caixas');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('sale_payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('products');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('categories');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'active', 'must_change_password']);
        });
    }
};
