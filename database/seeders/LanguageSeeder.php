<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [
            [
                'id' => 1,
                'name' => 'English',
                'locale' => 'en',
                'locale_web' => 'en',
                'translation' => [
                    [
                        'language_id' => 1,
                        'field_name' => 'name',
                        'translation'=> 'English',
                        'purpose'=> 1
                    ],
                    [
                        'language_id' => 1,
                        'field_name' => 'name',
                        'translation'=> 'Chinese',
                        'purpose'=> 2
                    ],
                    [
                        'language_id' => 1,
                        'field_name' => 'name',
                        'translation'=> 'Khmer',
                        'purpose'=> 3
                    ],
                    // [
                    //     'language_id' => 1,
                    //     'field_name' => 'name',
                    //     'translation'=> 'Vietnamese',
                    //     'purpose'=> 4
                    // ],
                    // [
                    //     'language_id' => 1,
                    //     'field_name' => 'name',
                    //     'translation'=> 'Thai',
                    //     'purpose'=> 5
                    // ],
                ]
            ],
            [
                'id' => 2,
                'name' => '中文',
                'locale' => 'zh-Hans',
                'locale_web' => 'ch',
                'translation' => [
                    [
                        'language_id' => 2,
                        'field_name' => 'name',
                        'translation'=> '英语',
                        'purpose'=> 1
                    ],
                    [
                        'language_id' => 2,
                        'field_name' => 'name',
                        'translation'=> '中文'
                        ,
                        'purpose'=> 2
                    ],
                    [
                        'language_id' => 2,
                        'field_name' => 'name',
                        'translation'=> '高棉语',
                        'purpose'=> 3
                    ],
                    [
                        'language_id' => 2,
                        'field_name' => 'name',
                        'translation'=> '越南语',
                        'purpose'=> 4
                    ],
                    [
                        'language_id' => 2,
                        'field_name' => 'name',
                        'translation'=> '泰语',
                        'purpose'=> 5
                    ],
                ]
            ],
            [
                'id' => 3,
                'name' => 'Khmer',
                'locale' => 'km',
                'locale_web' => 'kh',
                'translation' => [
                    [
                        'language_id' => 3,
                        'field_name' => 'name',
                        'translation'=> 'អង់គ្លេស',
                        'purpose'=> 1
                    ],
                    [
                        'language_id' => 3,
                        'field_name' => 'name',
                        'translation'=> 'ចិន',
                        'purpose'=> 2
                    ],
                    [
                        'language_id' => 3,
                        'field_name' => 'name',
                        'translation'=> 'ខ្មែរ',
                        'purpose'=> 3
                    ],
                    // [
                    //     'language_id' => 3,
                    //     'field_name' => 'name',
                    //     'translation'=> 'វៀតណាម',
                    //     'purpose'=> 4
                    // ],
                    // [
                    //     'language_id' => 3,
                    //     'field_name' => 'name',
                    //     'translation'=> 'ថៃ',
                    //     'purpose'=> 5
                    // ],
                ]
            ],
            // [
            //     'id' => 4,
            //     'name' => 'Vietnamese',
            //     'locale' => 'vi-VN',
            //     'locale_web' => 'vt',
            //     'translation' => [
            //         [
            //             'language_id' => 4,
            //             'field_name' => 'name',
            //             'translation'=> 'Tiếng Anh',
            //             'purpose'=> 1
            //         ],
            //         [
            //             'language_id' => 4,
            //             'field_name' => 'name',
            //             'translation'=> 'người Trung Quốc',
            //             'purpose'=> 2
            //         ],
            //         [
            //             'language_id' => 4,
            //             'field_name' => 'name',
            //             'translation'=> 'khmer',
            //             'purpose'=> 3
            //         ],
            //         [
            //             'language_id' => 4,
            //             'field_name' => 'name',
            //             'translation'=> 'Tiếng Việt',
            //             'purpose'=> 4
            //         ],
            //         [
            //             'language_id' => 4,
            //             'field_name' => 'name',
            //             'translation'=> 'tiếng Thái',
            //             'purpose'=> 5
            //         ],
            //     ]
            // ],
            // [
            //     'id' => 5,
            //     'name' => 'Thai',
            //     'locale' => 'th',
            //     'locale_web' => 'th',
            //     'translation' => [
            //         [
            //             'language_id' => 5,
            //             'field_name' => 'name',
            //             'translation'=> 'ภาษาอังกฤษ',
            //             'purpose'=> 1
            //         ],
            //         [
            //             'language_id' => 5,
            //             'field_name' => 'name',
            //             'translation'=> 'ชาวจีน',
            //             'purpose'=> 2
            //         ],
            //         [
            //             'language_id' => 5,
            //             'field_name' => 'name',
            //             'translation'=> 'เขมร',
            //             'purpose'=> 3
            //         ],
            //         [
            //             'language_id' => 5,
            //             'field_name' => 'name',
            //             'translation'=> 'เวียตนาม',
            //             'purpose'=> 4
            //         ],
            //         [
            //             'language_id' => 5,
            //             'field_name' => 'name',
            //             'translation'=> 'แบบไทย',
            //             'purpose'=> 5
            //         ],
            //     ]
            // ],
        ];
        foreach ($languages as $language) {
            $translation = $language['translation'];
            unset($language['translation']);
            // filters
            $lang = Language::updateOrcreate(['id' => $language['id']], $language);
            //save name translation
            saveTranslation($lang,'translates',$translation,'name');

        }
    }
}
