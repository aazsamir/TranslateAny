<?php

declare(strict_types=1);

namespace App\System\Chat;

class TextTrimmer
{
    public static function trim(?string $text): string
    {
        if ($text === null) {
            return '';
        }

        $text = preg_replace('/<think>.*?<\/think>/s', '', $text) ?? '';
        $text = preg_replace('/<thinking>.*?<\/thinking>/s', '', $text) ?? '';
        $text = str_replace(['<think>', '</think>', '<thinking>', '</thinking>'], '', $text);
        $text = trim($text, "\n\r\t\v\0 #");

        return $text;
    }
}
