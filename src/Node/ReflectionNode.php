<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronMind\Agent\BaseAgent;
use NeuronMind\Logger\SimpleLogger;
use Sixtynine\NeuronGraph\Graph\GraphState;
use Sixtynine\NeuronGraph\Nodes\Node;

class ReflectionNode extends Node
{
    public function run(GraphState|null $state, mixed $input): AnswerNode|SearcherNode
    {
        $searchResults = $state->get('search_results');
        $userQuery = $state->get('user_query');

        if (!is_array($searchResults)) {
            throw new \RuntimeException('Expected search_results to be an array');
        }

        SimpleLogger::info('ReflectionNode - User query: '.$userQuery, truncate: false);
        SimpleLogger::info('ReflectionNode - Search results: ', $searchResults);

        $agent = BaseAgent::make();

        $resultsText = implode("\n---\n", $searchResults);

        $response = $agent->chat(new UserMessage(
            <<<PROMPT
                You are a critical research assistant.

                You will receive a user's original question and a list of search results retrieved from the web.

                Evaluate whether the search results are sufficient to answer the user's question.
                If yes, reply with this exact JSON: { "is_sufficient": true }

                If not, reply with this JSON: { "is_sufficient": false, "follow_up_queries": [ "query1", "query2", "query3" ] }

                User question:
                {$userQuery}

                Search results:
                ---
                {$resultsText}
            PROMPT
        ));

        $content = preg_replace('/```json\n|```/', '', $response->getContent());
        $json = json_decode($content, true);

        if (!is_array($json) || !isset($json['is_sufficient'])) {
            throw new \RuntimeException('Failed to parse reflection response.');
        }

        SimpleLogger::info('ReflectionNode - Reflection response: ', $content, truncate: false);

        $state->set('is_sufficient', $json['is_sufficient']);

        if ($state->has('loop_count')) {
            $state->set('loop_count', $state->get('loop_count') + 1);
        } else {
            $state->set('loop_count', 0);
        }

        if (!$json['is_sufficient']) {
            $state->set('queries', $json['follow_up_queries'] ?? []);
            return new SearcherNode();
        }

        return new AnswerNode();
    }
}
