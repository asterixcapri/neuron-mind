<?php

namespace NeuronMind\Dto;

use NeuronAI\StructuredOutput\SchemaProperty;

class QueryWriterDto
{
    #[SchemaProperty(description: 'The rationale for the queries.', required: true)]
    public string $rationale = '';

    #[SchemaProperty(description: 'The queries to be used for the search.', required: true)]
    public array $queries = [];
}
