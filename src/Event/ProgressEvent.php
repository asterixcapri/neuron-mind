<?php

namespace NeuronMind\Event;

use NeuronAI\Workflow\Event;

class ProgressEvent implements Event
{
    public function __construct(public string $message, public mixed $data = null)
    {
    }
}
