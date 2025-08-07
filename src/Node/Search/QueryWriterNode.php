<?php

namespace NeuronMind\Node\Search;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\Search\QueryWriterAgent;
use NeuronMind\Logger\SimpleLogger;
use RuntimeException;

class QueryWriterNode extends Node
{
    public function run(WorkflowState $state): WorkflowState
    {
        SimpleLogger::info('QueryWriterNode - Starting...');

        $agent = QueryWriterAgent::make();

        $question = $state->get('question');

        SimpleLogger::info('QueryWriterNode - Question: ', $question, truncate: false);

        $userMessage = new UserMessage("Question: {$question}");
        $data = $agent->structured($userMessage);

        if (is_null($data) || !isset($data->queries)) {
            throw new RuntimeException('Failed to decode query generation output.');
        }

        SimpleLogger::info('QueryWriterNode - Response: ', $data, truncate: false);

        $state->set('rationale', $data->rationale ?? '');
        $state->set('queries', $data->queries ?? []);

        return $state;
    }
}
