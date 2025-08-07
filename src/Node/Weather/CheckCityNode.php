<?php

namespace NeuronMind\Node\Weather;

use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class CheckCityNode extends Node
{
    public function run(WorkflowState $state): WorkflowState
    {
        if (!$state->has('city')) {
            $this->interrupt([
                'type' => 'fill_slot',
                'slot_name' => 'city',
                'question' => 'Per quale città vuoi conoscere il meteo?',
            ]);
        }

        return $state;
    }
}
