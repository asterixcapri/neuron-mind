<?php

namespace NeuronMind\Logger;

class SimpleLogger
{
    public static function info($message, $data = null)
    {
        echo $message;

        if (is_string($data)) {
            echo substr(preg_replace('/\s+/', ' ', $data), 0, 80).'...';
        }
        elseif (is_array($data)) {
            echo json_encode(array_map(fn($result) => substr(preg_replace('/\s+/', ' ', $result), 0, 50).'...', $data));
        }

        echo "\n";
    }
}
