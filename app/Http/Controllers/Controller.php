<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use AssetApi;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $successStatus = 200;

    public function uploadAsset(Request $request)
    {
        $return = array(
            'success' => '',
            'error' => 'Upload Asset gagal. Silahkan coba lagi'
        );

        try
        {
            $user_token = $request->user_token;

            $images = [];

            if($request->hasFile('images'))
            {
                foreach($request->file('images') as $img)
                {
                    $file = $img;
                    $filename = $file->getClientOriginalName();
                    $filepath = $file->getPathName();
                    $filetype = $file->getMimeType();
        
                    $postParam = array(
                        'endpoint'  => 'v'.config('app.api_ver').'/store',
                        'form_params' => array(
                            'multipart' => [
                                [
                                    'name'      => 'file',
                                    'filename'  => $filename,
                                    'Mime-Type' => $filetype,
                                    'contents'  => fopen( $filepath, 'r' )
                                ],
                                [
                                    'name'  => 'caption',
                                    'contents'  => str_replace(" ", "_", $filename)
                                ],
                                [
                                    'name' => 'service',
                                    'contents' => 'product'
                                ]
                            ]
                        ),
                        'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
                    );
                    
                    // dump($postParam);
                    $AssetApi = AssetApi::postData( $postParam );
                    $dataDecode = json_decode($AssetApi);
                    // dump($dataDecode);
                    if(!empty($dataDecode->data) && !empty($dataDecode->data->media))
                        $images[] = $dataDecode->data->media;
                }
            }

            if(!empty($images))
            {
                unset($return['error']);
                $return['success'] = 'Image Uploaded Succesfully;';
                $return['images'] = $images;
            }

            return response()->json($return, $this->successStatus);
        }
        catch (\Exception $e) 
        {
            return response()->json($return, $this->successStatus);
            // return response()->json(['message' => 'user not found!'], 404);
        }
    }
}
