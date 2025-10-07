<?php

namespace NeuronMind\Node;

use Generator;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StopEvent;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\AnswerAgent;
use NeuronMind\Event\AnswerEvent;
use NeuronMind\Event\ProgressEvent;

class AnswerNode extends Node
{
    public function __invoke(AnswerEvent $event, WorkflowState $state): StopEvent|Generator
    {
        yield new ProgressEvent('AnswerNode - Starting...');
        yield new ProgressEvent('AnswerNode - Question: ', ['question' => $state->get('question')]);

        $userMessage = new UserMessage(sprintf(
            'User Question: %s\nSummaries: %s',
            $state->get('question'),
            implode("\n---\n", $event->searchResults)
        ));

        $content = AnswerAgent::make()
            ->chat($userMessage)
            ->getContent();

        yield new ProgressEvent('AnswerNode - Response: ', ['response' => $content]);

        $state->set('answer', $content);

        return new StopEvent();
    }
}
