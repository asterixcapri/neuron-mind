<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\SearcherAgent;
use NeuronMind\Event\ReflectEvent;
use NeuronMind\Event\SearchEvent;
use NeuronMind\Logger\SimpleLogger;
use RuntimeException;

class SearcherNode extends Node
{
    public function __invoke(SearchEvent $event, WorkflowState $state): ReflectEvent
    {
        SimpleLogger::info('SearcherNode - Starting...');

        $queries = $state->get('queries');

        if (!is_array($queries)) {
            throw new RuntimeException('Expected queries to be an array');
        }

        if (!$state->has('searchResults')) {
            $state->set('searchResults', []);
        }

        foreach ($queries as $query) {
            SimpleLogger::info('SearcherNode - Query: '.$query);

            $content = SearcherAgent::make()
                ->chat(new UserMessage("Query: {$query}"))
                ->getContent();

            SimpleLogger::info('SearcherNode - Result: ', $content);

            $state->set('searchResults', array_merge($state->get('searchResults'), [$content]));
        }

        return new ReflectEvent();
    }
}
