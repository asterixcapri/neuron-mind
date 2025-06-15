<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronMind\Agent\BaseAgent;
use NeuronMind\Logger\SimpleLogger;
use Sixtynine\NeuronGraph\Graph\GraphState;
use Sixtynine\NeuronGraph\Nodes\Node;
use NeuronMind\Node\SearcherNode;

class QueryWriterNode extends Node
{
    public function run(GraphState|null $state, mixed $userQuery): SearcherNode
    {
        SimpleLogger::info('QueryWriterNode - Starting query writer');

        $agent = BaseAgent::make();

        $response = $agent->chat(new UserMessage(
            <<<PROMPT
                You are an expert research assistant. You will be given a user question.
                Generate a list of 3 search queries that would help answer the question, and a short rationale for why you chose them.
                Return them as a JSON object with keys "rationale" and "queries".

                User question:
                {$userQuery}

                Only return the JSON. Do not include any explanations.
            PROMPT
        ));

        $content = preg_replace('/```json\n|```/', '', $response->getContent());

        SimpleLogger::info('QueryWriterNode - Query writer response: ', $content, truncate: false);

        $json = json_decode($content, true);

        if (!is_array($json) || !isset($json['queries'])) {
            throw new \RuntimeException('Failed to decode query generation output.');
        }

        $state ??= new GraphState();
        $state->set('user_query', $userQuery);
        $state->set('queries', $json['queries']);
        $state->set('rationale', $json['rationale'] ?? null);

        return new SearcherNode();
    }
}
