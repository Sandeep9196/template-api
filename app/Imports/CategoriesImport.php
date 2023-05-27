<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use App\Models\Translation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CategoriesImport implements ToCollection, WithBatchInserts, WithChunkReading
{
    private $rowNumber = 0;
    private $catNamePostions = [];
    private $subNamePostions = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        $notUploadData = [];
        $totalFailRecords = 0;
        $totalSuccessRecords = 0;

        foreach ($rows as $row) {
            $result = $this->importCategory($row, $this->rowNumber);
            if (!empty($result) && $this->rowNumber != 0) {
                $notUploadData[] = $result;
                array_push($notUploadData, $row);
                $totalFailRecords++;
            } elseif ($this->rowNumber != 0) {
                $totalSuccessRecords++;
            }
            $this->rowNumber++;
        }
        $this->notUploadData = $notUploadData;
        $this->totalFailRecords = $totalFailRecords;
        $this->totalSuccessRecords = $totalSuccessRecords;
    }

    public function split($data)
    {
        $arrayData = [];
        if (!empty($data)) {
            $arrayData = explode('_', $data);
        }
        return $arrayData;
    }
    public function importCategory($row, $rowNumber)
    {
        /*  Get all Columns Position from the Excel  */
        $this->getPositions($row);

        if ($rowNumber !== 0) { /*  Skipping the first row in Excel sheet  */
            if (!empty(session('cat_slug'))) {

                 if(empty($row[session('cat_slug')])){
                    $row['fail_reason'] = 'Category Slug is missing';
                    return $row;
                 }

                /*  Check Slug already exist in Category  */
                $category = Category::where('slug', $row[session('cat_slug')])->first();

                if (empty($category)) {
                    $categoryId = $this->storeCategory($row);
                } else {
                    $categoryId = $category->id;
                }
                if (!empty(session('sub_slug'))) {

                    if(empty($row[session('sub_slug')])){
                        $row['fail_reason'] = 'Sub Category Slug is missing';
                        return $row;
                     }


                    /*  Check Slug already exist in Sub Category  */
                    $subCategory = SubCategory::where('slug', $row[session('sub_slug')])->first();
                    if (empty($subCategory)) {
                        $subCategoryId  = $this->storeSubCategory($row, $categoryId);
                    } else {
                        $subCategoryId = $subCategory->id;
                    }

                    /*  Check if Parent is available in excel  */
                    if (!empty($row[session('parent_slug')])) {
                        $subCategory = [
                            'category_id' =>  $categoryId,
                            'parent_sub_category_id' =>  $subCategoryId,
                            'name' => $row[session('parent_name')]
                        ];


                        if (!empty($row[session('parent_slug')])) {
                            $subCategory = SubCategory::where('slug', $row[session('parent_slug')])->first();
                            if (empty($subCategory)) {
                                $subCategory = SubCategory::create(['slug', $row[session('parent_slug')], 'category_id' => $subCategory->category_id]);
                            }
                            $subCategoryData = ['parent_sub_category_id' =>  $subCategory->id, 'name' => $row[session('parent_name')], 'category_id' => $subCategory->category_id];
                            $parentData = SubCategory::where($subCategoryData)->first();

                            if (empty($parentData)) {
                                SubCategory::insert($subCategoryData);
                            } else {
                                $parentData->update($subCategoryData);
                            }
                        }
                    }
                } else {
                    $row['fail_reason'] = 'Sub Category Slug is missing';
                    return $row;
                }

                /*  Check Translation */
                $this->storeTranslation($row, $categoryId, $subCategoryId);
            }
        }
    }
    public function storeCategory($row)
    {
        $categoryData = [
            'name' => !empty($row[session('cat_name_postion')]) ? $row[session('cat_name_postion')] : '',
            'description' => !empty($row[session('cat_desc')]) ? $row[session('cat_desc')] : '',
            'slug' => !empty($row[session('cat_slug')]) ? $row[session('cat_slug')] : '',
            'status' => !empty($row[session('cat_status')]) ? $row[session('cat_status')] : config('constants.STATUS_INACTIVE'),
        ];
        return Category::create($categoryData)->id;
    }
    public function storeSubCategory($row, $categoryID)
    {
        $subCategoryData = [
            'category_id' => $categoryID,
            'name' => !empty($row[session('sub_name_postion')]) ? $row[session('sub_name_postion')] : '',
            'description' => !empty($row[session('sub_desc')]) ? $row[session('sub_desc')] : '',
            'slug' => !empty($row[session('sub_slug')]) ? $row[session('sub_slug')] : '',
            'status1' => !empty($row[session('sub_status')]) ? $row[session('sub_status')] : config('constants.STATUS_INACTIVE'),
        ];
        $subCategory = SubCategory::where(['slug' => $subCategoryData['slug'], 'status' => config('constants.STATUS_ACTIVE')])->first();
        if (!empty($subCategory)) {
            $subCategory->update($subCategoryData);
        } else {
            $subCategory =   SubCategory::create($subCategoryData);
        }
        return $subCategory->id;
    }
    public function storeTranslation($row, $categoryId, $subCategoryId)
    {
        $category = Category::find($categoryId);
        $subCategory = SubCategory::find($subCategoryId);
        if (!empty(session('cat_name'))) {
            foreach (session('cat_name') as $value) {
                $langID = $this->getLanguageID($value['locale']);
                $transData = [
                    'translationable_id' => $categoryId,
                    'translationable_type' => get_class($category),
                    'language_id' => $langID,
                    'field_name' => 'name',
                    'translation' => $row[$value['position']],
                ];
                $translation = Translation::where(['translationable_id' => $categoryId, 'language_id' => $langID, 'translationable_type' => get_class($category)])->first();

                if (!empty($translation)) {
                    Translation::where(['id' => $translation->id])->update($transData);
                } else {
                    Translation::create($transData);
                }
            }
        }


        if (!empty(session('sub_name'))) {
            foreach (session('sub_name') as $values) {
                $langID = $this->getLanguageID($values['locale']);
                $transData = [
                    'translationable_id' => $subCategoryId,
                    'translationable_type' => get_class($subCategory),
                    'language_id' => $langID,
                    'field_name' => 'name',
                    'translation' => $row[$values['position']],
                ];
                $translation = Translation::where(['translationable_id' => $subCategoryId, 'language_id' => $langID, 'translationable_type' => get_class($subCategory)])->first();

                if (!empty($translation)) {
                    Translation::where(['id' => $translation->id])->update($transData);
                } else {
                    Translation::create($transData);
                }
            }
        }
    }

    public function getLanguageID($locale)
    {
        $language =  Language::where('locale', $locale)->first();
        if (empty($language)) {
            $language = Language::create(['locale' => $locale]);
        }
        return $language->id;
    }

    public function batchSize(): int
    {
        return 1000;
    }
    public function chunkSize(): int
    {
        return 1000;
    }
    public function getPositions($row)
    {
        if (!empty($row)) {

            for ($key = 1; $key < count($row); $key++) {
                $arrayData =  $this->split($row[$key]);
                if (!empty($arrayData)) {
                    if ($arrayData[0] == 'cat') {


                        switch ($arrayData[1]) {
                            case  'slug':
                                session(['cat_slug' => $key]);
                                break;
                            case 'name':
                                $catName['position'] = $key;
                                $catName['locale'] = $arrayData[2];
                                $this->catNamePostions[] = $catName;
                                if (!empty($arrayData[2]) && $arrayData[2] == 'en') {
                                    session(['cat_name_postion' => $key]);
                                }

                                break;
                            case  'desc':
                                session(['cat_desc' => $key]);
                                break;
                            case  'status':
                                session(['cat_status' => $key]);
                                break;
                            default:
                                break;
                        }
                        session(['cat_name' => $this->catNamePostions]);
                    } elseif ($arrayData[0] == 'sub') {




                        switch ($arrayData[1]) {
                            case  'slug':
                                session(['sub_slug' => $key]);
                                break;
                            case 'name':
                                $subName['position'] = $key;
                                $subName['locale'] = $arrayData[2];
                                $this->subNamePostions[] = $subName;
                                if (!empty($arrayData[2]) && $arrayData[2] == 'en') {
                                    session(['sub_name_postion' => $key]);
                                }

                                break;
                            case  'desc':
                                session(['sub_desc' => $key]);
                                break;
                            case  'status':
                                session(['sub_status' => $key]);
                                break;
                            default:
                                break;
                        }

                        session(['sub_name' => $this->subNamePostions]);
                    } elseif ($arrayData[0] == 'parent') {
                        switch ($arrayData[1]) {
                            case 'name':
                                session(['parent_name' => $key]);
                                break;
                            case 'slug':
                                session(['parent_slug' => $key]);
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        }
        return true;
    }
}
