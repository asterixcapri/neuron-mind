<?php

namespace NeuronMind\Logger;

class SimpleLogger
{
    public static function info($message, $data = null, $truncate = true)
    {
        echo $message;

        if (is_string($data)) {
            $output = $truncate ? substr(preg_replace('/\s+/', ' ', $data), 0, 80).'...' : $data;
            echo trim($output);
        }
        elseif (is_array($data)) {
            $output = json_encode(array_map(function($result) use ($truncate) {
                return $truncate ? substr(preg_replace('/\s+/', ' ', $result), 0, 50).'...' : $result;
            }, $data));

            echo trim($output);
        }

        echo "\n";
    }
}
