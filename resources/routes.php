<?php

use FastRoute\RouteCollector;
use App\Markdown\Page;

return function (RouteCollector $router) {
    $viewsFolder = dirname($_SERVER['DOCUMENT_ROOT']) . '/Views';
    // include the menu data
    require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/menus/menus.php';
    $title = urldecode(ucfirst(str_replace('-', ' ', basename($_SERVER['REQUEST_URI']))));

    $genericMetaDataArray = [
        'metadata' => [
            // For title we need to extract the page title from the request URI and capitalize the first letter
            'title' => (!empty($title)) ? $title : 'Home',
            'description' => GENERIC_DESCRIPTION,
            'keywords' => GENERIC_KEYWORDS,
            'thumbimage' => OG_LOGO,
            'menu' => MAIN_MENU,
        ]
    ];
    $genericMetaAdminDataArray = [
        'metadata' => [
            'title' => (!empty($title)) ? $title : 'Home',
            'description' => GENERIC_DESCRIPTION,
            'keywords' => GENERIC_KEYWORDS,
            'thumbimage' => OG_LOGO,
            'menu' => ADMIN_MENU,
        ]
    ];
    /* Views */
    // Landing
    // Root page
    $router->addRoute('GET', '/', [$viewsFolder . '/landing/main.php', $genericMetaDataArray]);
    $router->addRoute('GET', '/contact', [$viewsFolder . '/landing/contact.php', $genericMetaDataArray]);
    $router->addRoute('GET', '/player/{season:\d+}/{type}/{region}', [$viewsFolder . '/battlegrounds/player.php', [
        'metadata' => [
            'title' => explode('=', $title)[1] ?? 'Player',
            'description' => explode('=', $title)[1] ?? 'Player' . ' page for the Hearthstone Battlegrounds. Find out the rating progression and rank progression for a player in a specific season, region and type.',
            'keywords' => ['hearthstone battlegrounds', explode('=', $title)[1] ?? 'Player', 'rating progression', 'rank progression', 'season', 'region', 'type'],
            'thumbimage' => OG_LOGO,
            'menu' => MAIN_MENU,
        ]
    ]]);
    // Solo pages
    $router->addRoute('GET', '/solo/eu', [$viewsFolder . '/battlegrounds/show-leaderboard.php', [
        'metadata' => [
            // For title we need to extract the page title from the request URI and capitalize the first letter
            'title' => 'Solo EU Season 7 Leaderboard',
            'description' => 'Leaderboard for the Solo EU Season 7 Hearthstone Battlegrounds. Find out who is the best player in the EU region is and find yourself in the rankings with our search tool. We also have a rating progression chart for each player.',
            'keywords' => ['hearthstone battlegrounds', 'leaderboard', 'solo', 'eu', 'season 7', 'rating progression', 'chart'],
            'thumbimage' => OG_LOGO,
            'menu' => MAIN_MENU,
        ]
    ]]);
    $router->addRoute('GET', '/solo/us', [$viewsFolder . '/battlegrounds/show-leaderboard.php', [
        'metadata' => [
            // For title we need to extract the page title from the request URI and capitalize the first letter
            'title' => 'Solo US Season 7 Leaderboard',
            'description' => 'Leaderboard for the Solo US Season 7 Hearthstone Battlegrounds. Find out who is the best player in the US region is and find yourself in the rankings with our search tool. We also have a rating progression chart for each player.',
            'keywords' => ['hearthstone battlegrounds', 'leaderboard', 'solo', 'us', 'season 7', 'rating progression', 'chart'],
            'thumbimage' => OG_LOGO,
            'menu' => MAIN_MENU,
        ]
    ]]);
    $router->addRoute('GET', '/solo/ap', [$viewsFolder . '/battlegrounds/show-leaderboard.php', [
        'metadata' => [
            // For title we need to extract the page title from the request URI and capitalize the first letter
            'title' => 'Solo AP Season 7 Leaderboard',
            'description' => 'Leaderboard for the Solo Asia-Pacific Season 7 Hearthstone Battlegrounds. Find out who is the best player in the AP region is and find yourself in the rankings with our search tool. We also have a rating progression chart for each player.',
            'keywords' => ['hearthstone battlegrounds', 'leaderboard', 'solo', 'ap', 'season 7', 'rating progression', 'chart'],
            'thumbimage' => OG_LOGO,
            'menu' => MAIN_MENU,
        ]
    ]]);

    // Duos pages
    $router->addRoute('GET', '/duos/eu', [$viewsFolder . '/battlegrounds/show-leaderboard.php', [
        'metadata' => [
            // For title we need to extract the page title from the request URI and capitalize the first letter
            'title' => 'Duos EU Season 7 Leaderboard',
            'description' => 'Leaderboard for the Duos EU Season 7 Hearthstone Battlegrounds. Find out who is the best player in the EU region is and find yourself in the rankings with our search tool. We also have a rating progression chart for each player.',
            'keywords' => ['hearthstone battlegrounds', 'leaderboard', 'duos', 'eu', 'season 7', 'rating progression', 'chart'],
            'thumbimage' => DUOS_LOGO,
            'menu' => MAIN_MENU,
        ]
    ]]);
    $router->addRoute('GET', '/duos/us', [$viewsFolder . '/battlegrounds/show-leaderboard.php', [
        'metadata' => [
            // For title we need to extract the page title from the request URI and capitalize the first letter
            'title' => 'Duos US Season 7 Leaderboard',
            'description' => 'Leaderboard for the Duos US Season 7 Hearthstone Battlegrounds. Find out who is the best player in the US region is and find yourself in the rankings with our search tool. We also have a rating progression chart for each player.',
            'keywords' => ['hearthstone battlegrounds', 'leaderboard', 'duos', 'us', 'season 7', 'rating progression', 'chart'],
            'thumbimage' => DUOS_LOGO,
            'menu' => MAIN_MENU,
        ]
    ]]);
    $router->addRoute('GET', '/duos/ap', [$viewsFolder . '/battlegrounds/show-leaderboard.php', [
        'metadata' => [
            // For title we need to extract the page title from the request URI and capitalize the first letter
            'title' => 'Duos AP Season 7 Leaderboard',
            'description' => 'Leaderboard for the Duos Asia-Pacific Season 7 Hearthstone Battlegrounds. Find out who is the best player in the AP region is and find yourself in the rankings with our search tool. We also have a rating progression chart for each player.',
            'keywords' => ['hearthstone battlegrounds', 'leaderboard', 'duos', 'ap', 'season 7', 'rating progression', 'chart'],
            'thumbimage' => DUOS_LOGO,
            'menu' => MAIN_MENU,
        ]
    ]]);

    $router->addRoute('GET', '/6/eu', [$viewsFolder . '/battlegrounds/show-leaderboard.php', $genericMetaDataArray]);
    $router->addRoute('GET', '/6/us', [$viewsFolder . '/battlegrounds/show-leaderboard.php', $genericMetaDataArray]);
    $router->addRoute('GET', '/6/ap', [$viewsFolder . '/battlegrounds/show-leaderboard.php', $genericMetaDataArray]);

    // HS Api Routes
    $router->addRoute('POST', '/api/eu/6/record', [$viewsFolder . '/api/eu/6/record.php']);
    $router->addRoute('POST', '/api/us/6/record', [$viewsFolder . '/api/us/6/record.php']);
    $router->addRoute('POST', '/api/ap/6/record', [$viewsFolder . '/api/ap/6/record.php']);

    $router->addRoute('GET', '/api/eu/6/get', [$viewsFolder . '/api/eu/6/get.php']);
    $router->addRoute('GET', '/api/us/6/get', [$viewsFolder . '/api/us/6/get.php']);
    $router->addRoute('GET', '/api/ap/6/get', [$viewsFolder . '/api/ap/6/get.php']);

    $router->addRoute('GET', '/api/6/eu/get', [$viewsFolder . '/api/battlegrounds/get.php']);
    $router->addRoute('GET', '/api/6/us/get', [$viewsFolder . '/api/battlegrounds/get.php']);
    $router->addRoute('GET', '/api/6/ap/get', [$viewsFolder . '/api/battlegrounds/get.php']);

    $router->addRoute('POST', '/api/6/eu/record', [$viewsFolder . '/api/battlegrounds/record.php']);
    $router->addRoute('POST', '/api/6/us/record', [$viewsFolder . '/api/battlegrounds/record.php']);
    $router->addRoute('POST', '/api/6/ap/record', [$viewsFolder . '/api/battlegrounds/record.php']);

    // Season 7 Api Routes
    $router->addRoute('POST', '/api/7/duos/eu/record', [$viewsFolder . '/api/battlegrounds/record.php']);
    $router->addRoute('POST', '/api/7/duos/us/record', [$viewsFolder . '/api/battlegrounds/record.php']);
    $router->addRoute('POST', '/api/7/duos/ap/record', [$viewsFolder . '/api/battlegrounds/record.php']);

    $router->addRoute('POST', '/api/7/solo/eu/record', [$viewsFolder . '/api/battlegrounds/record.php']);
    $router->addRoute('POST', '/api/7/solo/us/record', [$viewsFolder . '/api/battlegrounds/record.php']);
    $router->addRoute('POST', '/api/7/solo/ap/record', [$viewsFolder . '/api/battlegrounds/record.php']);

    $router->addRoute('GET', '/api/7/solo/eu/get', [$viewsFolder . '/api/battlegrounds/get.php']);
    $router->addRoute('GET', '/api/7/solo/us/get', [$viewsFolder . '/api/battlegrounds/get.php']);
    $router->addRoute('GET', '/api/7/solo/ap/get', [$viewsFolder . '/api/battlegrounds/get.php']);

    $router->addRoute('GET', '/api/7/duos/eu/get', [$viewsFolder . '/api/battlegrounds/get.php']);
    $router->addRoute('GET', '/api/7/duos/us/get', [$viewsFolder . '/api/battlegrounds/get.php']);
    $router->addRoute('GET', '/api/7/duos/ap/get', [$viewsFolder . '/api/battlegrounds/get.php']);

    // Login page
    $router->addRoute('GET', '/login', [$viewsFolder . '/landing/login.php', $genericMetaDataArray]);
    // Install apge
    //$router->addRoute('GET', '/install', [$viewsFolder . '/landing/install.php', $genericMetaDataArray]);
    // Register page
    $router->addRoute('GET', '/register', [$viewsFolder . '/landing/register.php', $genericMetaDataArray]);
    // User settings page
    $router->addRoute('GET', '/user-settings', [$viewsFolder . '/landing/user-settings.php', $genericMetaDataArray]);

    // Example
    $router->addRoute('GET', '/charts', [$viewsFolder . '/example/charts.php', $genericMetaDataArray]);
    $router->addRoute('GET', '/forms', [$viewsFolder . '/example/forms.php', $genericMetaDataArray]);
    $router->addRoute('GET', '/datagrid', [$viewsFolder . '/example/datagrid.php', $genericMetaDataArray]);
    
    // Auth verify page
    $router->addRoute('POST', '/auth-verify', [$viewsFolder . '/auth-verify.php']);
    $router->addRoute('GET', '/auth-verify', [$viewsFolder . '/auth-verify.php']);
    // Logout page
    $router->addRoute('GET', '/logout', [$viewsFolder . '/logout.php']);
    // CSP report endpoiont
    $router->addRoute('POST', '/csp-report', [$viewsFolder . '/csp-report.php']);
    // Admin
    $router->addRoute('GET', '/adminx', [$viewsFolder . '/admin/index.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/server', [$viewsFolder . '/admin/server.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/users', [$viewsFolder . '/admin/users.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/cache', [$viewsFolder . '/admin/cache.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/system-logs', [$viewsFolder . '/admin/system-logs.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/csp-reports', [$viewsFolder . '/admin/csp/csp-reports.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/csp-approved-domains', [$viewsFolder . '/admin/csp/csp-approved-domains.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/firewall', [$viewsFolder . '/admin/firewall.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/queries', [$viewsFolder . '/admin/queries.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/mailer', [$viewsFolder . '/admin/mailer.php', $genericMetaAdminDataArray]);
    $router->addRoute('GET', '/adminx/base64', [$viewsFolder . '/admin/tools/base64encode.php', $genericMetaAdminDataArray]);

    // Admin API
    $router->addRoute('POST', '/api/admin/csp/add', [$viewsFolder . '/api/admin/csp/add.php']);
    $router->addRoute('POST', '/api/admin/queries', [$viewsFolder . '/api/admin/queries.php']);

    // Tools API
    $router->addRoute('POST', '/api/tools/get-error-file', [$viewsFolder . '/api/tools/get-error-file.php']);
    $router->addRoute('POST', '/api/tools/clear-error-file', [$viewsFolder . '/api/tools/clear-error-file.php']);
    $router->addRoute('POST', '/api/tools/export-csv', [$viewsFolder . '/api/tools/export-csv.php']);
    $router->addRoute('POST', '/api/tools/export-tsv', [$viewsFolder . '/api/tools/export-tsv.php']);
    $router->addRoute('POST', '/api/tools/base64encode', [$viewsFolder . '/api/tools/base64encode.php']);
    $router->addRoute('POST', '/api/tools/php-info-parser', [$viewsFolder . '/api/tools/php-info-parser.php']);

    /* API Routes */
    $router->addRoute(['GET','PUT','DELETE','POST'], '/api/user[/{id:\d+}]', [$viewsFolder . '/api/user.php']);
    $router->addRoute(['GET','PUT','DELETE','POST'], '/api/firewall[/{id:\d+}]', [$viewsFolder . '/api/firewall.php']);
    $router->addRoute('POST', '/api/mail/send', [$viewsFolder . '/api/mail/send.php']);

    /* DataGrid Api */
    $router->addRoute('POST', '/api/datagrid/get-records', [$viewsFolder . '/api/datagrid/get-records.php']);
    $router->addRoute('POST', '/api/datagrid/update-records', [$viewsFolder . '/api/datagrid/update-records.php']);
    $router->addRoute('POST', '/api/datagrid/delete-records', [$viewsFolder . '/api/datagrid/delete-records.php']);

    // Docs pages markdown auto routing for /docs
    $docsFolder = '/docs';
    $markDownFolder = $viewsFolder . $docsFolder;
    $router->addRoute('GET', '/docs', [$markDownFolder . '/index.php', Page::getMetaDataFromMd('index', $markDownFolder)]);
    // Search the /docs for files and build a route for each file
    $docFiles = Page::getMdFilesInDir($viewsFolder . '/docs');
    foreach ($docFiles as $file) {
        $router->addRoute('GET', '/docs/' . $file, [$markDownFolder . '/index.php', Page::getMetaDataFromMd($file, $markDownFolder)]);
    }

    // Test
    $router->addRoute('GET', '/test', [$viewsFolder . '/test.php', $genericMetaDataArray]);
    // API Example
    $router->addRoute(['PUT', 'DELETE'], '/api/example/{id:\d+}', [$viewsFolder . '/api/example.php']);
    $router->addRoute('POST', '/api/example', [$viewsFolder . '/api/example.php']);
};
