<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Company;
use App\Models\Configure;
use App\Models\File;
use App\Models\TemplateDetail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use stdClass;

class TemplateDetailService
{

    public function store(array $data)
    {

        try {
            TemplateDetail::where(['status' => 'active'])->update(['status' => 'inactive']);
            $companyId = '';
            $addressId = '';
            if (!empty($data['company_info']['address'])) {
                $addressData = [
                    "city_id" => !empty($data['company_info']['address']['city_id']) ? $data['company_info']['address']['city_id'] : NULL,
                    "state_id" => !empty($data['company_info']['address']['state_id']) ? $data['company_info']['address']['state_id'] : NULL,
                    "country_id" => !empty($data['company_info']['address']['country_id']) ? $data['company_info']['address']['country_id'] : NULL,
                    "zipcode" => !empty($data['company_info']['address']['zipcode']) ? $data['company_info']['address']['zipcode'] : '',
                ];
                $address = Address::create($addressData);
                $addressId = $address->id;
                if (!empty($data['company_info']['address']['address_line'])) {
                    saveTranslation($address, 'translations', $data['company_info']['address']['address_line'], 'address_line');
                }
                if (!empty($data['company_info']['address']['address_line_2'])) {
                    saveTranslation($address, 'translations', $data['company_info']['address']['address_line_2'], 'address_line_2');
                }
            }

            if (!empty($data['company_info']['company_name'])) {
                $companyData = [
                    "info_email" => !empty($data['company_info']['info_email']) ? $data['company_info']['info_email'] : '',
                    "support_email" => !empty($data['company_info']['support_email']) ? $data['company_info']['support_email'] : '',
                    "contact_number" => !empty($data['company_info']['contact_number']) ? $data['company_info']['contact_number'] : '',
                    "address_id" => !empty($addressId) ? $addressId : ''
                ];
                $company   = Company::create($companyData);
                $companyId  = $company->id;

                if (!empty($data['company_info']['company_name'])) {
                    saveTranslation($company, 'translations', $data['company_info']['company_name'], 'company_name');
                }
                if (!empty($data['company_info']['copy_right'])) {
                    saveTranslation($company, 'translations', $data['company_info']['copy_right'], 'copy_right');
                }
            }

            $templateData = [
                "company_id" => !empty($companyId) ? $companyId : '',
                "template_id" => !empty($data['template_id']) ? $data['template_id'] : '',
                "theme" => !empty($data['theme']) ? $data['theme'] : '',
                "banner_style" => !empty($data['banner_style']) ? $data['banner_style'] : ''
            ];
            $templateDetail = TemplateDetail::create($templateData);

            if (!empty($data['website_title'])) {
                saveTranslation($templateDetail, 'translations', $data['website_title'], 'website_title');
            }
            if (!empty($data['website_description'])) {
                saveTranslation($templateDetail, 'translations', $data['website_description'], 'website_description');
            }

            if (!empty($data['website_logo'])) {
                File::where(['fileable_id' => $templateDetail->id, 'fileable_type' => TemplateDetail::class])->delete();
                saveFiles($templateDetail, 'websiteLogo', $data['website_logo']);
            }
            if (!empty($data['h5_logo'])) {
                saveFiles($templateDetail, 'h5Logo', $data['h5_logo'], ['purpose' => 11]);
            }

            if (!empty($data['social'])) {
                foreach ($data['social'] as $social) {
                    if (!empty($social['image'])) {
                        saveFiles($templateDetail, 'socialLogo', $social['image'], ['purpose' => $social['purpose']]);
                    }
                    $socialDatas[] =  [
                        'name' => !empty($social['name']) ? $social['name'] : '',
                        'url' => !empty($social['url']) ? $social['url'] : '',
                        'status' => !empty($social['status']) ? $social['status'] : 'inactive',
                        'purpose' => !empty($social['purpose']) ? $social['purpose'] : '0',
                    ];
                }
                $socialData = [
                    'configurable_id' => $templateDetail->id,
                    'configurable_type' => TemplateDetail::class,
                    'data' => json_encode($socialDatas),
                    'created_at' => Carbon::now()
                ];
                Configure::create($socialData);
            }

            return response()->json([
                'messages' => ['Template Details created successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function update($template, array $data): JsonResponse
    {
        try {

            DB::transaction(function () use ($template, $data) {
                TemplateDetail::where(['status' => 'active'])->update(['status' => 'inactive']);
                $companyId = $template->company_id;
                $addressId = '';

                if (!empty($data['company_info']['company_name'])) {

                    $companyData = [

                        "info_email" => !empty($data['company_info']['info_email']) ? $data['company_info']['info_email'] : '',
                        "support_email" => !empty($data['company_info']['support_email']) ? $data['company_info']['support_email'] : '',
                        "contact_number" => !empty($data['company_info']['contact_number']) ? $data['company_info']['contact_number'] : ''
                    ];
                    if (!empty($companyId)) {

                        Company::whereId($companyId)->update($companyData);
                        $company  = Company::find($companyId);
                    } else {
                        $company = Company::create($companyData);
                        $companyId = $company->id;
                    }
                    if (!empty($data['company_info']['company_name'])) {
                        saveTranslation($company, 'translations', $data['company_info']['company_name'], 'company_name');
                    }
                    if (!empty($data['company_info']['copy_right'])) {
                        saveTranslation($company, 'translations', $data['company_info']['copy_right'], 'copy_right');
                    }
                }

                if (!empty($data['company_info']['address'])) {
                    $addressData = [
                        "city_id" => !empty($data['company_info']['address']['city_id']) ? $data['company_info']['address']['city_id'] : NULL,
                        "state_id" => !empty($data['company_info']['address']['state_id']) ? $data['company_info']['address']['state_id'] : NULL,
                        "country_id" => !empty($data['company_info']['address']['country_id']) ? $data['company_info']['address']['country_id'] : NULL,
                        "zipcode" => !empty($data['company_info']['address']['zipcode']) ? $data['company_info']['address']['zipcode'] : '',
                    ];
                    if (!empty($companyId)) {
                        $company = Company::whereId($companyId)->first();
                        $addressId = $company->address_id;
                        if (!empty($addressId)) {
                            Address::whereId($addressId)->update($addressData);
                            $address  = Address::find($addressId);
                        } else {
                            $address = Address::create($addressData);
                            $addressId =    $address->id;
                        }

                        if (!empty($data['company_info']['address']['address_line'])) {
                            saveTranslation($address, 'translations', $data['company_info']['address']['address_line'], 'address_line');
                        }
                        if (!empty($data['company_info']['address']['address_line_2'])) {
                            saveTranslation($address, 'translations', $data['company_info']['address']['address_line_2'], 'address_line_2');
                        }
                    }
                }

                $templateData = [
                    "company_id" => !empty($companyId) ? $companyId : '',
                    "template_id" => !empty($data['template_id']) ? $data['template_id'] : '',
                    "theme" => !empty($data['theme']) ? $data['theme'] : '',
                    "banner_style" => !empty($data['banner_style']) ? $data['banner_style'] : '',
                    "status" => !empty($data['status']) ? $data['status'] : 'active'

                ];
                if (!empty($data['website_logo'])) {
                    saveFiles($template, 'websiteLogo', $data['website_logo']);
                }
                if (!empty($data['h5_logo'])) {
                    saveFiles($template, 'h5Logo', $data['h5_logo'], ['purpose' => 11]);
                }

                TemplateDetail::whereId($template->id)->update($templateData);
                $templateDetail = TemplateDetail::find($template->id);

                if (!empty($data['website_title'])) {
                    saveTranslation($templateDetail, 'translations', $data['website_title'], 'website_title');
                }
                if (!empty($data['website_description'])) {
                    saveTranslation($templateDetail, 'translations', $data['website_description'], 'website_description');
                }

                if (!empty($data['social'])) {
                    foreach ($data['social'] as $social) {
                        if (!empty($social['image'])) {
                            saveFiles($templateDetail, 'socialLogo', $social['image'], ['purpose' => $social['purpose']]);
                        }
                        $socialDatas[] =  [
                            'name' => !empty($social['name']) ? $social['name'] : '',
                            'url' => !empty($social['url']) ? $social['url'] : '',
                            'status' => !empty($social['status']) ? $social['status'] : 'inactive',
                            'purpose' => !empty($social['purpose']) ? $social['purpose'] : '0',
                        ];
                    }
                    $socialData = [
                        'configurable_id' => $templateDetail->id,
                        'configurable_type' => TemplateDetail::class,
                        'data' => json_encode($socialDatas),
                        'created_at' => Carbon::now()
                    ];

                    $config = Configure::where('configurable_id', $templateDetail->id)->first();
                    if (!empty($config)) {
                        $config->update($socialData);
                    } else {
                        Configure::create($socialData);
                    }
                }
            });

            return response()->json([
                'messages' => ['Template updated successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function get(): JsonResponse
    {
        try {
            $templateDetail = new stdClass();
            $templateDetails = TemplateDetail::with('websiteLogo:id,path,fileable_id', 'h5Logo:id,path,fileable_id', 'socialLogo:id,path,fileable_id', 'templateConfiguration')
                ->whereStatus('active')->first();
            if (!empty($templateDetails)) {
                $templateDetail->id = $templateDetails->id;
                $templateDetail->template_id = $templateDetails->template_id;
                $templateDetail->theme = json_decode($templateDetails->theme);
                $templateDetail->banner_style = $templateDetails->banner_style;
                $templateDetail->website_logo = !empty($templateDetails->websiteLogo->path) ? $templateDetails->websiteLogo->path : '';
                $templateDetail->h5_logo = !empty($templateDetails->h5Logo->path) ? $templateDetails->h5Logo->path : '';
                $templateDetail->social_logo = !empty($templateDetails->socialLogo) ? $templateDetails->socialLogo : '';
                $templateDetail->configure = !empty($templateDetails->templateConfiguration) ? $templateDetails->templateConfiguration : '';
                $response = [
                    'messages' => ['Template fetched successfully'],
                    'data' => $templateDetail
                ];
            } else {
                $response =  [
                    'messages' => ['No Active Template']
                ];
            }
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function getData(): JsonResponse
    {
        try {
            $templateDetail = new stdClass();
            $templateDetails = TemplateDetail::with(['h5Logo:id,path,fileable_id', 'websiteLogo:id,path,fileable_id', 'companyInfo:id,info_email,support_email,contact_number,address_id', 'socialLogo:id,path,fileable_id,purpose', 'templateConfiguration:data,configurable_id'])
                ->whereStatus('active')->first();
            if (!empty($templateDetails)) {
                $templateDetail->h5_logo = !empty($templateDetails->h5Logo->path) ? $templateDetails->h5Logo->path : '';
                $templateDetails->theme = json_decode($templateDetails->theme);

                if (request()->segment(2) != ADMIN) {
                    $socialData = !empty($templateDetails->templateConfiguration[0]) ? json_decode($templateDetails->templateConfiguration[0]->data) : [];

                    if (!empty($templateDetails->socialLogo)) {

                        for ($i = 0; $i < count($socialData); $i++) {

                            $socialData[$i]->image = $this->getImage($socialData[$i], $templateDetails->socialLogo);
                        }
                        unset($templateDetails->socialLogo);
                        unset($templateDetails->templateConfiguration);
                        $socials = [];

                        foreach ($socialData as $key => $social) {
                            if (!empty($social->name) && $social->status != 'inactive') {
                                $socials[] = $socialData[$key];
                            }
                        }
                        $templateDetails->social_data =  $socials;
                    }
                }
                $response = [
                    'messages' => ['Template fetched successfully'],
                    'data' => $templateDetails
                ];
            } else {
                $response =  [
                    'messages' => ['No Active Template']
                ];
            }
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function getImage($social, $template)
    {
        $image = '';
        foreach ($template as $key => $socials) {
            if ($socials['purpose'] == (int)$social->purpose) {
                $image = $socials['path'];
            }
        }
        return $image;
    }
}
