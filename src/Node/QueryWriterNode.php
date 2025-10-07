<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\QueryWriterAgent;
use NeuronMind\Event\SearchEvent;
use NeuronMind\Logger\SimpleLogger;
use NeuronMind\Service\JsonExtractor;
use RuntimeException;

class QueryWriterNode extends Node
{
    public function __invoke(StartEvent $event, WorkflowState $state): SearchEvent
    {
        SimpleLogger::info('QueryWriterNode - Starting...');

        $question = $state->get('question');

        SimpleLogger::info('QueryWriterNode - Question: ', $question, truncate: false);

        $content = QueryWriterAgent::make()
            ->chat(new UserMessage("Question: {$question}"))
            ->getContent();

        $data = (new JsonExtractor())->getData($content);

        if (is_null($data) || !isset($data->queries)) {
            throw new RuntimeException('Failed to decode query generation output.');
        }

        SimpleLogger::info('QueryWriterNode - Response: ', $data, truncate: false);

        $state->set('rationale', $data->rationale ?? '');
        $state->set('queries', $data->queries ?? []);

        return new SearchEvent();
    }
}
