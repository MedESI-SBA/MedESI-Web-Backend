<?php

use App\Enums\AppoitmentStatus;
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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->dateTime('dateTime')->nullable();
            $table->dateTime("requestedDate")->nullable();
            $table->text("notes")->nullable();
            $table->enum("status",array_column(App\Enums\AppoitmentStatus::cases(),'value'));
            $table->enum('createdBy',array_column(App\Enums\AppoitmentCreator::cases(),'value'));
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
