<?php

namespace NeuronMind\Tool;

use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\ToolProperty;
use NeuronAI\Tools\Tool;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Event\ProgressEvent;
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
        $workflow = SearchWorkflow::make(new WorkflowState(['question' => $question]));
        $handler = $workflow->start();

        foreach ($handler->streamEvents() as $event) {
            if ($event instanceof ProgressEvent) {
                echo $event->message;

                if ($event->data) {
                    echo json_encode($event->data, \JSON_PRETTY_PRINT);
                }

                echo "\n";
            }
        }

        $result = $handler->getResult();

        return $result->get('answer');
    }
}
