<?php

use Components\Html;

$prefixes = ['solo', 'duos'];

echo '<div class="flex md:flex-col flex-row justify-center items-center">';
    foreach ($prefixes as $prefix) {
        echo '<div class="m-2 p-2 max-w-md flex flex-col bg-orange-300 dark:bg-gray-900 rounded items-center overflow-auto">';
            echo '<div class="my-4 p-2 flex flex-row flex-wrap justify-center items-center">';
                echo '<img src="/assets/images/' . $prefix . '_Icon.webp" class="w-14 h-14" alt="' . $prefix . ' Icon">';
                echo HTML::h3(ucfirst($prefix) . ' Leaderboards');
            echo '</div>';
            echo '<div class="flex flex-col max-w-fit p-2 items-center">';
                echo HTML::bigButtonLink('/' . $prefix . '/eu', 'Europe Leaderboard', $theme);
                echo HTML::bigButtonLink('/' . $prefix . '/us', 'Americas Leaderboard', $theme);
                echo HTML::bigButtonLink('/' . $prefix . '/ap', 'Asia-Pacific Leaderboard', $theme);
                echo HTML::bigButtonLink('/' . $prefix . '/combined-leaderboard', 'Combined Leaderboard', $theme);
            echo '</div>';
        echo '</div>';
    }
echo '</div>';
