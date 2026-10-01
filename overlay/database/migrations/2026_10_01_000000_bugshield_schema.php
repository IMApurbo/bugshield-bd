<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('researcher');          // researcher | organization | admin
            $t->string('phone')->nullable();
            $t->string('status')->default('active');            // active | pending | suspended
            $t->decimal('balance', 12, 2)->default(0);
            $t->unsignedInteger('reputation')->default(0);
            $t->text('bio')->nullable();
        });

        Schema::create('organizations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('website');
            $t->string('trx_id')->unique();                     // bKash transaction id
            $t->string('sender_number');                        // bKash number the fee was sent from
            $t->decimal('fee_amount', 12, 2);
            $t->string('status')->default('pending');           // pending | approved | rejected
            $t->text('admin_note')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->timestamps();
        });

        Schema::create('programs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->text('description');
            $t->text('scope');
            $t->unsignedInteger('reward_critical')->default(50000);
            $t->unsignedInteger('reward_high')->default(20000);
            $t->unsignedInteger('reward_medium')->default(8000);
            $t->unsignedInteger('reward_low')->default(2000);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('reports', function (Blueprint $t) {
            $t->id();
            $t->foreignId('program_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->string('type');
            $t->text('steps');
            $t->text('impact')->nullable();
            $t->string('cvss_vector');
            $t->decimal('cvss_score', 3, 1)->default(0);
            $t->string('severity')->default('informative');
            $t->string('status')->default('new');              // new|triaged|resolved|duplicate|informative
            $t->unsignedInteger('reward_amount')->default(0);
            $t->boolean('reward_paid')->default(false);
            $t->string('evidence_path')->nullable();
            $t->unsignedBigInteger('duplicate_of')->nullable();
            $t->text('org_response')->nullable();
            $t->timestamps();
        });

        Schema::create('comments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('report_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->text('body');
            $t->timestamps();
        });

        Schema::create('withdrawals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('bkash_number');
            $t->decimal('amount', 12, 2);
            $t->string('status')->default('successful');
            $t->string('reference');
            $t->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action');
            $t->text('details')->nullable();
            $t->string('ip')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'withdrawals', 'comments', 'reports', 'programs', 'organizations'] as $tbl) {
            Schema::dropIfExists($tbl);
        }
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['role', 'phone', 'status', 'balance', 'reputation', 'bio']);
        });
    }
};
