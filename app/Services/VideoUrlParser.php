<?php

namespace App\Services;

class VideoUrlParser
{
    public static function parse($url)
    {

        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=|shorts\/)|youtu\.be\/)([^"&?\/\s]{11})/';
        
        if (preg_match($pattern, $url, $matches)) {
            return [
                'platform' => 'youtube',
                'id' => $matches[1],
                'url' => $url
            ];
        }

        // TikTok
        
        return null;
    }
}