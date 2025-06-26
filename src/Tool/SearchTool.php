<?php

namespace NeuronMind\Tool;

use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\ToolProperty;
use NeuronAI\Tools\Tool;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Workflow\SearchWorkflow;

class SearchTool extends Tool
{
    public function __construct()
    {
        parent::__construct(
            'search',
            'Search information for a given question'
        );
    }

    protected function properties(): array
    {
        return [
            new ToolProperty(
                name: 'question',
                type: PropertyType::STRING,
                description: 'The full question to search for',
                required: true
            )
        ];
    }

    public function __invoke(string $question): string
    {
        $initialState = new WorkflowState();
        $initialState->set('question', $question);

        $workflow = new SearchWorkflow();
        $resultState = $workflow->run($initialState);

        return $resultState->get('answer');
    }
}
