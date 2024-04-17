<?php

use Components\Html;

// Center the first div
echo '<div class="flex justify-center items-center">';
    echo '<div class="flex items-center flex-col justify-center m-4 max-w-fit">';
        echo HTML::bigButtonLink('/us', '&#x1F1FA;&#x1F1F8; Leaderboard', $theme);
        echo HTML::bigButtonLink('/eu', '&#x1F1EA;&#x1F1FA; Leaderboard', $theme);
        echo HTML::bigButtonLink('/ap', '&#x1F1E8;&#x1F1F3; Leaderboard', $theme);
    echo '</div>';
echo '</div>';
