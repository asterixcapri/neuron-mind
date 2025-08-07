<?php

namespace NeuronMind\Node\Search;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\Search\SearcherAgent;
use NeuronMind\Logger\SimpleLogger;
use RuntimeException;

class SearcherNode extends Node
{
    public function run(WorkflowState $state): WorkflowState
    {
        SimpleLogger::info('SearcherNode - Starting...');

        $agent = SearcherAgent::make();

        $queries = $state->get('queries');

        if (!is_array($queries)) {
            throw new RuntimeException('Expected queries to be an array');
        }

        if (!$state->has('searchResults')) {
            $state->set('searchResults', []);
        }

        foreach ($queries as $query) {
            SimpleLogger::info('SearcherNode - Query: '.$query);

            $userMessage = new UserMessage("Query: {$query}");
            $response = $agent->chat($userMessage);
            $content = $response->getContent();

            SimpleLogger::info('SearcherNode - Result: ', $content);

            $state->set('searchResults', array_merge($state->get('searchResults'), [$content]));
        }

        return $state;
    }
}
