<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\QueryWriterAgent;
use NeuronMind\Event\SearchEvent;
use NeuronMind\Logger\SimpleLogger;

class QueryWriterNode extends Node
{
    public function __invoke(StartEvent $event, WorkflowState $state): SearchEvent
    {
        SimpleLogger::info('QueryWriterNode - Starting...');

        $question = $state->get('question');

        SimpleLogger::info('QueryWriterNode - Question: ', $question, truncate: false);

        $data = QueryWriterAgent::make()->structured(new UserMessage("Question: {$question}"));

        SimpleLogger::info('QueryWriterNode - Response: ', $data, truncate: false);

        return new SearchEvent($data->queries);
    }
}
