<?php

namespace App\Security;

use App\Database\MYSQL;
use Controllers\Api\Output;

class Firewall
{
    public static function cirdMatch ($ip, $range) {
        list($subnet, $bits) = explode('/', $range);
        if ($bits === null) {
            $bits = 32;
        }
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
        return ($ip & $mask) == $subnet;
    }

    public static function activate() {
        // Find out the real client IP
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $client_ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $client_ip = str_replace(strstr($_SERVER['HTTP_CLIENT_IP'], ':'), '', $_SERVER['HTTP_CLIENT_IP']);
        } else {
            // or just use the normal remote addr
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }
        $firewall_ips_check = MYSQL::query('SELECT * FROM `firewall`');
        $firewall_array = $firewall_ips_check->fetch_all(MYSQLI_ASSOC);
        $allow_list = [];
        foreach ($firewall_array as $index => $array) {
            foreach ($array as $name => $value) {
                if ($name === 'ip_cidr') {
                    array_push($allow_list, $value);
                }
            }
        }
        // Initiate validator variable
        $valid_ip = false;
        // Loop through the allow list
        foreach ($allow_list as $addr) {
            // If there is a match
            if (self::cirdMatch($client_ip, $addr)) {
                // Set the validator to true
                $valid_ip = true;
                // and break the loop
                break;
            }
        }
        if (!$valid_ip) {
            //include_once dirname($_SERVER['DOCUMENT_ROOT']) . '/functions/systemLog/systemLog.php';
            //writeToSystemLog('just tried to access the web app and got Unauthorized', 'Access');
            Output::error('Unauthorized access for IP Address ' . $client_ip . ' on uri ' . $_SERVER['REQUEST_URI'], 401);
        }
    }
}
