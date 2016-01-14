﻿<?php

// Including localization file
include_once('config.php');
include_once('localization_'.Config::$ui_localization.'.php');

// Including common template
require_once('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'template.php');

// Implement common "CMS" functions
abstract class CommonExtender extends LanguageTemplate
{
    public function GetLocalizedString($name, $params = false, $renderer)
    {
        return self::FormatLocalizedString($name, $params);
    }

    public static function FormatLocalizedString($name, $params = false)
    {
        if ($params == false)
            return self::LocalizeString($name);

        if (!is_array($params))
            return sprintf(self::LocalizeString($name), $params);

        array_unshift($params, self::LocalizeString($name));
        return call_user_func_array('sprintf', $params);
    }

    public function AppendJavaScript($filename, $renderer)
    {
    	  echo '<script src="'.$this->Convert2uri($filename).'" type="text/javascript"></script>';
    }

    public function AppendCSS($filename, $renderer)
    {
    	  echo '<link href="'.$this->Convert2uri($filename).'" media="screen" rel="stylesheet" type="text/css"/>';
    }

    public function Convert2uri($filename)
    {
        $filename = str_replace('\\', '/', $filename);
        $current_script = explode('/', dirname($_SERVER['SCRIPT_FILENAME']));
        $included_file = explode('/', dirname($filename));
        $url = implode('/', array_slice($included_file, count($current_script))) . '/' . basename($filename);
        return $url;
    }

    static function LocalizeString($str)
    {
        $str = strtolower($str);
        $data = @LanguageTemplate::$language_data[$str];
        return $data ? $data : $str;
    }

    static function isFeatureSupported($catalogInfo, $featureName)
    {
        $result = false;
        foreach ($catalogInfo->features->feature as $feature) {
            if ((string)$feature['name'] == $featureName) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
?>