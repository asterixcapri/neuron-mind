<?php

namespace NeuronMind\Node;

use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class CheckCityNode extends Node
{
    public function run(WorkflowState $state): WorkflowState
    {
        if (!$state->has('city')) {
            $response = $this->interrupt([
                'question' => 'What city do you want to know the weather for?',
            ]);

            $state->set('city', $response['city']);
        }

        return $state;
    }
}
