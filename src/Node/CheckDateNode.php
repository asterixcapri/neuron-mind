<?php

namespace NeuronMind\Node;

use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class CheckDateNode extends Node
{
    public function run(WorkflowState $state): WorkflowState
    {
        if (!$state->has('date')) {
            $this->interrupt([
                'type' => 'fill_slot',
                'slot_name' => 'date',
                'question' => 'Per quale giorno vuoi le previsioni del tempo?',
            ]);
        }

        return $state;
    }
}
