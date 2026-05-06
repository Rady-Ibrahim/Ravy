<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // اسم المحافظة
            $table->string('name_en')->unique(); // الاسم بالإنجليزية
            $table->decimal('shipping_cost', 8, 2)->default(0); // تكلفة الشحن
            $table->boolean('is_active')->default(true); // هل الشحن متاح للمحافظة
            $table->integer('delivery_days')->default(2); // أيام التوصيل المتوقعة
            $table->timestamps();
        });

        // إدخال المحافظات الإماراتية
        DB::table('governorates')->insert([
            ['name' => 'أبوظبي', 'name_en' => 'Abu Dhabi', 'shipping_cost' => 25.00, 'is_active' => true, 'delivery_days' => 2],
            ['name' => 'دبي', 'name_en' => 'Dubai', 'shipping_cost' => 20.00, 'is_active' => true, 'delivery_days' => 1],
            ['name' => 'الشارقة', 'name_en' => 'Sharjah', 'shipping_cost' => 20.00, 'is_active' => true, 'delivery_days' => 1],
            ['name' => 'عجمان', 'name_en' => 'Ajman', 'shipping_cost' => 15.00, 'is_active' => true, 'delivery_days' => 1],
            ['name' => 'أم القيوين', 'name_en' => 'Umm Al Quwain', 'shipping_cost' => 15.00, 'is_active' => true, 'delivery_days' => 2],
            ['name' => 'رأس الخيمة', 'name_en' => 'Ras Al Khaimah', 'shipping_cost' => 25.00, 'is_active' => true, 'delivery_days' => 2],
            ['name' => 'الفجيرة', 'name_en' => 'Fujairah', 'shipping_cost' => 30.00, 'is_active' => true, 'delivery_days' => 3],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('governorates');
    }
};
