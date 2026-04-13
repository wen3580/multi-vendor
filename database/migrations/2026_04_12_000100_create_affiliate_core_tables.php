<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shop_domain')->unique();
            $table->string('shop_gid')->nullable();
            $table->text('access_token');
            $table->text('scopes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('app_embedded_enabled')->default(false);
            $table->boolean('app_proxy_enabled')->default(false);
            $table->unsignedInteger('default_cookie_days')->default(30);
            $table->enum('default_commission_type', ['percent', 'fixed'])->default('percent');
            $table->decimal('default_commission_value', 12, 2)->default(10.00);
            $table->enum('commission_approval_mode', ['manual', 'auto'])->default('manual');
            $table->timestamps();
        });

        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('email');
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'blocked'])->default('pending');
            $table->string('referral_code', 100);
            $table->string('referral_slug', 150)->nullable();
            $table->string('default_discount_code', 100)->nullable();
            $table->enum('commission_type', ['percent', 'fixed'])->nullable();
            $table->decimal('commission_value', 12, 2)->nullable();
            $table->enum('payout_method', ['paypal', 'bank', 'manual'])->nullable();
            $table->string('payout_account')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['shop_id', 'referral_code']);
            $table->unique(['shop_id', 'email']);
        });

        Schema::create('affiliate_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->string('email');
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('social_links')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->uuid('click_uuid')->unique();
            $table->string('ref_code', 100);
            $table->text('landing_url')->nullable();
            $table->text('referer')->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('ip', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('browser_cookie_id', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_attributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->unsignedBigInteger('order_id');
            $table->string('order_gid')->nullable();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->uuid('click_uuid')->nullable();
            $table->enum('attribution_type', ['coupon', 'cookie', 'manual']);
            $table->string('coupon_code', 100)->nullable();
            $table->string('ref_code', 100)->nullable();
            $table->timestamp('attributed_at');
            $table->timestamps();
            $table->unique(['shop_id', 'order_id']);
        });

        Schema::create('affiliate_coupon_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->string('discount_node_gid');
            $table->string('code', 100);
            $table->enum('discount_type', ['percent', 'fixed', 'shipping']);
            $table->decimal('discount_value', 12, 2);
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->unique(['shop_id', 'code']);
        });

        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->unsignedBigInteger('order_id');
            $table->string('order_gid')->nullable();
            $table->unsignedBigInteger('attribution_id')->nullable();
            $table->decimal('commission_base_amount', 12, 2)->default(0);
            $table->enum('commission_type', ['percent', 'fixed']);
            $table->decimal('commission_value', 12, 2);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->string('currency', 10);
            $table->enum('status', ['pending', 'approved', 'paid', 'void', 'refunded'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['shop_id', 'order_id', 'affiliate_id']);
        });

        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->string('payout_no', 50)->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10);
            $table->enum('method', ['paypal', 'bank', 'manual'])->default('manual');
            $table->enum('status', ['draft', 'processing', 'paid', 'failed'])->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->string('topic', 100);
            $table->string('webhook_id', 100)->nullable();
            $table->longText('payload');
            $table->boolean('hmac_valid')->default(false);
            $table->enum('process_status', ['pending', 'processed', 'failed', 'ignored'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->id();
            $table->string('idempotency_key', 150)->unique();
            $table->string('source_type', 50);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('affiliate_payouts');
        Schema::dropIfExists('affiliate_commissions');
        Schema::dropIfExists('affiliate_coupon_codes');
        Schema::dropIfExists('affiliate_attributions');
        Schema::dropIfExists('affiliate_clicks');
        Schema::dropIfExists('affiliate_applications');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('shops');
    }
};
