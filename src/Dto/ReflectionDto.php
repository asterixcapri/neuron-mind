<?php

namespace NeuronMind\Dto;

use NeuronAI\StructuredOutput\SchemaProperty;

class ReflectionDto
{
    #[SchemaProperty(description: 'Whether the answer is sufficient.', required: true)]
    public bool $isSufficient = false;

    #[SchemaProperty(description: 'The knowledge gap if the answer is not sufficient.', required: true)]
    public string $knowledgeGap = '';

    #[SchemaProperty(description: 'The follow-up queries if the answer is not sufficient.', required: true)]
    public array $followUpQueries = [];
}
