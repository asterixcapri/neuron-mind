<?php

namespace NeuronMind\Service;

class JsonOutputParser
{
    public function parse(string $output, ?bool $associative = null): mixed
    {
        $output = preg_replace('/^[``json]+|[```]+$/', '$1', $output);
        return json_decode($output, $associative);
    }
}
