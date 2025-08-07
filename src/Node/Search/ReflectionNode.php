<?php

namespace NeuronMind\Node\Search;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Agent\Search\ReflectionAgent;
use NeuronMind\Logger\SimpleLogger;
use RuntimeException;

class ReflectionNode extends Node
{
    public function run(WorkflowState $state): WorkflowState
    {
        SimpleLogger::info('ReflectionNode - Starting...');

        $agent = ReflectionAgent::make();

        $question = $state->get('question');
        $searchResults = $state->get('searchResults');

        if (!is_array($searchResults)) {
            throw new RuntimeException('Expected searchResults to be an array');
        }

        SimpleLogger::info('ReflectionNode - Question: ', $question, truncate: false);
        SimpleLogger::info('ReflectionNode - Results: ', count($searchResults));

        $userMessage = new UserMessage(sprintf(
            "Question: %s\n\nSummaries: %s",
            $question,
            implode("\n---\n", $searchResults)
        ));

        $data = $agent->structured($userMessage);

        if (is_null($data) || !isset($data->isSufficient)) {
            throw new RuntimeException('Failed to parse reflection response.');
        }

        SimpleLogger::info('ReflectionNode - Response: ', $data);

        $state->set('isSufficient', (bool) $data->isSufficient);
        $state->set('knowledgeGap', $data->knowledgeGap ?? '');
        $state->set('queries', $data->followUpQueries ?? []);

        $loopCount = $state->has('loopCount') ? $state->get('loopCount') + 1 : 1;
        $state->set('loopCount', $loopCount);

        return $state;
    }
}
