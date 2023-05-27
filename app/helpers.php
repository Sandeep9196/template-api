<?php


use App\Models\Customer;
use App\Models\File;
use App\Models\Language;
use App\Models\OntimePassword;
use App\Models\Translation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

const MODEL_PREFIX = "App\Models\\";
const TRASH_FOLDER = '/trash/';
const FILE_PERPOSE_1 = 1;
const FILE_PERPOSE_2 = 2;
const FILE_PERPOSE_3 = 3;
const FILE_PERPOSE_4 = 4;
const FILE_PERPOSE_5 = 5;
const FILE_PERPOSE_6 = 6;
const FILE_PERPOSE_7 = 7;
const FILE_PERPOSE_8 = 8;
const FILE_PERPOSE_9 = 9;
const FILE_PERPOSE_10 = 10;
const FILE_PERPOSE_TYPES = [
    FILE_PERPOSE_1 => 'Setting Logo',
    FILE_PERPOSE_2 => 'Twitter Icon',
    FILE_PERPOSE_3 => 'Pinterest Icon',
    FILE_PERPOSE_4 => 'Facebook Icon',
    FILE_PERPOSE_5 => 'Youtube Icon',
    FILE_PERPOSE_6 => 'Instagram Icon',
    FILE_PERPOSE_7 => 'QQ Icon',
    FILE_PERPOSE_8 => 'Skype Icon',
    FILE_PERPOSE_9 => 'Telegram Icon',
    FILE_PERPOSE_10 => 'Whatsapp Icon',
];
const TRANSFER_IN  = 'transfer_in';
const TRANSFER_OUT  = 'transfer_out';
const WITHDRAW  = 'withdraw';

if (!function_exists('getErrorMessages')) {
    function getErrorMessages($messages)
    {
        $errorMessages = [];
        foreach ($messages as $key => $values) {
            foreach ($values as $index => $value) {
                array_push($errorMessages, $value);
            }
        }

        return $errorMessages;
    }
}

if (!function_exists('generalErrorResponse')) {
    function generalErrorResponse(Exception $e)
    {
        Log::debug($e);
        return response()->json([
            'messages' => [$e->getMessage()],
            'trace' => [$e->getTrace()],
        ], 400);
    }
}
if (!function_exists('getArrayCollections')) {

    function getArrayCollections($arrayData)
    {
        $data = [];
        foreach ($arrayData as $key => $dt) {
            foreach ($dt as $d) {
                array_push($data, $d);
            }
        }

        return $data;
    }
}

if (!function_exists('paginate')) {
    function paginate($items, $perPage = 100, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}

// if (!function_exists('sendOTP')) {
//     function sendOTP($idd = 0, $phoneNumber = 0, $type = 'register')
//     {

//         $otpValue = rand(100000, 999999);
//         $otpValue = 123456;
//         //save otp in table
//         $otp = OntimePassword::whereIdd($idd)->wherePhoneNumber($phoneNumber)->whereType($type)->whereValue($otpValue)->first();
//         if ($otp)
//             $otp->update(['value' => $otpValue]);
//         else {
//             OntimePassword::create([
//                 'value' => $otpValue,
//                 'phone_number' => $phoneNumber,
//                 'idd' => $idd,
//                 'type' => $type,
//             ]);
//         }
//     }
// }

if (!function_exists('sendOTP')) {
    function sendOTP($idd = 0, $phoneNumber = 0, $langId = 1, $type = 'register')
    {
        // $otpValue = rand(100000, 999999);
        $otpValue = 123456;
        $landData = Language::where('id', $langId)->first();
        if (!empty($landData->locale_web)) {
            $langIds = $landData->locale_web;
        } else {
            $langIds = "en";
        }
        if ($type == 'register') {
            // $message = trans('message.firstOtpTextReg') . ' ' . $otpValue . ' ' . trans('message.secondOtpTextReg');
            if ($langIds == "ch") {
                $message = $otpValue . ' 是您的一次性密码，输入密码以完成注册';
            } elseif ($langIds == "kh") {
                $message = 'អតិថិជនជាទីគោរព លេខ ' . $otpValue . ' គឺជា OTP ដើម្បីចុះឈ្មោះរបស់អ្នកសម្រាប់ OneShop ។ សូមកុំបង្ហាញនរណា, ក្រុមការងារ OneShop មិនដែលសុំ OTP ទេ។';
            } else {
                $message = 'Dear Customer, ' . $otpValue . ' is the OTP to complete your registration for OneShop. DO NOT disclose it to anyone, OneShop team never asks for OTP.';
            }
        } else {
            if ($langIds == "ch") {
                $message = $otpValue . ' 是您的一次性密码，输入密码以重置密码。如果您没提出此请求，请通过 admin@the1shops.com 联系我们';
            } elseif ($langIds == "kh") {
                $message = 'ប្រើលេខ ' . $otpValue . ' ជា OTP របស់អ្នក ដើម្បីប្តូរពាក្យសម្ងាត់ OneShop របស់អ្នកឡើងវិញ។ ប្រសិនបើអ្នកមិនបានធ្វើសំណើនេះទេ សូមជូនដំណឹងមកយើងតាមរយៈ admin@the1shops.com';
            } else {
                $message = 'Use ' . $otpValue . ' as your OTP to reset your OneShop Password. If you did not make this request, please alert us at admin@the1shops.com';
            }
        }


        // $toPhoneNumber = $idd . $phoneNumber;
        // $toPhoneNumber = str_replace(array('+'), '', $toPhoneNumber);

        // $serviceUrl = 'http://bizsms.metfone.com.kh:8804/bulkapi?wsdl';

        // if ($langIds == "en") {
        //     $contentType = 0;
        // } else {
        //     $contentType = 1;
        // }
        // return $contentType;
        // $client = new \SoapClient($serviceUrl);
        // $params = array(
        //     "User" => env('SMS_GATEWAY_USERID'),
        //     "Password" => env('SMS_GATEWAY_PASSWORD'),
        //     "CPCode" => env('SMS_GATEWAY_CODE'),
        //     "RequestID" => "1",
        //     "UserID" => $toPhoneNumber,
        //     "ReceiverID" => $toPhoneNumber,
        //     "ServiceID" => env('SMS_GATEWAY_SERVICEDID'),
        //     "CommandCode" => "bulksms",
        //     "Content" => $message,
        //     "ContentType" => $contentType
        // );

        // $response = $client->__soapCall("wsCpMt", array($params));


        // if ($response->return->result === 0) {
        //     return false;
        // }

        //save otp in table
        $otp = OntimePassword::whereIdd($idd)->wherePhoneNumber($phoneNumber)->whereType($type)->whereValue($otpValue)->first();
        if ($otp)
            $otp->update(['value' => $otpValue]);
        else {
            OntimePassword::create([
                'value' => $otpValue,
                'phone_number' => $phoneNumber,
                'idd' => $idd,
                'type' => $type,
            ]);
        }
        return true;
    }
}

if (!function_exists('verifyOTP')) {
    function verifyOTP($idd = 0, $phoneNumber = 0, $otpValue = 0, $type = 'register')
    {
        $otp = OntimePassword::whereIdd($idd)->whereValue($otpValue)->wherePhoneNumber($phoneNumber)->whereType($type)->first();

        if ($otp) {
            if ($otp->expire_at && strtotime($otp->expire_at) < strtotime(now()))
                return ['status' => false, 'msg' => 'OTP expired'];

            $otp->update(['is_verify' => true]);
            return ['status' => true];
        } else
            return ['status' => false, 'msg' => 'OTP not matched'];
    }
}
if (!function_exists('generateReferralCode')) {
    function generateReferralCode($codeLength = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);

        $code = '';
        while (strlen($code) < 6) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code . $character;
        }

        if (Customer::where('referral_code', $code)->exists()) {
            generateReferralCode($codeLength);
        }

        return $code;
    }
}

if (!function_exists('formatIdd')) {
    function formatIdd($idd)
    {
        $idd = str_replace('+', '', $idd);
        $idd = '+' . $idd;

        return $idd;
    }
}
if (!function_exists('checkFileType')) {
    function checkFileType($file)
    {
        $type = 'file';
        if (substr($file->getMimeType(), 0, 5) == 'image') {
            $type = 'image';
        }

        return $type;
    }
}

if (!function_exists('getRandomIdGenerate')) {
    function getRandomIdGenerate($prefix = null)
    {
        return $prefix . '-' . Carbon::now()->timestamp . mt_rand(100, 99999);
    }
}

if (!function_exists('getRandomIdGenerator')) {
    function getRandomIdGenerator($prefix = null)
    {
        return $prefix . '-' . mt_rand(100, 99999999);
    }
}


if (!function_exists('uploadImage')) {
    function uploadImage($file, $folder)
    {

        if (!empty($file) && is_file($file)) {
            $md5Name = md5_file($file->getRealPath());
            $md5Name = Carbon::now()->timestamp . $md5Name;
            $guessExtension = $file->guessExtension();
            $uploaded_files = $file->storeAs('public/images/' . $folder, $md5Name . '.' . $guessExtension);
            $uploaded_files = substr(Storage::url($uploaded_files), 1);

            return $uploaded_files;
        }
        $file;
    }
}


/**
 * @desc soft delete relationship
 * @param $resource
 * @param $relations_to_cascade
 * @return mixed
 * @date 07 Jan 2023
 * @author Phen
 */
if (!function_exists('softDeleteRelations')) {
    function softDeleteRelations($resource, $relations_to_cascade)
    {
        if ($relations_to_cascade && is_array($relations_to_cascade)) {
            foreach ($relations_to_cascade as $relation) {
                if ($resource->{$relation}) {
                    if ($relation == 'file' or $relation == 'files' or $relation == 'image' or $relation == 'images') {
                        try {
                            foreach ($resource->{$relation}()->get() as $item) {
                                $data = $item->storage_path;
                                $trash_data = TRASH_FOLDER . $data;
                                //if is file, will move file to trash folder (safe delete can restore file later)
                                Storage::move($data, $trash_data);
                                $item->delete();
                            }
                        } catch (\Exception $e) {
                            Log::error("Delete relationship of table " . $resource->getTable() . " error: for relation name: " . $relation);
                            Log::error($e->getMessage());
                        }
                    } else {
                        try {
                            foreach ($resource->{$relation}()->get() as $item) {
                                $item->delete();
                                Log::debug("Deleted: " . $item->getTable());
                            }
                        } catch (\Exception $e) {
                            Log::error("Delete relationship of table " . $resource->getTable() . " error: for relation name: " . $relation);
                            Log::error($e->getMessage());
                        }
                    }
                }
            }
        }
    }
}

if (!function_exists('restoreRelations')) {
    function restoreRelations($resource, $relations_to_cascade)
    {
        foreach ($relations_to_cascade as $relation) {
            if (method_exists($resource, $relation)) {
                if ($relation == 'file' or $relation == 'files' or $relation == 'image' or $relation == 'images') {

                    try {
                        foreach ($resource->{$relation}()->withoutGlobalScope(SoftDeletingScope::class)->get() as $item) {
                            $data = $item->storage_path;
                            $trash_data = TRASH_FOLDER . $data;
                            Storage::move($trash_data, $data);
                            $item->restore();
                        }
                    } catch (\Exception $e) {
                        Log::error("Restore relationship of table " . $resource->getTable() . " error: for relation name: " . $relation);
                        Log::error($e->getCode());
                    }
                } else {
                    try {
                        foreach ($resource->{$relation}()->withoutGlobalScope(SoftDeletingScope::class)->get() as $item) {
                            $item->restore();
                        }
                    } catch (\Exception $e) {
                        Log::error("Restore relationship of table " . $resource->getTable() . " error: for relation name: " . $relation);
                        Log::error($e->getCode());
                    }
                }
            }
        }
    }
}

/**
 * @desc store files
 * @param $resource
 * @param $relations_to_cascade
 * @return mixed
 * @date 11 Jan 2023
 * @author Phen
 */
if (!function_exists('saveFiles')) {
    function saveFiles(object $masterModel, $fileRelation, $newFiles, array $fileFilter = [])
    {

        if ($newFiles) {
            if (!is_array($newFiles)) $newFiles = array($newFiles);
            if (sizeof($newFiles) > 0) {
                $files = $masterModel->{$fileRelation}();
                if ($fileFilter)
                    $files->where($fileFilter);

                $files = $files->get();

                if (sizeof($files) > 0) {
                    foreach ($files as $key => $file) {
                        $oldFile = $file->storage_path;
                        if (Storage::exists($oldFile)) Storage::delete($oldFile);
                        if (!empty($newFiles[$key])) {
                            $path = Storage::putFile('public/images/' . $masterModel->getTable(), $newFiles[$key]);

                            $file->update([
                                'path' =>  $path,
                                'type' => checkFileType($newFiles[$key])
                            ]);
                        }
                    }
                    //if old file less than new files
                    for ($key; $key < sizeof($newFiles) - 1; $key++) {
                        $path = Storage::putFile('public/images/' . $masterModel->getTable(), $newFiles[$key]);

                        $masterModel->{$fileRelation}()->create(
                            array_merge(
                                [
                                    'path' =>  $path,
                                    'type' => checkFileType($newFiles[$key])
                                ],
                                $fileFilter
                            )
                        );
                    }
                } else {
                    foreach ($newFiles as $key => $newFile) {
                        $path = Storage::putFile('public/images/' . $masterModel->getTable(), $newFile);

                        $masterModel->{$fileRelation}()->create(
                            $fileFilter ? array_merge(
                                [
                                    'path' =>$path ,
                                    'type' => checkFileType($newFile)
                                ],
                                $fileFilter
                            ) : [
                                'path' => $path,
                                'type' => checkFileType($newFile)
                            ]
                        );
                    }
                }
            }
        }
    }
}

if (!function_exists('deleteImage')) {
    function deleteImage($id)
    {
        $file = File::find($id);
        $url = env('APP_URL') . 'api/media/';
        $path = str_replace($url, '', $file->path);
        Storage::delete($path);
        $file->forceDelete();
    }
}

/**
 * @desc store translations
 * @param $resource
 * @param $relations_to_cascade
 * @return mixed
 * @date 11 Jan 2023
 * @author Phen
 */
if (!function_exists('saveTranslation')) {
    function saveTranslation(object $masterModel, $translationRellation, $translateDatas, $fieldName = null, $purpose = false)
    {
        if ($translateDatas) {
            $languages = Language::pluck('id')->toArray();
            $existingTranslationUpdateId = array();
            //save or update translations

            foreach ($translateDatas as $key => $translateData) {
                $translateData['language_id'] = @$translateData['language_id'] ?? $masterModel->id;
                if (in_array($translateData['language_id'], $languages) || $translateData['language_id'] == $masterModel->id) {

                    $exist = Translation::whereTranslationableType(get_class($masterModel))
                        ->whereTranslationableId($masterModel->id)
                        ->whereLanguageId($translateData['language_id'])
                        ->whereFieldName($translateData['field_name']);

                    if ($purpose)
                        $exist->wherePurpose($translateData['language_id']);

                    $exist = $exist->first();
                    if ($exist) {
                        $exist->update($translateData);
                        array_push($existingTranslationUpdateId, $exist->id);
                    } else {
                        $translateData['purpose'] =  $translateData['language_id'];
                        $newTran = $masterModel->{$translationRellation}()->create($translateData);
                        array_push($existingTranslationUpdateId, $newTran->id);
                    }
                }
            }
            //end of save or update translations

            //delete existing translation
            if ($fieldName)
                $masterModel->{$translationRellation}()->where('field_name', $fieldName)
                    ->whereNotIn('translations.id', $existingTranslationUpdateId)->delete();
            else
                $masterModel->{$translationRellation}()->whereNotIn('translations.id', $existingTranslationUpdateId)->delete();
            //end of delete existing translation

        } else {
            if ($fieldName)
                $masterModel->{$translationRellation}()->where('field_name', $fieldName)->delete();
            else
                $masterModel->{$translationRellation}()->delete();
        }
    }
}

if (!function_exists('zeroappend')) {
    function zeroappend($LastNumber)
    {
        $count = (int) log10(abs($LastNumber)) + 1;
        if ($count == 1) {
            return $append = '000000';
        } elseif ($count == 2) {
            return $append = '00000';
        } elseif ($count == 3) {
            return $append = '0000';
        } elseif ($count == 4) {
            return $append = '000';
        } elseif ($count == 5) {
            return $append = '00';
        } elseif ($count == 6) {
            return $append = '0';
        } elseif ($count == 7) {
            return $append = '';
        } else {
            return $append = '';
        }
    }
}

if (!function_exists('getSuccessMessages')) {
    function getSuccessMessages($data, $status = true)
    {
        $successMessage = [];
        if (!empty($data['message'])) {
            $successMessage['message'] = $data['message'];
        }
        if (!empty($data['data'])) {
            $successMessage['data'] = $data['data'];
        }
        $successMessage['status'] = $status;

        return response()->json($successMessage, $data['statusCode']);
    }
}


if (!function_exists('getErrorMessagesMob')) {
    function getErrorMessagesMob($messages)
    {
        $errorMessages = [];
        foreach ($messages as $key => $values) {
            foreach ($values as $index => $value) {
                array_push($errorMessages, $value);
            }
        }

        return $errorMessages[0];
    }
}

if (!function_exists('saveTranslation')) {
    function saveTranslation(object $masterModel, $translationRellation, $translateDatas, $fieldName = null)
    {
        if ($translateDatas) {
            $languages = Language::pluck('id')->toArray();
            $existingTranslationUpdateId = array();
            //save or update translations
            foreach ($translateDatas as $key => $translateData) {
                if (in_array($translateData['language_id'], $languages)) {

                    $exist = Translation::whereTranslationableType(get_class($masterModel))
                        ->whereTranslationableId($masterModel->id)
                        ->whereLanguageId($translateData['language_id'])
                        ->whereFieldName($translateData['field_name']);

                    if (array_key_exists('purpose', $translateData))
                        $exist->wherePurpose($translateData['purpose']);

                    $exist = $exist->first();
                    if ($exist) {
                        $exist->update($translateData);
                        array_push($existingTranslationUpdateId, $exist->id);
                    } else {
                        $newTran = $masterModel->{$translationRellation}()->create($translateData);
                        array_push($existingTranslationUpdateId, $newTran->id);
                    }
                }
            }
            //end of save or update translations

            //delete existing translation
            if ($fieldName)
                $masterModel->{$translationRellation}()->where('field_name', $fieldName)
                    ->whereNotIn('translations.id', $existingTranslationUpdateId)->delete();
            else
                $masterModel->{$translationRellation}()->whereNotIn('translations.id', $existingTranslationUpdateId)->delete();
            //end of delete existing translation

        } else {
            if ($fieldName)
                $masterModel->{$translationRellation}()->where('field_name', $fieldName)->delete();
            else
                $masterModel->{$translationRellation}()->delete();
        }
    }
}
// $languageIds = Language::pluck('id');
// $langPurPoseMap = array();
// foreach ( $languages as $languageId)
//     $langPurPoseMap[$languageId] = $languageId;

const TRANSLATION_PURPOSE = [
    Language::class => [
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9
    ]
];


if (!function_exists('getTranslationPurpose')) {
    function getTranslationPurpose(string $model, $modelId)
    {
        return TRANSLATION_PURPOSE[$model][$modelId];
    }
}



///=====================================================Payment Utilities================================

/**
 * @desc getConfigs
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('getConfigs')) {
    function getConfigs()
    {
        $configs = [
            'url'               => 'https://devwebpayment.kesspay.io',
            'username'          => "lomatechnology2022@gmail.com",
            'password'          => '%6}.-MONeBKOuz-J]EKvuC^CA=%7K]h4F1=>F[$ARg._y@!|IintHQ2',
            'client_id'         => "00980f8f-fb0f-455f-bda8-b0615ba950b1",
            'client_secret'     => "rRb8s7JRbs+fyIy*jw(??.&-Lhm0iqje)J,?&ZJB(d9,tr=4.Uoo_|p",
            'seller_code'       => 'CU2302-28043196470682791',
            'api_secret_key'    => 's3:9EKp>!R9?9z%G_QmUjEQq,+}G~wkHw.FCMHR,pCwYzCWg-u<1nM.',
        ];
        return $configs;
    }
}


/**
 * @desc getConfigs
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('getToken')) {
    function getToken()
    {


        if (isset($_COOKIE['access_token'])) {
            return $_COOKIE['access_token'];
        }

        $params = [
            'grant_type' => "password",
            'client_id' => getConfigs()['client_id'],
            'client_secret' => getConfigs()['client_secret'],
            "username" => getConfigs()['username'],
            "password" => getConfigs()['password'],
        ];

        $url = getConfigs()['url'] . '/oauth/token';
        try {
            $resp = callHttp($url, $params);
            setcookie('access_token', $resp['access_token'], $resp['expires_in'] - 100);
            return $resp['access_token'];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

/**
 * @desc getConfigs
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('callHttp')) {
    function callHttp($url, $params)
    {

        try {

            $headers = ["Content-Type: application/json"];
            if (!str_contains($url, "oauth/token") && $token = getToken()) {
                $headers[] =  "Authorization: Bearer " . $token;
            }

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER =>  $headers,
                CURLOPT_SSL_VERIFYPEER => false
            ));

            $response = curl_exec($curl);

            if ($response === false) {
                throw new Exception(curl_error($curl));
            }

            curl_close($curl);

            return json_decode($response, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}


/**
 * @desc encrypt
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('encrypt')) {
    function encrypt(array $params)
    {
        $rawText = json_encode($params);
        openssl_public_encrypt($rawText, $encrypted, getPublicKey());

        return bin2hex($encrypted);
    }
}


/**
 * @desc signature
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('signature')) {
    function signature(array $params, $api_secret_key)
    {
        $signType = $params['sign_type'] ?? "MD5";

        $string = toUrlParams($params);
        $string = $string . "&key=" . $api_secret_key;

        if ($signType == "MD5")
            $string = md5($string);
        else if ($signType == "HMAC-SHA256")
            $string = hash_hmac("sha256", $string, $api_secret_key);

        return $string;
    }
}

/**
 * @desc toUrlParams
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('toUrlParams')) {
    function toUrlParams(array $values)
    {
        ksort($values);

        $values = array_filter($values, function ($var) {
            return !is_null($var);
        });

        $buff = "";

        foreach ($values as $k => $v) {
            if ($k != "sign" && $v !== "" && !is_array($v) && !is_object($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");

        return $buff;
    }
}


/**
 * @desc getPublicKey
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('getPublicKey')) {
    function getPublicKey()
    {
        return "-----BEGIN PUBLIC KEY-----

    -----END PUBLIC KEY-----";
    }
}
///=====================================================Payment Utilities================================


/**
 * @desc get customer withDraw Amount
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('withDrawAmount')) {
    function withDrawAmount(array $status = array('Review', 'Approve', 'Pending', 'Success'))
    {
        if ($status)
            return Auth::user()->transactions()->whereTransactionType('withdraw')->whereIn('status', $status)->sum('amount');
        return Auth::user()->transactions()->whereTransactionType('withdraw')->sum('amount');
    }
}

/**
 * @desc get customer remaining Amount
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('customerRemainAmount')) {
    function customerRemainAmount($currencyId = 1)
    {
        $customerRemainAmount = 0;
        $customer = Auth::user();
        if (@$customer->wallets) {
            $customerRemainAmount = @$customer->wallets->where('currency_id', $currencyId)->first()->amount ?? 0;
            $customerRemainAmount = $customerRemainAmount - withDrawAmount(['Approve', 'Review']);
            if ($customerRemainAmount < 0)
                $customerRemainAmount = 0;
        }

        return $customerRemainAmount;
    }
}

if (! function_exists('resizeImage')) {
    function resizeImage($imagePath, $percentage) {
        $img = InterventionImage::make($imagePath);
        $width = $img->width();
        $height = $img->height();
        $new_width = intval($width * $percentage / 100);
        $new_height = intval($height * $percentage / 100);
        $img->resize($new_width, $new_height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        return $img->response();
    }
}

if (!function_exists('generateUniqueSlug')) {
    function generateUniqueSlug( $model, $nameString, $modelSlugField = 'slug') {
        $uniqueSlug = $nameString;
        try {
            $uniqueSlug = Str::slug($nameString);
            $i = 1;
            $existingSlugs = $model->where($modelSlugField, $uniqueSlug)->pluck($modelSlugField)->toArray();
            while (in_array($uniqueSlug, $existingSlugs)) {
                $uniqueSlug = $uniqueSlug . '-' . $i;
                $i++;
            }
        }
        catch(Exception $th){

        }
        return $uniqueSlug;
    }
}



define('ADMIN', 'admin');
