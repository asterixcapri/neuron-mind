<?php

namespace NeuronMind\Service;

use NeuronAI\StructuredOutput\JsonExtractor as NeuronAIJsonExtractor;

class JsonExtractor extends NeuronAIJsonExtractor
{
    public function getData(string $output, ?bool $associative = null): mixed
    {
        $json = (new NeuronAIJsonExtractor())->getJson($output);
        return json_decode($json, $associative);
    }
}
