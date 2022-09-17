<?php

namespace App\Helpers;

use Illuminate\Database\QueryException;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use UserApi;
use ContentApi;
use GeneralApi;
use AssetApi;
use Schema;

// use App\LogHistory;

class GlobalHelper 
{
    public static function userCan(Request $request, $perm)
    {
        if(in_array($perm, $request->user_can))
            return true;

        return false;
    }

    public static function userRole(Request $request, $roles)
    {
        if(!empty($request->user_role))
        {
            if(is_array($roles))
            {
                foreach($roles as $role)
                {
                    if(in_array($role, $request->user_role))
                        return true;
                }
            }
            else
                if(in_array($roles,$request->user_role))
                    return true;
        }

        return false;
    }

    public static function userPos(Request $request, $positions)
    {
        if(!empty($request->user_pos))
        {
            if(is_array($positions))
            {
                if(in_array(strtolower($request->user_pos),$positions))
                    return true;                
            }
            else
                if($positions == strtolower($request->user_pos))
                    return true;                            
        }

        return false;
    }

    public static function maybe_unserialize( $original, $array = true ) 
    {
        // if ( self::is_serialized( $original ) ) // don't attempt to unserialize data that wasn't serialized going in
        if(@json_decode($original))
            return @json_decode( $original, $array );

        return $original;
    }

    public static function maybe_serialize( $data ) 
    {
        if ( is_array( $data ) || is_object( $data ) )
            // return serialize( $data );
            return json_encode($data);

        // Double serialization is required for backward compatibility.
        // See https://core.trac.wordpress.org/ticket/12930
        // Also the world will end. See WP 3.6.1.
        // if ( self::is_serialized( $data, false ) )
        //     return serialize( $data );

        return $data;
    }

    private static function is_serialized( $data, $strict = true ) 
    {
        // if it isn't a string, it isn't serialized.
        if ( ! is_string( $data ) ) {
            return false;
        }
        $data = trim( $data );
        if ( 'N;' == $data ) {
            return true;
        }
        if ( strlen( $data ) < 4 ) {
            return false;
        }
        if ( ':' !== $data[1] ) {
            return false;
        }
        if ( $strict ) {
            $lastc = substr( $data, -1 );
            if ( ';' !== $lastc && '}' !== $lastc ) {
                return false;
            }
        } else {
            $semicolon = strpos( $data, ';' );
            $brace     = strpos( $data, '}' );
            // Either ; or } must exist.
            if ( false === $semicolon && false === $brace )
                return false;
            // But neither must be in the first X characters.
            if ( false !== $semicolon && $semicolon < 3 )
                return false;
            if ( false !== $brace && $brace < 4 )
                return false;
        }
        $token = $data[0];
        switch ( $token ) {
            case 's' :
                if ( $strict ) {
                    if ( '"' !== substr( $data, -2, 1 ) ) {
                        return false;
                    }
                } elseif ( false === strpos( $data, '"' ) ) {
                    return false;
                }
                // or else fall through
            case 'a' :
            case 'O' :
                return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
            case 'b' :
            case 'i' :
            case 'd' :
                $end = $strict ? '$' : '';
                return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
        }
        return false;
    }

    public static function encodeJWT($token)
    {

        return base64_encode(gzdeflate($token));
        // $method = 'AES-256-CBC';
        // $secret = base64_decode('tvFD4Vl6Pu2CmqdKYOhIkEQ8ZO4XA4D8CLowBpLSCvA=');
        // $iv = base64_decode('AVoIW0Zs2YY2zFm5fazLfg==');

        // $compressed = self::compress($token);

        // return openssl_encrypt($compressed, $method, $secret, false, $iv);
    }

    public static function decodeJWT($encrypt)
    {
        return gzinflate(base64_decode($encrypt));
    }

    public static function compress($input, $ascii_offset = 38)
    {
        $input = strtoupper($input);
        $output = '';
        //We can try for a 4:3 (8:6) compression (roughly), 24 bits for 4 chars
        foreach(str_split($input, 4) as $chunk) {
            $chunk = str_pad($chunk, 4, '=');

            $int_24 = 0;
            for($i=0; $i<4; $i++){
                //Shift the output to the left 6 bits
                $int_24 <<= 6;

                //Add the next 6 bits
                //Discard the leading ascii chars, i.e make
                $int_24 |= (ord($chunk[$i]) - $ascii_offset) & 0b111111;
            }

            //Here we take the 4 sets of 6 apart in 3 sets of 8
            for($i=0; $i<3; $i++) {
                $output = pack('C', $int_24) . $output;
                $int_24 >>= 8;
            }
        }

        return $output;
    }

    public static function decompress($input, $ascii_offset = 38) 
    {
        $output = '';
        foreach(str_split($input, 3) as $chunk) {

            //Reassemble the 24 bit ints from 3 bytes
            $int_24 = 0;
            foreach(unpack('C*', $chunk) as $char) {
                $int_24 <<= 8;
                $int_24 |= $char & 0b11111111;
            }

            //Expand the 24 bits to 4 sets of 6, and take their character values
            for($i = 0; $i < 4; $i++) {
                $output = chr($ascii_offset + ($int_24 & 0b111111)) . $output;
                $int_24 >>= 6;
            }
        }

        //Make lowercase again and trim off the padding.
        return strtolower(rtrim($output, '='));
    }
    
    static function uploadFile($input_file, $caption, $service, $token)
    {
        $response = null;
        $file = $input_file;
        $filename = $file->getClientOriginalName();
        $filepath = $file->getPathName();
        $filetype = $file->getMimeType();

        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/store',
            'form_params' => array(
                'multipart' => [
                    [
                        'name'      => 'file',
                        'filename'  => $filename,
                        'Mime-Type' => $filetype,
                        'contents'  => fopen($filepath, 'r')
                    ],
                    [
                        'name'  => 'caption',
                        'contents'  => $caption
                    ],
                    [
                        'name' => 'service',
                        'contents' => $service
                    ]
                ]
            ),
            'headers' => ['Authorization' => 'Bearer ' . $token]
        );


        $AssetApi = AssetApi::postData($postParam);
        $dataDecode = json_decode($AssetApi);

        if (!empty($dataDecode->data) && !empty($dataDecode->data->media))
            $response = $dataDecode->data->media;

        return $response;
    }

    public static function queue_types()
    {
        return [
            'customer_name',
            'customer_phone',
            'customer_email',
            'text',
            'number',
            'phone',
            'email',
            'dropdown',
            'checkbox',
            'radio_button',
            'selfie_ktp',
            'ktp',
            'date',
            'toggle',
            'list'
        ];
    }

    public static function promotion_type($sort = [])
    {
        return array(
            [
                'type'  => 'main_banner',
                'width_landscape'   => 2517,
                'height_landscape'  => 1889,
                'width_potret'   => 2160,
                'height_potret'  => 1390,
                'label_img_landscape' => 'Image Landscape *',
                'label_img_potret' => 'Image Potret *',
                'sort' => !empty($sort['main_banner']) ? $sort['main_banner'] : 1,
            ],
            [
                'type'  => 'text_only',
                'width_landscape'   => 2517,
                'height_landscape'  => 1889,
                'width_potret'   => 2160,
                'height_potret'  => 1390,
                'label_img_landscape' => 'Image Landscape *',
                'label_img_potret' => 'Image Potret *',
                'sort' => !empty($sort['main_banner']) ? $sort['main_banner'] : 1,
            ]
        );
    }

    public static function tanggal_indo($time,$short=false,$timer=false)
    {
        $hari = array("Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu");
        $bulan = array("","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");

    if($short)
    {
        if($timer)
            return date('j',$time).' '.$bulan[date('n',$time)].' '.date('Y',$time).', '.date('H:i',$time);
        else
            return date('j',$time).' '.$bulan[date('n',$time)].' '.date('Y',$time);
    }
    else
        if($timer)
            return $hari[date('w',$time)].', '.date('j',$time).' '.$bulan[date('n',$time)].' '.date('Y',$time).', Pkl. '.date('H:i',$time).' WIB.'; 
        else
            return $hari[date('w',$time)].', '.date('j',$time).' '.$bulan[date('n',$time)].' '.date('Y',$time);
    }
}