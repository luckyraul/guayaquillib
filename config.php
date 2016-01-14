<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

class Config {
    public static $ui_localization = 'en'; // ru or en
    public static $catalog_data = 'en_US'; // en_GB or ru_RU

    public static $useLoginAuthorizationMethod = false;

    // login/key from laximo.ru
//    public static $userLogin = '';
//    public static $userKey = '';

    public static $redirectUrl = 'http://domain.tld/search?query=$oem$';
}
