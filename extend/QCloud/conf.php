<?php

namespace qcloudcos;

class Conf {
    // Cos php sdk version number.
    const VERSION = 'v4.2';
    const API_COSAPI_END_POINT = 'http://region.file.myqcloud.com/files/v2/';

    // Please refer to http://console.qcloud.com/cos to fetch your app_id, secret_id and secret_key.
    const APP_ID = '1253714281';
    const SECRET_ID = 'AKIDOhplW6bjASbeWDL2GnETZUJ4iASdqbIo';
    const SECRET_KEY = 'TltvmRvHVY4VkniS0NtOk2CByZyDrQo0';

    /**
     * Get the User-Agent string to send to COS server.
     */
    public static function getUserAgent() {
        return 'cos-php-sdk-' . self::VERSION;
    }
}
