<?php

use Components\Html;

$prefixes = ['solo', 'duos'];

echo '<div class="flex md:flex-col flex-row justify-center items-center">';
    foreach ($prefixes as $prefix) {
        echo '<div class="my-2 max-w-md flex flex-col bg-gray-100 dark:bg-gray-900 rounded items-center">';
            echo '<div class="my-4 p-2 flex flex-row justify-center items-center">';
                echo '<img src="/assets/images/' . $prefix . '_Icon.webp" class="w-14 h-14" alt="' . $prefix . ' Icon">';
                echo HTML::h3(ucfirst($prefix) . ' Leaderboards');
            echo '</div>';
            echo '<div class="flex flex-col max-w-fit p-2 items-center">';
                echo HTML::bigButtonLink('/' . $prefix . '/us', '&#x1F1FA;&#x1F1F8; Leaderboard', $theme);
                echo HTML::bigButtonLink('/' . $prefix . '/eu', '&#x1F1EA;&#x1F1FA; Leaderboard', $theme);
                echo HTML::bigButtonLink('/' . $prefix . '/ap', '&#x1F1E8;&#x1F1F3; Leaderboard', $theme);
            echo '</div>';
        echo '</div>';
    }
echo '</div>';
