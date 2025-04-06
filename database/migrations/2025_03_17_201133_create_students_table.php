<?php

use App\Enums\PatientTypes;
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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('familyName');
            $table->string('firstName');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('age');
            $table->string('phoneNumber')->unique();
            $table->enum('patientType',array_column(PatientTypes::cases(), 'value'))->default(PatientTypes::STUDENT->value);
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
