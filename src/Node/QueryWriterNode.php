<?php

namespace NeuronMind\Node;

use Generator;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\QueryWriterAgent;
use NeuronMind\Event\ProgressEvent;
use NeuronMind\Event\SearchEvent;

class QueryWriterNode extends Node
{
    public function __invoke(StartEvent $event, WorkflowState $state): SearchEvent|Generator
    {
        yield new ProgressEvent('QueryWriterNode - Starting...');
        yield new ProgressEvent('QueryWriterNode - Question: ', ['question' => $state->get('question')]);

        $data = QueryWriterAgent::make()->structured(new UserMessage('Question: '.$state->get('question')));

        yield new ProgressEvent('QueryWriterNode - Response: ', $data);

        return new SearchEvent($data->queries);
    }
}
