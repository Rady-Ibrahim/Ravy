<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Product\Models\Brand;
use Modules\Product\Models\CategoryAttribute;
use Modules\Product\Models\CategoryAttributeValue;
use Modules\Product\Models\Color;
use Modules\Product\Models\Product;
use Modules\Product\Models\Size;
use Modules\Product\Models\Variant;
use Modules\Product\Services\Admin\VariantService;
use Modules\Category\Models\Category;

class ProductVariantDemoSeeder extends Seeder
{
    public function __construct(
        private readonly VariantService $variantService
    ) {}

    public function run(): void
    {
        // Clean previous data for demo tables
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \Illuminate\Support\Facades\DB::table('variant_attributes')->truncate();
        \Illuminate\Support\Facades\DB::table('variants')->truncate();
        \Illuminate\Support\Facades\DB::table('product_images')->truncate();
        \Illuminate\Support\Facades\DB::table('product_category')->truncate();
        \Illuminate\Support\Facades\DB::table('products')->truncate();
        \Illuminate\Support\Facades\DB::table('brands')->truncate();
        \Illuminate\Support\Facades\DB::table('sizes')->truncate();
        \Illuminate\Support\Facades\DB::table('colors')->truncate();
        // delete category attribute values for size/color
        $attributeIds = \Illuminate\Support\Facades\DB::table('category_attributes')->whereIn('code', ['size', 'color'])->pluck('id')->all();
        if (!empty($attributeIds)) {
            \Illuminate\Support\Facades\DB::table('category_attribute_values')->whereIn('attribute_id', $attributeIds)->delete();
        }
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ✅ الخطوة 1: إنشاء ألوان جديدة (يتم نسخها تلقائياً لـ category_attribute_values)
        $colors = [
            ['name' => 'Red', 'code' => 'red', 'hex' => '#FF0000'],
            ['name' => 'Blue', 'code' => 'blue', 'hex' => '#0000FF'],
            ['name' => 'Green', 'code' => 'green', 'hex' => '#00AA00'],
        ];

        $colorIds = [];
        foreach ($colors as $color) {
            $colorModel = Color::query()->firstOrCreate(
                ['code' => $color['code']],
                ['name' => $color['name']]
            );
            // Color hex stored in category_attribute_values->extra
            $colorIds[$color['code']] = $this->syncColorToAttributeValues($colorModel, $color['hex']);
        }

        // ✅ الخطوة 2: إنشاء أحجام جديدة (يتم نسخها تلقائياً لـ category_attribute_values)
        $sizes = [
            ['name' => 'Small', 'code' => 'S', 'code_from' => '34', 'code_to' => '36'],
            ['name' => 'Medium', 'code' => 'M', 'code_from' => '38', 'code_to' => '40'],
            ['name' => 'Large', 'code' => 'L', 'code_from' => '42', 'code_to' => '44'],
            ['name' => 'XL', 'code' => 'XL', 'code_from' => '46', 'code_to' => '48'],
        ];

        $sizeIds = [];
        foreach ($sizes as $size) {
            $sizeModel = Size::query()->firstOrCreate(
                ['code' => $size['code']],
                [
                    'name' => $size['name'],
                    'code_from' => $size['code_from'],
                    'code_to' => $size['code_to'],
                ]
            );
            $sizeIds[$size['code']] = $this->syncSizeToAttributeValues($sizeModel);
        }

        // ✅ الخطوة 3: الحصول على الفئة (الافتراضية: Fashion)
        $category = Category::query()->firstWhere('slug', 'fashion')
            ?? Category::query()->first();

        if (!$category) {
            $this->command->warn('⚠️ لم تجد أي فئة في قاعدة البيانات');
            return;
        }

        // ✅ الخطوة 4: إنشاء براند
        $brand = Brand::query()->firstOrCreate(
            ['slug' => 'demo-brand'],
            ['name' => 'Demo Brand', 'is_active' => true]
        );

        // ✅ الخطوة 5: إنشاء منتج جديد
        $product = Product::query()->create([
            'name' => 'Premium T-Shirt Collection',
            'slug' => 'premium-t-shirt-collection',
            'primary_category_id' => $category->id,
            'brand_id' => $brand->id,
            'description' => 'High quality cotton t-shirt with multiple color and size options',
            'short_description' => 'Premium T-Shirt - Available in multiple colors & sizes',
            'is_active' => true,
            'is_featured' => true,
            'min_price' => 199,
            'max_price' => 399,
        ]);

        // تعيين المنتج للفئة
        $product->categories()->attach($category->id, ['is_primary' => true]);

        // ✅ الخطوة 6: إنشاء variants بتوليفات مختلفة من colors و sizes
        $variantCombinations = [
            ['color' => 'red', 'size' => 'S', 'price' => 199, 'stock' => 50],
            ['color' => 'red', 'size' => 'M', 'price' => 199, 'stock' => 60],
            ['color' => 'red', 'size' => 'L', 'price' => 249, 'stock' => 40],
            ['color' => 'red', 'size' => 'XL', 'price' => 299, 'stock' => 30],
            
            ['color' => 'blue', 'size' => 'S', 'price' => 199, 'stock' => 55],
            ['color' => 'blue', 'size' => 'M', 'price' => 199, 'stock' => 65],
            ['color' => 'blue', 'size' => 'L', 'price' => 249, 'stock' => 45],
            ['color' => 'blue', 'size' => 'XL', 'price' => 299, 'stock' => 35],
            
            ['color' => 'green', 'size' => 'M', 'price' => 199, 'stock' => 50],
            ['color' => 'green', 'size' => 'L', 'price' => 249, 'stock' => 40],
        ];

        foreach ($variantCombinations as $combination) {
            $colorId = $colorIds[$combination['color']] ?? null;
            $sizeId = $sizeIds[$combination['size']] ?? null;

            if (!$colorId || !$sizeId) {
                continue;
            }

            // SKU: PRODUCT_COLOR_SIZE
            $sku = strtoupper($product->slug . '-' . $combination['color'] . '-' . $combination['size']);

            // هنا يتم إنشاء variant مع اختيار الألوان والأحجام
            $attributeValueIds = [$colorId, $sizeId];
            $hash = $this->variantService->makeAttributesHash($attributeValueIds);

            // تحقق من عدم وجود نفس التوليفة
            $existingVariant = Variant::query()
                ->where('product_id', $product->id)
                ->where('attributes_hash', $hash)
                ->first();

            if (!$existingVariant) {
                $this->variantService->create([
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'price' => $combination['price'],
                    'compare_at_price' => $combination['price'] + 100, // سعر المقارنة أعلى
                    'stock' => $combination['stock'],
                    'is_active' => true,
                ], $attributeValueIds);

                $this->command->info("✅ تم إنشاء variant: $sku");
            }
        }

        $this->command->info("✅ تم إنشاء المنتج '{$product->name}' مع " . count($variantCombinations) . " variants");
    }

    private function syncColorToAttributeValues(Color $color, string $hex): int
    {
        $slug = Str::slug($color->code, '_');
        $colorAttributes = CategoryAttribute::query()->where('code', 'color')->get();

        $valueId = null;
        foreach ($colorAttributes as $attribute) {
            $value = CategoryAttributeValue::query()->updateOrCreate(
                ['attribute_id' => $attribute->id, 'slug' => $slug],
                ['value' => $color->name, 'extra' => ['hex' => $hex]]
            );
            $valueId = $value->id;
        }

        return $valueId ?? 0;
    }

    private function syncSizeToAttributeValues(Size $size): int
    {
        $slug = Str::slug($size->code ?: $size->name, '_');
        $sizeAttributes = CategoryAttribute::query()->where('code', 'size')->get();

        $valueId = null;
        foreach ($sizeAttributes as $attribute) {
            $value = CategoryAttributeValue::query()->updateOrCreate(
                ['attribute_id' => $attribute->id, 'slug' => $slug],
                [
                    'value' => $size->name,
                    'extra' => [
                        'code' => $size->code,
                        'code_from' => $size->code_from,
                        'code_to' => $size->code_to,
                    ],
                ]
            );
            $valueId = $value->id;
        }

        return $valueId ?? 0;
    }
}
