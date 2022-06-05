<?php

namespace App\Service;

class Slugify{

    public function generate(string $input): string {
        $target = [' ', 'à', 'ç','\'', '.' ];
        $replace = ['-', 'a', 'c', '',''];
        $slug = str_replace($target,$replace,$input);
        $cleanSlug = str_replace('--', '-', $slug);

        return trim(strtolower($cleanSlug));
    }
}