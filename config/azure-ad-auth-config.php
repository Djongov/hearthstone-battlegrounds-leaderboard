<?php

// Azure App registratin client id
define('AZURE_AD_CLIENT_ID', $_ENV['AZURE_AD_CLIENT_ID']);
// Azure App registration tenant id
define('AZURE_AD_TENANT_ID', $_ENV['AZURE_AD_TENANT_ID']);

if (AZURE_AD_LOGIN) {
    // if this env var is available, then we must be deployed into an app service and therefore control the auth settings from the app's settings as various env variable set by the platform
    if (getenv('WEBSITE_AUTH_CLIENT_ID')) {
        // The only exposure of the tenant is the openid_issuer env var, but it's a url so we use regex to catch the tenant id from it
        preg_match('/https:\/\/sts.windows.net\/(.*?)\/v2.0/', getenv('WEBSITE_AUTH_OPENID_ISSUER'), $match);
        define('AZURE_AD_TENANT_ID', $match[1]);
        // The client id is in this environmental variable
        define('AZURE_AD_CLIENT_ID', getenv('WEBSITE_AUTH_CLIENT_ID'));
        // The client secret is in this environmental variable. It's not actually being used anywhere here.
        define('AZURE_AD_CLIENT_SECRET', getenv('MICROSOFT_PROVIDER_AUTHENTICATION_SECRET'));
        // Let's build the oauth URL which includes the tenant. This is where we will be sending the request to login
        define('OAUTHURL', 'https://login.microsoftonline.com/' . AZURE_AD_TENANT_ID . '/oauth2/v2.0/authorize?');
        // Let's form what the login url will be

        define('AZURE_AD_LOGIN_BUTTON_URL', '/.auth/login/aad?post_login_redirect_uri=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        // And the logout URL
        define('AZURE_AD_LOGOUT_BUTTON_URL', '/.auth/logout?post_logout_redirect_uri=/');
        // The refresh token URL, for when the token needs to be refreshed. Note that here we generate a random nonce which is supposed to be kept somewhere like in a database. This is why we currently use a static nonce
        define('REFRESH_TOKEN_URL', 'https://login.microsoftonline.com/' . $match[1] . '/oauth2/v2.0/authorize?response_type=code+id_token&redirect_uri=' . $_SERVER['HTTP_HOST'] . '/.auth/login/aad/callback&client_id=' . getenv('APPSETTING_WEBSITE_AUTH_CLIENT_ID') . '&scope=openid+profile+email&response_mode=form_post&nonce=' .
            $_SESSION['nonce'] ?? null  . '&state=redir=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        // We end up here if we are using our own App Registration
    } else {
        // This is how we form the redirect URL. Note that https:// is hardcoded, which is fine as app registrations do not allow for http:// unless it's http://localhost.
        define('REDIRECT_URI', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/auth-verify');
        // Let's build the oauth URL which includes the tenant. This is where we will be sending the request to login
        //define('OAUTHURL', 'https://login.microsoftonline.com/organizations/oauth2/v2.0/authorize?');
        define('OAUTHURL', 'https://login.microsoftonline.com/' . AZURE_AD_TENANT_ID . '/oauth2/v2.0/authorize?');

        $authenticationData = [
            'client_id' => AZURE_AD_CLIENT_ID,
            'response_type' => 'id_token',
            'redirect_uri' => REDIRECT_URI,
            'response_mode' => 'form_post',
            'scope' => 'openid profile email',
            // Note that the nonce is supposed to be checked on return but you need special settings to keep it somewhere, like in a database. This is why we currently use a static nonce but i leave here a line with random nonce
            'nonce' => $_SESSION['nonce'] ?? null,
            //'nonce' => 'c0ca2663770b3c9571ca843c7106851816e2d415e77369a1',
            'state' => $destination
        ];
        // This basically merges OAUTH URL and $data
        $request_id_token_url = OAUTHURL . http_build_query($authenticationData);
        // Let's form what the login url will be
        define('AZURE_AD_LOGIN_BUTTON_URL', $request_id_token_url);
        // For this one, the logout will be our own script
        define('AZURE_AD_LOGOUT_BUTTON_URL', 'https://login.microsoftonline.com/' . AZURE_AD_TENANT_ID . '/oauth2/v2.0/logout?post_logout_redirect_uri=https://' . $_SERVER['HTTP_HOST']);
    }
}
