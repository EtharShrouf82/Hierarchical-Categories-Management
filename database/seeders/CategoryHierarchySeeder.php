<?php

namespace Database\Seeders;

use App\Models\Cat;
use App\Models\CatTranslation;
use Illuminate\Database\Seeder;

class CategoryHierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Don't truncate existing data, just add our hierarchical categories
        // Create root categories with higher IDs to avoid conflicts
        $palestine = Cat::create([
            'id' => 1000,
            'status' => true,
            'ord' => 1,
            'user_id' => 1,
            'parent_id' => null,
        ]);

        $jordan = Cat::create([
            'id' => 1001,
            'status' => true,
            'ord' => 2,
            'user_id' => 1,
            'parent_id' => null,
        ]);

        $syria = Cat::create([
            'id' => 1002,
            'status' => true,
            'ord' => 3,
            'user_id' => 1,
            'parent_id' => null,
        ]);

        // Create translations for root categories
        $palestine->translations()->create([
            'title' => 'Palestine',
            'locale' => 'en'
        ]);

        $jordan->translations()->create([
            'title' => 'Jordan',
            'locale' => 'en'
        ]);

        $syria->translations()->create([
            'title' => 'Syria',
            'locale' => 'en'
        ]);

        // Create Hebron under Palestine
        $hebron = Cat::create([
            'id' => 1003,
            'status' => true,
            'ord' => 1,
            'user_id' => 1,
            'parent_id' => $palestine->id,
        ]);

        $hebron->translations()->create([
            'title' => 'Hebron',
            'locale' => 'en'
        ]);

        // Create Nuba under Hebron
        $nuba = Cat::create([
            'id' => 1004,
            'status' => true,
            'ord' => 1,
            'user_id' => 1,
            'parent_id' => $hebron->id,
        ]);

        $nuba->translations()->create([
            'title' => 'Nuba',
            'locale' => 'en'
        ]);

        // Create Nuba School under Nuba
        $nubaSchool = Cat::create([
            'id' => 1005,
            'status' => true,
            'ord' => 1,
            'user_id' => 1,
            'parent_id' => $nuba->id,
        ]);

        $nubaSchool->translations()->create([
            'title' => 'Nuba School',
            'locale' => 'en'
        ]);

        // Create Amman under Jordan
        $amman = Cat::create([
            'id' => 1006,
            'status' => true,
            'ord' => 1,
            'user_id' => 1,
            'parent_id' => $jordan->id,
        ]);

        $amman->translations()->create([
            'title' => 'Amman',
            'locale' => 'en'
        ]);

        // Create Alepo under Syria
        $alepo = Cat::create([
            'id' => 1007,
            'status' => true,
            'ord' => 1,
            'user_id' => 1,
            'parent_id' => $syria->id,
        ]);

        $alepo->translations()->create([
            'title' => 'Alepo',
            'locale' => 'en'
        ]);

        // Create Alepo 2 under Alepo
        $alepo2 = Cat::create([
            'id' => 1008,
            'status' => true,
            'ord' => 1,
            'user_id' => 1,
            'parent_id' => $alepo->id,
        ]);

        $alepo2->translations()->create([
            'title' => 'Alepo 2',
            'locale' => 'en'
        ]);

        // Create Alepo 3 under Alepo 2
        $alepo3 = Cat::create([
            'id' => 1009,
            'status' => true,
            'ord' => 1,
            'user_id' => 1,
            'parent_id' => $alepo2->id,
        ]);

        $alepo3->translations()->create([
            'title' => 'Alepo 3',
            'locale' => 'en'
        ]);

        // Create Alepo 4 under Alepo 3
        $alepo4 = Cat::create([
            'id' => 1010,
            'status' => false, // Inactive for demonstration
            'ord' => 1,
            'user_id' => 1,
            'parent_id' => $alepo3->id,
        ]);

        $alepo4->translations()->create([
            'title' => 'Alepo 4',
            'locale' => 'en'
        ]);

        // Add some additional categories for testing
        $this->createAdditionalCategories($palestine, $jordan, $syria);
    }

    private function createAdditionalCategories($palestine, $jordan, $syria)
    {
        // Additional cities under Palestine
        $cities = ['Jerusalem', 'Bethlehem', 'Nazareth'];
        foreach ($cities as $index => $city) {
            $cat = Cat::create([
                'status' => true,
                'ord' => $index + 2, // Start from 2 since Hebron is 1
                'user_id' => 1,
                'parent_id' => $palestine->id,
            ]);

            $cat->translations()->create([
                'title' => $city,
                'locale' => 'en'
            ]);
        }

        // Additional cities under Jordan
        $jordanCities = ['Zarqa', 'Irbid', 'Aqaba'];
        foreach ($jordanCities as $index => $city) {
            $cat = Cat::create([
                'status' => true,
                'ord' => $index + 2, // Start from 2 since Amman is 1
                'user_id' => 1,
                'parent_id' => $jordan->id,
            ]);

            $cat->translations()->create([
                'title' => $city,
                'locale' => 'en'
            ]);
        }

        // Additional cities under Syria
        $syriaCities = ['Damascus', 'Homs', 'Latakia'];
        foreach ($syriaCities as $index => $city) {
            $cat = Cat::create([
                'status' => true,
                'ord' => $index + 2, // Start from 2 since Alepo is 1
                'user_id' => 1,
                'parent_id' => $syria->id,
            ]);

            $cat->translations()->create([
                'title' => $city,
                'locale' => 'en'
            ]);
        }
    }
}
