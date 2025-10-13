<?php

use App\Models\Configuration;

function getConfiguration($key){
    $config = Configuration::where('configuration_key', '=', $key)->first();
    if($config!=null){
        return $config->configuration_value;

    }
    return null;
}