<?php

namespace App\Services;
use App\Models\SecretStore;
use Illuminate\Support\Facades\Crypt;

/**
 * Class SecretProvider
 * @package App\Services
 */

class SecretProvider
{
    public static function get_secret($company_name)
    {
        $secret = SecretStore::where('company_name',$company_name)->first();
        if($secret)
        {
            $content = Crypt::decryptString($secret->content);
            return $content;
        }
    }
}
