<?php

namespace NeuronMind\Event;

use NeuronAI\Workflow\Event;

class ReflectEvent implements Event 
{
    public function __construct(public array $searchResults)
    {
    }
}
