<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Api\Ecommerce\ShipmentWay;
use App\Models\Api\Ecommerce\ShipmentZone;
use App\Models\Api\Ecommerce\ShipmentWayZone;

class ShipmentWayZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zone = ShipmentZone::where('price', 25)->first();
        if (!$zone) {
            $zone = ShipmentZone::first();
        }

        if (!$zone) {
            $city = ShipmentCity::first();
            if (!$city) {
                $city = ShipmentCity::create(['status' => 'active']);
                $city->translateOrNew('en')->title = 'Default City';
                $city->translateOrNew('en')->des = 'Default shipping city';
                $city->translateOrNew('ar')->title = 'مدينة افتراضية';
                $city->translateOrNew('ar')->des = 'مدينة شحن افتراضية';
                $city->save();
            }

            $zone = ShipmentZone::create(['city_id' => $city->id, 'price' => 25, 'status' => 'active']);
            $zone->translateOrNew('en')->title = 'Default Zone';
            $zone->translateOrNew('en')->des = 'Default shipping zone';
            $zone->translateOrNew('ar')->title = 'منطقة افتراضية';
            $zone->translateOrNew('ar')->des = 'منطقة شحن افتراضية';
            $zone->save();
        }

        $ways = ShipmentWay::where('status', 'active')->get();

        if ($ways->isEmpty()) {
            // Seed default ways if none exist
            $this->call(ShipmentWaySeeder::class);
            $ways = ShipmentWay::where('status', 'active')->get();
        }

        $prices = [
            'Small Car' => 30,
            'Medium Car' => 50,
            'Large Car' => 80,
            'Truck' => 150,
        ];

        foreach ($ways as $way) {
            $title = $way->translate('en')?->title ?? $way->translate('ar')?->title ?? null;
            $price = $prices[$title] ?? 50;

            ShipmentWayZone::updateOrCreate(
                ['way_id' => $way->id, 'zone_id' => $zone->id],
                [
                    'price' => $price,
                    'status' => 'active',
                ]
            );
        }
    }
}