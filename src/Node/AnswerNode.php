<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronMind\Agent\BaseAgent;
use NeuronMind\Logger\SimpleLogger;
use Sixtynine\NeuronGraph\Graph\GraphState;
use Sixtynine\NeuronGraph\Nodes\EndNode;
use Sixtynine\NeuronGraph\Nodes\Node;

class AnswerNode extends Node
{
    public function run(GraphState|null $state, mixed $input): EndNode
    {
        $originalQuestion = $state->get('user_query');
        $searchResults = $state->get('search_results');

        if (!is_array($searchResults)) {
            throw new \RuntimeException('Expected search_results to be an array');
        }

        SimpleLogger::info('AnswerNode - Original question: '.$originalQuestion, truncate: false);
        SimpleLogger::info('AnswerNode - Search results: ', $searchResults);

        $resultsText = implode("\n---\n", $searchResults);

        $agent = BaseAgent::make();

        $response = $agent->chat(new UserMessage(
            <<<PROMPT
                You are a helpful assistant.

                Based on the following search results, answer the user's original question.

                Original question: "{$originalQuestion}"

                Search results:
                ---
                {$resultsText}
                ---

                Answer with as much detail as needed.
            PROMPT
        ));

        $content = $response->getContent();

        SimpleLogger::info('AnswerNode - Answer: ', $content);

        return new EndNode($content);
    }
}
