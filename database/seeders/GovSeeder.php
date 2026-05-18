<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GovSeeder extends Seeder
{
    public function run(): void
    {
        $file = base_path('govs.json');
        if (!file_exists($file)) {
            $file = database_path('seeders/data/govs.json');
        }

        if (!file_exists($file)) {
            return;
        }

        $payload = json_decode(file_get_contents($file), true);
        if (!is_array($payload)) {
            return;
        }

        $now = now();
        $rows = collect($payload)
            ->map(function (array $gov) use ($now) {
                return [
                    'id' => (int) ($gov['id'] ?? 0),
                    'name_ar' => $gov['governorate_name_ar'] ?? $gov['name_ar'] ?? null,
                    'name_en' => $gov['governorate_name_en'] ?? $gov['name_en'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->filter(function (array $gov) {
                return !empty($gov['id']) && !empty($gov['name_ar']) && !empty($gov['name_en']);
            })
            ->values()
            ->all();

        if (empty($rows)) {
            return;
        }

        DB::table('govs')->upsert(
            $rows,
            ['id'],
            ['name_ar', 'name_en', 'updated_at']
        );
    }
}
