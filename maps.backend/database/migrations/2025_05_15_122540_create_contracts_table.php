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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            // внешний ключ к busstation (ogc_fid)
            $table->unsignedBigInteger('busstation_id');
            $table->foreign('busstation_id')
                  ->references('ogc_fid')
                  ->on('busstation')
                  ->onDelete('cascade');

            // Информация о файле
            $table->string('file_path');       // путь к файлу в хранилище
            $table->string('original_name');   // имя файла, которое загрузил пользователь
            $table->string('mime_type');       // тип файла
            $table->unsignedBigInteger('size'); // размер файла в байтах

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};