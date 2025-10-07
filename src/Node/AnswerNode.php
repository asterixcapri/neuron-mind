<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StopEvent;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\AnswerAgent;
use NeuronMind\Event\AnswerEvent;
use NeuronMind\Logger\SimpleLogger;

class AnswerNode extends Node
{
    public function __invoke(AnswerEvent $event, WorkflowState $state): StopEvent
    {
        SimpleLogger::info('AnswerNode - Starting...');
        SimpleLogger::info('AnswerNode - Question: ', $state->get('question'), truncate: false);

        $userMessage = new UserMessage(sprintf(
            'User Question: %s\nSummaries: %s',
            $state->get('question'),
            implode("\n---\n", $event->searchResults)
        ));

        $content = AnswerAgent::make()
            ->chat($userMessage)
            ->getContent();

        SimpleLogger::info('AnswerNode - Response: ', $content);

        $state->set('answer', $content);

        return new StopEvent();
    }
}
