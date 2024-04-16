<?php
// set the display errors to 1
ini_set('display_errors', 1);

/*

Branding & SEO Settings

*/
// Whether to show the loading screen on page load
define("SHOW_LOADING_SCREEN", true);
// Site title, Goes on footer and main menu header
define("SITE_TITLE", "Dashboard Template");
// Key words for SEO
define("GENERIC_KEYWORDS", [
    SITE_TITLE,
]);
// Site description for SEO
define("GENERIC_DESCRIPTION", "Dashboard Template");

// Logo for the SEO OG tags
define("OG_LOGO", 'https://' . $_SERVER['HTTP_HOST'] . '/assets/images/Logos/awm-full-logo-card.png');

// Used in terms of service
define("THIRD_PARTY_LIST", []);
// Used in terms of service
define("COMPANY_NAME", "Sunwell Solutions LTD");
// Used in terms of service
define("COMPANY_EMAIL", "info@sunwellsolutions.com");
// Used in terms of service
define("COMPANY_COUNTRY", "Bulgaria");
// Used in terms of service
define("COMPANY_PHONE", "+359887755355");
// Used in terms of service
define("COMPANY_ADDRESS", "Sofia, Khan Krum street 13");
// Used in terms of service
define("COMPANY_URL", "https://sunwellsolutions.com");

// Logo that sits on the menu and the footer
define("LOGO", '/assets/images/logo.jpg');

// Default set of metadata for the site, used in routes.php
define("DEFAULT_METADATA", [
    'title' => SITE_TITLE,
    'description' => GENERIC_DESCRIPTION,
    'keywords' => GENERIC_KEYWORDS,
    'thumbimage' => OG_LOGO,
]);

// Default theme for unathenticated users and first-time logins, possible values: 'amber', 'green', 'stone', 'rose', 'lime', 'teal', 'sky', 'purple', 'red', 'fuchsia', 'indigo'
define("COLOR_SCHEME", "amber");

/*

DB Settings

$_ENV is taking values from the .env file in the root of the project. If you are not using .env, hardcode them or pass them as env variables in your server

*/

define("MYSQL_SSL", filter_var($_ENV['MYSQL_SSL'], FILTER_VALIDATE_BOOLEAN));
define("DB_SSL", filter_var($_ENV['DB_SSL'], FILTER_VALIDATE_BOOLEAN));
define("DB_HOST", $_ENV['DB_HOST']);
define("DB_USER", $_ENV['DB_USER']);
define("DB_PASS", $_ENV['DB_PASS']);
define("DB_NAME", $_ENV['DB_NAME']);

// This is the DigiCertGlobalRootCA.crt.pem file that is used to verify the SSL connection to the DB. It's located in the .tools folder
define("CA_CERT", dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . '.tools' . DIRECTORY_SEPARATOR . 'DigiCertGlobalRootCA.crt.pem');
// This is used by the curl requests so you don't get SSL verification errors. It's located in the .tools folder
define("CURL_CERT", dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . '.tools' . DIRECTORY_SEPARATOR . 'cacert.pem');

// This needs to be set to what is set across the fetch requests in the javascript files. Default is the below
define('SECRET_HEADER', 'secretheader');
// Same as above
define('SECRET_HEADER_VALUE', 'badass');

/*

Mailer Settings (Sendgrid)

*/

define("SENDGRID", true);
if (SENDGRID) {
    define("SENDGRID_API_KEY", $_ENV['SENDGRID_API_KEY']);
    #define("SENDGRID_TEMPLATE_ID", 'd-381e01fdce2b44c48791d7a12683a9c3');
}

define("FROM", 'admin@gamerz-bg.com');
define("FROM_NAME", 'No Reply');

/*

Text Editor Settings (TinyMCE)

*/

define("TINYMCE", true);

if (TINYMCE) {
    define("TINYMCE_SCRIPT_LINK", 'https://cdn.tiny.cloud/1/z5zdktmh1l2u1e6mhjuer9yzde2z48kc5ctgg9wsppaobz9s/tinymce/6/tinymce.min.js');
    #define("TINYMCE_API_KEY", $_ENV['TINYMCE_API_KEY']);
}

/*

Charts

For displaying non-JS charts we utilize Quickchart.io. It's a free service that allows you to generate charts from a simple URL. We use it to generate the charts in the form of images which are suited for emailing them safely or display charts from the backend. However, we introduce QUICKCHART_HOST so you can host your own instance of Quickchart.io and use it instead of the public one. This is useful if you want to keep your data private and not send it to a third party service. If you want to host your own instance, you need an app hosting the docker image of Quickchart.io. You can find it here: ianw/quickchart:latest

*/

define("QUICKCHART_HOST", "quickchart.io");

/*

Authentication Settings

*/

// Name of the authentication cookie which holds the JWT token
define('AUTH_COOKIE_NAME', 'auth_cookie');

$destination = (isset($_GET['destination'])) ? $_GET['destination'] : $_SERVER['REQUEST_URI'];
$protocol = (str_contains($_SERVER['HTTP_HOST'], 'localhost')) ? 'http' : 'https';

// Whether to allow users to login with local accounts
define('LOCAL_USER_LOGIN', true);
if (LOCAL_USER_LOGIN) {
    // This is used by the JWT handler to sign the tokens. It's should be a base64 encoded string of the public key
    define("JWT_PUBLIC_KEY", $_ENV['JWT_PUBLIC_KEY']);
    // This is used by the JWT handler to sign the tokens. It's should to be a base64 encoded string of the private key
    define("JWT_PRIVATE_KEY", $_ENV['JWT_PRIVATE_KEY']);
    // Whether to allow users to manually register
    define('MANUAL_REGISTRATION', true);
}
// Whether to allow users to login with Azure AD accounts
define('AZURE_AD_LOGIN', true);
if (AZURE_AD_LOGIN) {
    include_once dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'azure-ad-auth-config.php';
}
define('MICROSOFT_LIVE_LOGIN', true);
if (MICROSOFT_LIVE_LOGIN) {
    include_once dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'microsoft-live-auth-config.php';
}
// Google login
define('GOOGLE_LOGIN', true);
if (GOOGLE_LOGIN) {
    include_once dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'google-auth-config.php';
}
