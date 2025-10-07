<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StopEvent;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\AnswerAgent;
use NeuronMind\Event\AnswerEvent;
use NeuronMind\Logger\SimpleLogger;
use RuntimeException;

class AnswerNode extends Node
{
    public function __invoke(AnswerEvent $event, WorkflowState $state): StopEvent
    {
        SimpleLogger::info('AnswerNode - Starting...');

        $question = $state->get('question');
        $searchResults = $state->get('searchResults');

        if (!is_array($searchResults)) {
            throw new RuntimeException('Expected search_results to be an array');
        }

        SimpleLogger::info('AnswerNode - Question: ', $question, truncate: false);

        $userMessage = new UserMessage(sprintf(
            'User Question: %s\nSummaries: %s',
            $question,
            implode("\n---\n", $searchResults)
        ));

        $content = AnswerAgent::make()
            ->chat($userMessage)
            ->getContent();

        SimpleLogger::info('AnswerNode - Response: ', $content);

        $state->set('answer', $content);

        return new StopEvent();
    }
}
