<?php

namespace NeuronMind\Logger;

class SimpleLogger
{
    public static function info($message, $data = null, $truncate = true)
    {
        echo $message;

        if (is_string($data)) {
            echo $truncate ? self::truncate($data) : $data;
        }
        elseif (is_array($data)) {
            echo json_encode(array_map(fn($item) => $truncate ? self::truncate($item) : $item, $data));
        }
        elseif (!is_null($data)) {
            echo json_encode($data);
        }

        echo "\n";
    }

    private static function truncate(string $s): string
    {
        return trim(substr(preg_replace('/\s+/', ' ', $s), 0, 160)).'...';
    }
}
