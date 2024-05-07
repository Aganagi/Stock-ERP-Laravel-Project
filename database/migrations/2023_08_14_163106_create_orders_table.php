<?php

use App\Models\Client;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Client::class)->constrained()->onDelete('set null')->onUpdate('cascade');
            $table->foreignIdFor(Product::class)->constrained()->onDelete('set null')->onUpdate('cascade');
            $table->integer('amount');
            $table->boolean('confirm')->default(false);
            $table->timestamps();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};