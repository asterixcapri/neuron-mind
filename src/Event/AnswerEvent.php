<?php

namespace NeuronMind\Event;

use NeuronAI\Workflow\Event;

class AnswerEvent implements Event 
{
    public function __construct(public array $searchResults)
    {
    }
}
