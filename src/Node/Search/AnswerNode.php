<?php

namespace NeuronMind\Node\Search;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\Search\AnswerAgent;
use NeuronMind\Logger\SimpleLogger;
use RuntimeException;

class AnswerNode extends Node
{
    public function run(WorkflowState $state): WorkflowState
    {
        SimpleLogger::info('AnswerNode - Starting...');

        $agent = AnswerAgent::make();

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

        $response = $agent->chat($userMessage);
        $content = $response->getContent();

        SimpleLogger::info('AnswerNode - Response: ', $content);

        $state->set('answer', $content);

        return $state;
    }
}
