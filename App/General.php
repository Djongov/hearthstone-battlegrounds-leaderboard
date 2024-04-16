<?php

namespace App;

class General
{
    public static function fullUri(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    public static function currentIP(): string
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $client_ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $client_ip = str_replace(strstr($_SERVER['HTTP_CLIENT_IP'], ':'), '', $_SERVER['HTTP_CLIENT_IP']);
        } else {
            // or just use the normal remote addr
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }
        return $client_ip;
    }
    public static function currentBrowser()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }
    // This method catches if current uri is in the array of uris including wildcards
    public static function matchRequestURI($uris)
    {
        // Get the current request URI
        $currentURI = $_SERVER['REQUEST_URI'];

        // Loop through the array of URIs
        foreach ($uris as $uri) {
            // If the URI in the array ends with a wildcard character (*), use strncmp to check if the current URI matches the beginning part of the URI in the array
            if (substr($uri, -1) === '*' && strncmp($currentURI, rtrim($uri, '*'), strlen(rtrim($uri, '*'))) === 0) {
                return true; // Match found
            } elseif ($currentURI === $uri) {
                return true; // Exact match found
            }
        }

        return false; // No match found
    }
    public static function parsePhpInfo() : array
    {
        ob_start();
        phpinfo(INFO_ALL);
        $phpinfo = ob_get_clean();

        // Find table data
        preg_match_all('/<tr>(?:.*?<td[^>]*>(.*?)<\/td>.*?<td[^>]*>(.*?)<\/td>)<\/tr>/', $phpinfo, $matches);

        $result = array();
        $currentCategory = '';
        foreach ($matches[1] as $index => $name) {
            if (empty($name)) {
                continue;
            }

            if (strpos($name, '</') !== false) {
                // Skip table headers and footers
                continue;
            }

            if (strpos($name, '<') !== false) {
                // Extract category name
                $currentCategory = strip_tags($name);
                $result[$currentCategory] = array();
            } else {
                // Add key-value pair to the current category
                $result[$currentCategory][$name] = strip_tags($matches[2][$index]);
            }
        }

        return $result;
    }
    // Convert assoc array to indexed array
    public static function assocToIndexed(array $array): array
    {
        return array_map(function ($key, $value) {
            return [$key, $value];
        }, array_keys($array), $array);
    }
    // Random RGBA
    public static function randomRGBA($opacity = 1)
    {
        return 'rgba(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . $opacity . ')';
    }
    // Convert any date to UTC
    public static function convertToUTC(string $date, string $format = 'Y-m-d H:i'): string
    {
        // get the current timezone
        $timezone = date_default_timezone_get();
        $originalTimezone = new \DateTimeZone($timezone);
        $dateTime = new \DateTime($date, $originalTimezone);
        $dateTime->setTimezone(new \DateTimeZone('UTC'));
        $utcDatetime = $dateTime->format($format);
        return $utcDatetime;
    }
    public static function isDateOrDatetime($dateString)
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
        return $dateTime && $dateTime->format('Y-m-d H:i:s') === $dateString;
    }
    public static function getLocaleFromCountryCode($countryCode)
    {
        $locales = [
            'US' => 'en_US',
            'GB' => 'en_GB',
            'FR' => 'fr_FR',
            'DE' => 'de_DE',
            'ES' => 'es_ES',
            'IT' => 'it_IT',
            'JP' => 'ja_JP',
            'CN' => 'zh_CN',
            'BR' => 'pt_BR',
            'IN' => 'en_IN',
            'CA' => 'en_CA',
            'AU' => 'en_AU',
            'MX' => 'es_MX',
            'AR' => 'es_AR',
            'RU' => 'ru_RU',
            'KR' => 'ko_KR',
            'SA' => 'ar_SA',
            'ZA' => 'en_ZA',
            'EG' => 'ar_EG',
            'NG' => 'en_NG',
        ];

        return isset($locales[$countryCode]) ? $locales[$countryCode] : null;
    }
    public static function base64url_encode(string $input, int $nopad = 1, int $wrap = 0)
    {
        $data  = base64_encode($input);

        if ($nopad) {
            $data = str_replace("=", "", $data);
        }

        $data = strtr($data, '+/=', '-_,');

        if ($wrap) {
            $datalb = "";
            while (mb_strlen($data) > 64) {
                $datalb .= substr($data, 0, 64) . "\n";
                $data = substr($data, 64);
            }
            $datalb .= $data;
            return $datalb;
        } else {
            return $data;
        }
    }

    public static function base64url_decode($input)
    {
        return base64_decode(strtr($input, '-_,', '+/='));
    }
    public static function decodeUnicodeString($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $str);
    }
    public static function country_code_to_locale($country_code, $language_code = '')
    {
        // Locale list taken from:
        // http://stackoverflow.com/questions/3191664/
        // list-of-all-locales-and-their-short-codes
        $locales = array(
            'af-ZA',
            'am-ET',
            'ar-AE',
            'ar-BH',
            'ar-DZ',
            'ar-EG',
            'ar-IQ',
            'ar-JO',
            'ar-KW',
            'ar-LB',
            'ar-LY',
            'ar-MA',
            'arn-CL',
            'ar-OM',
            'ar-QA',
            'ar-SA',
            'ar-SY',
            'ar-TN',
            'ar-YE',
            'as-IN',
            'az-Cyrl-AZ',
            'az-Latn-AZ',
            'ba-RU',
            'be-BY',
            'bg-BG',
            'bn-BD',
            'bn-IN',
            'bo-CN',
            'br-FR',
            'bs-Cyrl-BA',
            'bs-Latn-BA',
            'ca-ES',
            'co-FR',
            'cs-CZ',
            'cy-GB',
            'da-DK',
            'de-AT',
            'de-CH',
            'de-DE',
            'de-LI',
            'de-LU',
            'dsb-DE',
            'dv-MV',
            'el-GR',
            'en-029',
            'en-AU',
            'en-BZ',
            'en-CA',
            'en-GB',
            'en-IE',
            'en-IN',
            'en-JM',
            'en-MY',
            'en-NZ',
            'en-PH',
            'en-SG',
            'en-TT',
            'en-US',
            'en-ZA',
            'en-ZW',
            'es-AR',
            'es-BO',
            'es-CL',
            'es-CO',
            'es-CR',
            'es-DO',
            'es-EC',
            'es-ES',
            'es-GT',
            'es-HN',
            'es-MX',
            'es-NI',
            'es-PA',
            'es-PE',
            'es-PR',
            'es-PY',
            'es-SV',
            'es-US',
            'es-UY',
            'es-VE',
            'et-EE',
            'eu-ES',
            'fa-IR',
            'fi-FI',
            'fil-PH',
            'fo-FO',
            'fr-BE',
            'fr-CA',
            'fr-CH',
            'fr-FR',
            'fr-LU',
            'fr-MC',
            'fy-NL',
            'ga-IE',
            'gd-GB',
            'gl-ES',
            'gsw-FR',
            'gu-IN',
            'ha-Latn-NG',
            'he-IL',
            'hi-IN',
            'hr-BA',
            'hr-HR',
            'hsb-DE',
            'hu-HU',
            'hy-AM',
            'id-ID',
            'ig-NG',
            'ii-CN',
            'is-IS',
            'it-CH',
            'it-IT',
            'iu-Cans-CA',
            'iu-Latn-CA',
            'ja-JP',
            'ka-GE',
            'kk-KZ',
            'kl-GL',
            'km-KH',
            'kn-IN',
            'kok-IN',
            'ko-KR',
            'ky-KG',
            'lb-LU',
            'lo-LA',
            'lt-LT',
            'lv-LV',
            'mi-NZ',
            'mk-MK',
            'ml-IN',
            'mn-MN',
            'mn-Mong-CN',
            'moh-CA',
            'mr-IN',
            'ms-BN',
            'ms-MY',
            'mt-MT',
            'nb-NO',
            'ne-NP',
            'nl-BE',
            'nl-NL',
            'nn-NO',
            'nso-ZA',
            'oc-FR',
            'or-IN',
            'pa-IN',
            'pl-PL',
            'prs-AF',
            'ps-AF',
            'pt-BR',
            'pt-PT',
            'qut-GT',
            'quz-BO',
            'quz-EC',
            'quz-PE',
            'rm-CH',
            'ro-RO',
            'ru-RU',
            'rw-RW',
            'sah-RU',
            'sa-IN',
            'se-FI',
            'se-NO',
            'se-SE',
            'si-LK',
            'sk-SK',
            'sl-SI',
            'sma-NO',
            'sma-SE',
            'smj-NO',
            'smj-SE',
            'smn-FI',
            'sms-FI',
            'sq-AL',
            'sr-Cyrl-BA',
            'sr-Cyrl-CS',
            'sr-Cyrl-ME',
            'sr-Cyrl-RS',
            'sr-Latn-BA',
            'sr-Latn-CS',
            'sr-Latn-ME',
            'sr-Latn-RS',
            'sv-FI',
            'sv-SE',
            'sw-KE',
            'syr-SY',
            'ta-IN',
            'te-IN',
            'tg-Cyrl-TJ',
            'th-TH',
            'tk-TM',
            'tn-ZA',
            'tr-TR',
            'tt-RU',
            'tzm-Latn-DZ',
            'ug-CN',
            'uk-UA',
            'ur-PK',
            'uz-Cyrl-UZ',
            'uz-Latn-UZ',
            'vi-VN',
            'wo-SN',
            'xh-ZA',
            'yo-NG',
            'zh-CN',
            'zh-HK',
            'zh-MO',
            'zh-SG',
            'zh-TW',
            'zu-ZA',
        );

        foreach ($locales as $locale) {
            $locale_region = locale_get_region($locale);
            $locale_language = locale_get_primary_language($locale);
            $locale_array = array(
                'language' => $locale_language,
                'region' => $locale_region
            );
            if ($country_code !== null) {
                if (
                    strtoupper($country_code) == $locale_region &&
                    $language_code == ''
                ) {
                    return locale_compose($locale_array);
                } elseif (
                    strtoupper($country_code) == $locale_region &&
                    strtolower($language_code) == $locale_language
                ) {
                    return locale_compose($locale_array);
                }
            }
        }

        return null;
    }
    public static function randomString(int $length = 64)
    {
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length - ($length % 2)) / 2));
    }
}
