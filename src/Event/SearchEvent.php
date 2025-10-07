<?php

namespace NeuronMind\Event;

use NeuronAI\Workflow\Event;
use NeuronMind\Dto\QueryWriterDto;

class SearchEvent implements Event 
{
    public function __construct(public array $queries)
    {
    }
}
