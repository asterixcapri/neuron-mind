<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronMind\Agent\BaseAgent;
use NeuronMind\Logger\SimpleLogger;
use RuntimeException;
use Sixtynine\NeuronGraph\Graph\GraphState;
use Sixtynine\NeuronGraph\Nodes\EndNode;
use Sixtynine\NeuronGraph\Nodes\Node;

class AnswerNode extends Node
{
    public function run(GraphState|null $state, mixed $input): EndNode
    {
        SimpleLogger::info('AnswerNode - Starting...');

        $topic = $state->get('topic');
        $searchResults = $state->get('searchResults');

        if (!is_array($searchResults)) {
            throw new RuntimeException('Expected search_results to be an array');
        }

        SimpleLogger::info('AnswerNode - Topic: ', $topic, truncate: false);

        $currentDate = date('Y-m-d');

        $agent = BaseAgent::make()
            ->withInstructions(
                <<<INSTRUCTIONS
                    Generate a high-quality answer to the user's question based on the provided summaries.

                    Instructions:
                    - The current date is {$currentDate}.
                    - You are the final step of a multi-step research process, don't mention that you are the final step. 
                    - You have access to all the information gathered from the previous steps.
                    - You have access to the user's question.
                    - Generate a high-quality answer to the user's question based on the provided summaries and the user's question.
                    - you MUST include all the links from the summaries in the answer correctly.
                INSTRUCTIONS
            );

        $summaries = implode("\n---\n", $searchResults);

        $response = $agent->chat(new UserMessage(
            <<<MESSAGE
                User Question: {$topic}
                Summaries: {$summaries}
            MESSAGE
        ));

        $content = $response->getContent();

        SimpleLogger::info('AnswerNode - Response: ', $content);

        return new EndNode($content);
    }
}
