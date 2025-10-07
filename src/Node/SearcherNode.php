<?php

namespace NeuronMind\Node;

use Generator;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\SearcherAgent;
use NeuronMind\Event\ProgressEvent;
use NeuronMind\Event\ReflectEvent;
use NeuronMind\Event\SearchEvent;

class SearcherNode extends Node
{
    public function __invoke(SearchEvent $event, WorkflowState $state): ReflectEvent|Generator
    {
        yield new ProgressEvent('SearcherNode - Starting...');

        if (!$state->has('searchResults')) {
            $state->set('searchResults', []);
        }

        foreach ($event->queries as $query) {
            yield new ProgressEvent('SearcherNode - Query: ', ['query' => $query]);

            $content = SearcherAgent::make()
                ->chat(new UserMessage("Query: {$query}"))
                ->getContent();

            yield new ProgressEvent('SearcherNode - Result: ', ['result' => $content]);

            $state->set('searchResults', array_merge($state->get('searchResults'), [$content]));
        }

        return new ReflectEvent($state->get('searchResults'));
    }
}
