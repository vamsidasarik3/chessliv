<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NewsSource;
use App\Models\Category;

class NewsSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = config('chess_sources.default_sources', []);

        foreach ($sources as $sourceData) {
            $defaultCat = null;
            if (!empty($sourceData['default_category'])) {
                $defaultCat = Category::where('slug', $sourceData['default_category'])->first();
            }

            NewsSource::firstOrCreate(
                ['slug' => $sourceData['slug']],
                [
                    'name' => $sourceData['name'],
                    'feed_url' => $sourceData['feed_url'],
                    'site_url' => $sourceData['site_url'] ?? null,
                    'description' => $sourceData['description'] ?? null,
                    'default_category_id' => $defaultCat ? $defaultCat->id : null,
                    'language' => $sourceData['language'] ?? 'en',
                    'is_active' => $sourceData['is_active'] ?? true,
                    'fetch_frequency_minutes' => $sourceData['fetch_frequency_minutes'] ?? 60,
                ]
            );
        }
    }
}
