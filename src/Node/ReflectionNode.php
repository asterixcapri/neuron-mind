<?php

namespace NeuronMind\Node;

use Generator;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\ReflectionAgent;
use NeuronMind\Event\AnswerEvent;
use NeuronMind\Event\ProgressEvent;
use NeuronMind\Event\ReflectEvent;
use NeuronMind\Event\SearchEvent;

class ReflectionNode extends Node
{
    public function __invoke(ReflectEvent $event, WorkflowState $state): SearchEvent|AnswerEvent|Generator
    {
        yield new ProgressEvent('ReflectionNode - Starting...');
        yield new ProgressEvent('ReflectionNode - Question: ', ['question' => $state->get('question')]);
        yield new ProgressEvent('ReflectionNode - Results: ', ['results' => count($event->searchResults)]);

        $userMessage = new UserMessage(sprintf(
            "Question: %s\n\nSummaries: %s",
            $state->get('question'),
            implode("\n---\n", $event->searchResults)
        ));

        $data = ReflectionAgent::make()->structured($userMessage);

        yield new ProgressEvent('ReflectionNode - Response: ', $data);

        $loopCount = $state->has('loopCount')
            ? $state->get('loopCount') + 1
            : 1;

        $state->set('loopCount', $loopCount);

        if ($state->get('loopCount') > 3) {
            return new AnswerEvent($event->searchResults);
        }

        return $data->isSufficient
            ? new AnswerEvent($event->searchResults)
            : new SearchEvent($data->followUpQueries);
    }
}
