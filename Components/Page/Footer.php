<?php

namespace Components\Page;

use Components\Html;

class Footer
{
    public static function render($theme)
    {
        $html = '';
        $html .= '<footer class="p-4 bg-white sm:p-6 dark:bg-gray-900">' . PHP_EOL;
        $html .= '<div class="md:flex md:justify-between">';
        $html .= '<div class="md:flex md:justify-between">';
        $html .= '<a href="/" class="flex items-center"><img src="' . LOGO . '" alt="logo" class="w-10 h-10 rounded-lg object-cover spin" /><span class="ml-2 text-xl font-bold text-gray-800 dark:text-gray-200">' . SITE_TITLE . '</span></a>';
        $html .= '</div>';
        // Start grid
        $html .= '<div class="grid grid-cols-3 gap-8 sm:gap-6 sm:grid-cols-4">';
        $footerMenuArray = [
            'Report Issues' => [
                'Submit an issue' => '/report-issue',
            ],
            'About us' => [
                'About us' => '/about-us',
                'Contact us' => '/contact-us',
            ],
            'Legal' => [
                'Terms of service' => '/terms-of-service',
                'Privacy policy' => '/privacy-policy',
            ]
        ];
        foreach ($footerMenuArray as $title => $meta) {
            $html .= '<div>';
            $html .= HTML::h3($title);
            $html .= '<ul class="mt-2 leading-6 text-gray-500 dark:text-gray-400">';
            foreach ($meta as $linkTitle => $link) {
                $html .= '<li><a href="' . $link . '" class="hover:underline">' . $linkTitle . '</a></li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</footer>' . PHP_EOL;
        // Scroll to top button
        $html .= '<button type="button" data-mdb-ripple="true" data-mdb-ripple-color="light" class="inline-block p-3 bg-' . $theme . '-600 text-white font-medium text-xs leading-tight uppercase rounded-full shadow-md hover:bg-' . $theme . '-700 hover:shadow-lg focus:bg-' . $theme . '-700 focus:shadow-lg focus:outline-none focus:ring-2 active:bg-' . $theme . '-800 active:shadow-lg transition duration-150 ease-in-out bottom-5 right-5 fixed hidden" id="btn-back-to-top">';
        $html .= '<svg aria-hidden="true" focusable="false" data-prefix="fas" class="w-4 h-4" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
        <path fill="currentColor" d="M34.9 289.5l-22.2-22.2c-9.4-9.4-9.4-24.6 0-33.9L207 39c9.4-9.4 24.6-9.4 33.9 0l194.3 194.3c9.4 9.4 9.4 24.6 0 33.9L413 289.4c-9.5 9.5-25 9.3-34.3-.4L264 168.6V456c0 13.3-10.7 24-24 24h-32c-13.3 0-24-10.7-24-24V168.6L69.2 289.1c-9.3 9.8-24.8 10-34.3.4z"></path></svg>';
        $html .= '</button>';
        $html .= '<input type="hidden" name="theme" value="' . $theme . '" />';
        return $html;
    }
}
