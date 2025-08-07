<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cat;
use App\Models\CatTranslation;
use Illuminate\Support\Facades\DB;

class CatTableSeeder extends Seeder
{
    public function run(): void
    {
            $data = [
                [
                    'id' => 1,
                    'img' => 'img1.jpg',
                    'title' => 'التراث المادي',
                    'description' => 'المدن والقرى التاريخية، المواقع الأثرية، الملابس التراثية والتطريز الفلسطيني الشهير',
                    'children' => [
                        [
                            'id' => 2,
                            'title' => 'التراث المعماري',
                            'follow' => 1,
                            'children' => [
                                ['id' => 201, 'title' => 'المساجد'],
                                ['id' => 202, 'title' => 'الكنائس'],
                                ['id' => 203, 'title' => 'البيوت الطينية والحجرية'],
                                ['id' => 204, 'title' => 'الأقواس والعقود'],
                                ['id' => 205, 'title' => 'مقامات واماكن مقدسة'],
                                ['id' => 206, 'title' => 'الأسوار'],
                                ['id' => 207, 'title' => 'القلاع والأبراج'],
                                ['id' => 208, 'title' => 'الخانات والحمامات'],
                                ['id' => 209, 'title' => 'الآبار والبرك'],
                                ['id' => 210, 'title' => 'الحمامات'],
                            ]
                        ],
                        [
                            'id' => 3,
                            'title' => 'الزي التراثي',
                            'follow' => 1,
                            'children' => [
                                ['id' => 301, 'title' => 'الزي النسائي'],
                                ['id' => 302, 'title' => 'الزي الرجالي'],
                                ['id' => 303, 'title' => 'زي الأطفال'],
                                ['id' => 304, 'title' => 'زي المناسبات والأعياد'],
                            ]
                        ],
                        [
                            'id' => 4,
                            'title' => 'الحرف والصناعات',
                            'follow' => 1,
                            'children' => [
                                ['id' => 401, 'title' => 'التطريز الفلسطيني'],
                                ['id' => 402, 'title' => 'الفخار'],
                                ['id' => 403, 'title' => 'السلال المصنوعة من القش'],
                                ['id' => 404, 'title' => 'صابون نابلسي'],
                                ['id' => 405, 'title' => 'الزجاج اليدوي'],
                                ['id' => 406, 'title' => 'النسيج والسجاد اليدوي'],
                                ['id' => 407, 'title' => 'الطعام'],
                                ['id' => 408, 'title' => 'الخزف'],
                                ['id' => 409, 'title' => 'النسيج'],
                            ]
                        ],
                        [
                            'id' => 5,
                            'title' => 'الأدوات والأواني',
                            'follow' => 1,
                            'children' => [
                                ['id' => 501, 'title' => 'أواني المطبخ التقليدية'],
                                ['id' => 502, 'title' => 'أثاث البيت'],
                                ['id' => 503, 'title' => 'أدوات الزراعة'],
                                ['id' => 504, 'title' => 'أدوات البناء القديمة'],
                                ['id' => 505, 'title' => 'أدوات الصناعة'],
                                ['id' => 506, 'title' => 'أدوات الزينة'],
                                ['id' => 507, 'title' => 'أدوات موسيقية'],
                                ['id' => 508, 'title' => 'أدوات الطبابة'],
                            ]
                        ],
                        [
                            'id' => 6,
                            'title' => 'الأطباق الفلسطينية',
                            'follow' => 1,
                            'children' => [
                                ['id' => 601, 'title' => 'أطباق رئيسية'],
                                ['id' => 602, 'title' => 'الحلويات'],
                                ['id' => 603, 'title' => 'أطباق شعبية'],
                                ['id' => 604, 'title' => 'الخبز'],
                                ['id' => 605, 'title' => 'أطباق موسمية'],
                                ['id' => 606, 'title' => 'مشروبات'],
                            ]
                        ],
                        [
                            'id' => 7,
                            'title' => 'الطوابع والعملات والوثائق',
                            'follow' => 1,
                            'children' => [
                                ['id' => 701, 'title' => 'تصنيف الطوابع'],
                                ['id' => 702, 'title' => 'أنواع العملات'],
                                ['id' => 703, 'title' => 'أنواع الوثائق'],
                            ]
                        ],
                    ]
                ],
                [
                    'id' => 8,
                    'img' => 'img2.jpg',
                    'title' => 'التراث غير المادي',
                    'description' => 'العادات والتقاليد، الحكايات الشعبية، الأغاني والدبكة الفلسطينية، فنون الطهي',
                    'children' => [
                        [
                            'id' => 9,
                            'title' => 'الموسيقى والأغاني الشعبية',
                            'follow' => 2,
                            'children' => [
                                ['id' => 901, 'title' => 'الزجل، الشروقي، العتابا، الميجانا'],
                                ['id' => 902, 'title' => 'أغاني الأعراس'],
                                ['id' => 903, 'title' => 'أغاني العمل'],
                                ['id' => 904, 'title' => 'أغاني الحزن والرثاء'],
                                ['id' => 905, 'title' => 'أغاني المناسبات الدينية'],
                            ]
                        ],
                        [
                            'id' => 10,
                            'title' => 'الرقصات الشعبية',
                            'follow' => 2,
                            'children' => [
                                ['id' => 1001, 'title' => 'الدبكة الفلسطينية'],
                                ['id' => 1002, 'title' => 'السامر'],
                                ['id' => 1003, 'title' => 'الدحية'],
                            ]
                        ],
                        [
                            'id' => 11,
                            'title' => 'الحكايات والأساطير الشعبية',
                            'follow' => 2,
                            'children' => [
                                ['id' => 1101, 'title' => 'قصص الجدات'],
                                ['id' => 1102, 'title' => 'أساطير خيالية'],
                                ['id' => 1103, 'title' => 'أساطير دينية'],
                            ]
                        ],
                        [
                            'id' => 12,
                            'title' => 'الأمثال الشعبية',
                            'follow' => 2,
                            'children' => [
                                ['id' => 1201, 'title' => 'الحكمة والتجربة'],
                                ['id' => 1202, 'title' => 'الصبر والرضا'],
                                ['id' => 1203, 'title' => 'الكرم والجود'],
                                ['id' => 1204, 'title' => 'المرأة والزواج'],
                                ['id' => 1205, 'title' => ' الفقر والغنى'],
                                ['id' => 1206, 'title' => 'الحظ والقدر'],
                                ['id' => 1207, 'title' => 'العمل والكسل'],
                                ['id' => 1208, 'title' => 'العلاقات الاجتماعية'],
                                ['id' => 1209, 'title' => 'الغدر والخداع'],
                                ['id' => 1210, 'title' => 'الفخر والانتماء'],
                            ]
                        ],
                        [
                            'id' => 13,
                            'title' => 'المعتقدات والعادات',
                            'follow' => 2,
                            'children' => [
                                ['id' => 1301, 'title' => 'معتقدات الحسد والبركة'],
                                ['id' => 1302, 'title' => 'العادات الاجتماعية'],
                                ['id' => 1303, 'title' => 'العادات الدينية'],
                                ['id' => 1304, 'title' => 'العادات الموسمية'],
                                ['id' => 1305, 'title' => 'الرموز المستخدمة'],
                                ['id' => 1306, 'title' => 'العادات العشائرية'],
                            ]
                        ],
                        [
                            'id' => 14,
                            'title' => 'الطب الشعبي',
                            'follow' => 2,
                            'children' => [
                                ['id' => 1401, 'title' => 'العلاج بالأعشاب'],
                                ['id' => 1402, 'title' => 'الكيّ، الحجامة'],
                                ['id' => 1403, 'title' => 'الداية وطرق التوليد'],
                            ]
                        ],
                        [
                            'id' => 15,
                            'title' => 'اللهجات المحلية',
                            'follow' => 2,
                            'children' => [
                                ['id' => 1501, 'title' => 'لهجات جغرافية'],
                                ['id' => 1502, 'title' => 'تعبيرات فريدة'],
                                ['id' => 1503, 'title' => 'الأدعية والتعابير اليومية'],
                            ]
                        ],
                        [
                            'id' => 16,
                            'title' => 'الألعاب الشعبية',
                            'follow' => 2,
                            'children' => [
                                ['id' => 1601, 'title' => 'ألعاب الأولاد'],
                                ['id' => 1602, 'title' => 'ألعاب البنات'],
                                ['id' => 1603, 'title' => 'ألعاب كبار السن'],
                            ]
                        ],
                    ]
                ],
                [
                    'id' => 17,
                    'img' => 'img3.jpg',
                    'title' => 'التراث المادي',
                    'description' => 'المواقع الطبيعية ذات الأهمية التاريخية والثقافية، أشجار الزيتون المعمرة',
                    'children' => [
                        // فارغ حالياً – يمكن ملؤه لاحقاً
                    ]
                ],
                [
                    'id' => 19,
                    'title' => 'مقالات وأبحاث',
                    'img' => 'img3.jpg',
                    'description' => 'المواقع الطبيعية ذات الأهمية التاريخية والثقافية، أشجار الزيتون المعمرة',
                    'follow' => 3,
                    'children' => [
                        ['id' => 1901, 'title' => 'مقالات ثقافية'],
                        ['id' => 1902, 'title' => 'مقالات تحليلية'],
                        ['id' => 1903, 'title' => 'مقالات تاريخية'],
                    ]
                ],
            ];


        DB::transaction(function () use ($data) {
            foreach ($data as $i => $main) {
                $mainCat = Cat::create([
                    'id' => $main['id'],
                    'img' => $main['img'] ?? null,
                    'follow' => $main['follow'] ?? 1,
                    'status' => 1,
                    'ord' => $i,
                ]);

                CatTranslation::create([
                    'cat_id' => $mainCat->id,
                    'title' => $main['title'],
                    'description' => $main['description'] ?? null,
                    'locale' => 'ar',
                ]);

                if (!empty($main['children'])) {
                    foreach ($main['children'] as $j => $sub) {
                        $subCat = Cat::create([
                            'id' => $sub['id'],
                            'status' => 1,
                            'ord' => $j,
                            'follow' => $sub['follow'] ?? 0,
                            'parent_id' => $mainCat->id,
                        ]);

                        CatTranslation::create([
                            'cat_id' => $subCat->id,
                            'title' => $sub['title'],
                            'locale' => 'ar',
                        ]);

                        if (!empty($sub['children'])) {
                            foreach ($sub['children'] as $k => $child) {
                                $childCat = Cat::create([
                                    'id' => $child['id'],
                                    'status' => 1,
                                    'ord' => $k,
                                    'parent_id' => $subCat->id,
                                    'img' => $child['img'] ?? null,
                                ]);

                                CatTranslation::create([
                                    'cat_id' => $childCat->id,
                                    'title' => $child['title'],
                                    'description' => $child['description'] ?? null,
                                    'locale' => 'ar',
                                ]);
                            }
                        }
                    }
                }
            }
        });

    }
}
