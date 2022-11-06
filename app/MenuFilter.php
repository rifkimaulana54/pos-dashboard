<?php

namespace App;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Illuminate\Http\Request;
use Cookie;
use GlobalHelper;

class MenuFilter implements FilterInterface
{
    public function transform($item)
    {
        if(!empty(session('authenticated'))) 
        {
            $request = request();

            if(!GlobalHelper::userRole($request,'superadmin'))
            {
                if (!isset($item['can']) || (isset($item['can']) && !is_array($item['can']) && $item['can'] != 'all' && !in_array($item['can'],$request->user_can)))
                    return false;
                elseif(isset($item['can']) && is_array($item['can']))
                {
                    $return = false;
                    foreach($item['can'] as $can)
                    {
                        if(in_array($can,$request->user_can))
                        {
                            $return = $item;
                            break;
                        }
                    }

                    if(!$return)
                        return $return;
                }
            }
        }

        return $item;
    }
}
