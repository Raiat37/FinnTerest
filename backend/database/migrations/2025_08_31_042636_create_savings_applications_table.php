<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('savings_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_id')->constrained('banks')->restrictOnDelete();

            // amount the user is proposing to save for this period
            $table->decimal('amount', 12, 2);

            // 'auto' = system-proposed (salary - budget if positive), 'manual' = user-entered
            $table->enum('mode', ['auto', 'manual'])->default('auto');

            // status flow
            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled'])->default('pending');

            // month bucket to prevent duplicates; store the first day of the month (e.g., 2025-08-01)
            $table->date('period_month');

            // admin audit
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'period_month'], 'uniq_user_month');
            $table->index(['status', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_applications');
    }
};
