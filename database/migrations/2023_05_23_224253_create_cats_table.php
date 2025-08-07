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
        Schema::create('cats', function (Blueprint $table) {
            $table->id();
            $table->string('img')->nullable();
            $table->boolean('status')->default(0);
            $table->integer('follow')->default(0)->comment('1:Heritage,2:Media 4:City, 5:Tags, 6:Authors');
            $table->integer('ord')->default(0);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('show_in_media', [1, 2])->nullable();
            $table->enum('show_in_article', [1, 2])->nullable();
            $table->enum('type', [1, 2])->nullable()->comment('is_video');

            // ✅ Self-referencing foreign key
            $table->foreignId('parent_id')->nullable()->constrained('cats')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cats');
    }
};
