<?php

namespace App\Markdown;

use Components\Alerts;
use Parsedown;

class Page
{
    public static function render($fileName, $theme)
    {
        // Now let's get the contents of the file
        if (file_exists($fileName . '.md') === false) {
            http_response_code(404);
            return Alerts::danger('page does not exist');
        }
        $fileContents = file_get_contents($fileName . '.md');
        // Now let's render the markdown
        $html = '<article class="mx-6 my-4 max-w-full p-6 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-400 prose prose-md prose-' . $theme . ' dark:prose-invert">';
            $html .= Parsedown::instance()->text($fileContents);
        $html .= '</article>';
        return $html;
    }
    public static function renderRemote($url, $theme)
    {
        // Now let's get the contents of the file
        $fileContents = file_get_contents($url);
        if (empty($fileContents)) {
            http_response_code(404);
            return Alerts::danger('Could not fetch remote page');
        }
        // Now let's render the markdown
        $html = '<article class="mx-6 my-4 max-w-full p-6 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-400 prose prose-md prose-' . $theme . ' dark:prose-invert">';
            $html .= Parsedown::instance()->text($fileContents);
        $html .= '</article>';
        return $html;
    }
    public static function getMdFilesInDir($dir)
    {
        $files = scandir($dir);
        $files = array_diff($files, ['.', '..', 'index.php']);
        $files = array_map(function ($file) {
            return str_replace('.md', '', $file);
        }, $files);
        return $files;
    }
    public static function getMetaDataFromMd($file, $folder) : array
    {
        $content = file_get_contents($folder . '/' . $file . '.md');
        // Find the rows that start with [XXXX] : <> and use them as metadata
        preg_match_all('/\[(\w+)]: # \(([^)]+)\)/', $content, $matches, PREG_SET_ORDER);

        $results = array();
        foreach ($matches as $match) {
            $results[$match[1]] = $match[2];
        }

        $title = (isset($results['title'])) ? $results['title'] : ucfirst(str_replace('-', ' ', basename($_SERVER['REQUEST_URI'])));
        $description = (isset($results['description'])) ? $results['description'] : GENERIC_DESCRIPTION;
        $keywords = (isset($results['keywords'])) ? $results['keywords'] : GENERIC_KEYWORDS;
        $thumbimage = (isset($results['thumbimage'])) ? $results['thumbimage'] : OG_LOGO;
        $menu = (isset($results['menu'])) ? $results['menu'] : MAIN_MENU;
        $genericMetaDataArray = [
            'metadata' => [
                // Title will be the uppercased file name
                'title' => $title,
                'description' => $description,
                'keywords' => $keywords,
                'thumbimage' => $thumbimage,
                'menu' => $menu,
            ]
        ];
        return $genericMetaDataArray;
    }
}
