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
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            // General Information
            $table->string('title'); // e.g., Bponi - Pathao
            $table->string('logo')->nullable(); // For the image field shown in the list
            $table->enum('type', ['Fixed', 'Weight-Based', 'Distance-Based'])->default('Fixed');
            
            // Pricing Logic
            $table->decimal('min_charge', 10, 2)->default(0);
            $table->decimal('max_charge', 10, 2)->default(0);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            
            // API Instruction Fields
            $table->string('base_url')->nullable(); // Live API base URL
            $table->string('test_base_url')->nullable(); // Test/Sandbox API base URL
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable(); // Text used for longer encrypted strings
            $table->string('test_client_id')->nullable(); // Test environment client ID
            $table->text('test_client_secret')->nullable(); // Test environment client secret
            $table->string('client_email')->nullable();
            $table->string('client_password')->nullable();
            $table->string('grant_type')->default('password');
            $table->string('store_id')->nullable();
            
            // Status & Flags
            $table->boolean('is_live')->default(false); // Toggle between live and test environment
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};
