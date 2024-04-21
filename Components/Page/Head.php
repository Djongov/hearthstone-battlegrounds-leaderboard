<?php

namespace Components\Page;

class Head
{
    public static function render(string $title, string $description, array $keywords, string $thumbimage, array $scriptsArray, array $cssArray)
    {
        $html = '';
        $html .= '<!DOCTYPE html>' . PHP_EOL;
        $html .= '<html lang="en" class="h-full">' . PHP_EOL;
        $html .= '<head>' . PHP_EOL;
        $html .=  '<title>' . $title . ' - ' . SITE_TITLE . '</title>' . PHP_EOL;
        // Icon
        $html .= '<link rel="icon" type="image/x-icon" href="' . LOGO . '" >' . PHP_EOL;
        // General Meta tags
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1">' . PHP_EOL;
        $html .= '<meta charset="UTF-8">' . PHP_EOL;
        $html .= '<meta name="robots" content="index, follow" >' . PHP_EOL;
        $html .= '<meta name="author" content="Dimitar Dzhongov" >' . PHP_EOL;
        $html .= '<meta name="keywords" content="' . implode(",", $keywords) . '" >' . PHP_EOL;
        $html .= '<meta name="description" content="' . $description . '" >' . PHP_EOL;
        // Og tags
        $html .= self::ogTags($title, $description, $thumbimage);
        // Twitter tags
        $html .= self::twitterTags($title, $description, $thumbimage, '@Djongov', '@Djongov');
        // CSS files
        $html .= self::cssLoad($cssArray);
        // Scripts
        $html .= self::scriptLoad($scriptsArray);
        // Inline scripts
        $tailwindConfig = file_get_contents(dirname($_SERVER['DOCUMENT_ROOT']) . '/tailwind.config.js');
        $html .= <<< InlineScript
                <script nonce="1nL1n3JsRuN1192kwoko2k323WKE">
                    $tailwindConfig
                </script>
                InlineScript . PHP_EOL;
        $html .= '</head>' . PHP_EOL;
        return $html;
    }
    public static function ogTags(string $title, string $description, string $thumbimage) : string
    {
        $html = '';
        $html .= '<meta property="og:type" content="website" >' . PHP_EOL;
        $html .= '<meta property="og:url" content="https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" >' . PHP_EOL;
        $html .= '<meta property="og:title" content="' . $title . '" >' . PHP_EOL;
        $html .= '<meta property="og:description" content="' . $description . '" >' . PHP_EOL;
        $html .= '<meta property="og:image" content="' . $thumbimage . '" >' . PHP_EOL;
        $html .= '<meta property="og:image:width" content="250">' . PHP_EOL;
        $html .= '<meta property="og:image:height" content="236">' . PHP_EOL;
        $html .= '<meta property="og:image:type" content="image/png">' . PHP_EOL;
        return $html;
    }
    public static function twitterTags(string $title, string $description, string $thumbimage, string $site, string $creator) : string
    {
        $html = '';
        $html .= '<meta name="twitter:card" content="summary_large_image" >' . PHP_EOL;
        $html .= '<meta name="twitter:site" content="' . $site . '" >' . PHP_EOL;
        $html .= '<meta name="twitter:creator" content="' . $creator . '" >' . PHP_EOL;
        $html .= '<meta name="twitter:title" content="' . $title . '" >' . PHP_EOL;
        $html .= '<meta name="twitter:description" content="' . $description . '" >' . PHP_EOL;
        $html .= '<meta name="twitter:image" content="' . $thumbimage . '" >' . PHP_EOL;
        $html .= '<meta name="twitter:image:alt" content="' . SITE_TITLE . ' logo" >' . PHP_EOL;
        return $html;
    }
    public static function scriptLoad(array $scriptArray)
    {
        $html = '';
        foreach ($scriptArray as $link => $value) {
            if (is_array($value)) {
                $defaultValue = false;
                $defer = $value['defer'] ?? $defaultValue;
                $async = $value['async'] ?? $defaultValue;
                $cache = '?=' . time() ?? '';
                $integrity = $value['integrity'] ?? $defaultValue;
                $crossorigin = $value['crossorigin'] ?? $defaultValue;
                $html .= '<script src="' . $link . $cache . '" ' . ($defer ? 'defer' : '') . ' ' . ($async ? 'async' : '') . ' ' . ($integrity ? 'integrity="' . $integrity . '"' : '') . ' ' . ($crossorigin ? 'crossorigin="' . $crossorigin . '"' : '') . '></script>' . PHP_EOL;
            } else {
                $html .= '<script src="' . $value . '"></script>' . PHP_EOL;
            }
        }
        return $html;
    }
    public static function cssLoad(array $cssArray)
    {
        $html = '';
        foreach ($cssArray as $link) {
            $cache = '?=' . time() ?? '';
            $html .= '<link rel="stylesheet" href="' . $link . $cache . '">' . PHP_EOL;
        }
        return $html;
    }
}
