<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Api\Ecommerce\ShipmentWay;

class ShipmentWaySeeder extends Seeder
{
    public function run(): void
    {
        $ways = [
            [
                'title_en' => 'Small Car',
                'title_ar' => 'سيارة صغيرة',
                'des_en' => 'Small delivery car for light shipments',
                'des_ar' => 'سيارة توصيل صغيرة للشحنات الخفيفة',
                'capacity' => 3,
                'status' => 'active',
            ],
            [
                'title_en' => 'Medium Car',
                'title_ar' => 'سيارة متوسطة',
                'des_en' => 'Medium delivery car for standard shipments',
                'des_ar' => 'سيارة توصيل متوسطة للشحنات العادية',
                'capacity' => 8,
                'status' => 'active',
            ],
            [
                'title_en' => 'Large Car',
                'title_ar' => 'سيارة كبيرة',
                'des_en' => 'Large delivery car for bulky shipments',
                'des_ar' => 'سيارة توصيل كبيرة للشحنات الضخمة',
                'capacity' => 15,
                'status' => 'active',
            ],
            [
                'title_en' => 'Truck',
                'title_ar' => 'شاحنة',
                'des_en' => 'Truck for very large shipments',
                'des_ar' => 'شاحنة للشحنات الكبيرة جداً',
                'capacity' => 30,
                'status' => 'active',
            ],
        ];

        foreach ($ways as $wayData) {
            $way = ShipmentWay::firstOrCreate(
                ['capacity' => $wayData['capacity']],
                ['status' => $wayData['status']]
            );

            $way->translateOrNew('en')->title = $wayData['title_en'];
            $way->translateOrNew('en')->des = $wayData['des_en'];
            $way->translateOrNew('ar')->title = $wayData['title_ar'];
            $way->translateOrNew('ar')->des = $wayData['des_ar'];
            $way->save();
        }
    }
}