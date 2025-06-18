<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronMind\Agent\BaseAgent;
use NeuronMind\Logger\SimpleLogger;
use NeuronMind\Service\JsonOutputParser;
use RuntimeException;
use Sixtynine\NeuronGraph\Graph\GraphState;
use Sixtynine\NeuronGraph\Nodes\Node;

class ReflectionNode extends Node
{
    public function run(GraphState|null $state, mixed $input): AnswerNode|SearcherNode
    {
        SimpleLogger::info('ReflectionNode - Starting...');

        $topic = $state->get('topic');
        $searchResults = $state->get('searchResults');

        if (!is_array($searchResults)) {
            throw new RuntimeException('Expected searchResults to be an array');
        }

        SimpleLogger::info('ReflectionNode - Topic: ', $topic, truncate: false);
        SimpleLogger::info('ReflectionNode - Results: ', count($searchResults));

        $agent = BaseAgent::make()
            ->withInstructions(
                <<<INSTRUCTIONS
                    You are an expert research assistant analyzing summaries about "{$topic}".

                    Instructions:
                    - Carefully review the summaries to determine if they provide a clear and complete answer to the user's question.
                    - If the information is sufficient, you should confidently answer "isSufficient": true and avoid suggesting unnecessary follow-up.
                    - If there are meaningful knowledge gaps — such as missing explanations, examples, or key technical details — describe them and suggest one or more useful follow-up queries.

                    Requirements:
                    - Ensure the follow-up query is self-contained and includes necessary context for search.

                    Output Format:
                    - Format your response as a JSON object with these exact keys:
                    - "isSufficient": true or false
                    - "knowledgeGap": Describe what information is missing or needs clarification
                    - "followUpQueries": Write a specific question to address this gap

                    Example:
                    ```json
                    {
                        "isSufficient": true, // or false
                        "knowledgeGap": "The summary lacks information about performance metrics and benchmarks", // "" if isSufficient is true
                        "followUpQueries": ["What are typical performance benchmarks and metrics used to evaluate [specific technology]?"] // [] if isSufficient is true
                    }

                    Reflect carefully on the Summaries to identify knowledge gaps and produce a follow-up query.
                    Then, produce your output following this JSON format.
                INSTRUCTIONS
            );

        $summaries = implode("\n---\n", $searchResults);
        $response = $agent->chat(new UserMessage("Summaries: {$summaries}"));

        $data = (new JsonOutputParser())->parse($response->getContent());

        if (is_null($data) || !isset($data->isSufficient)) {
            throw new RuntimeException('Failed to parse reflection response.');
        }

        SimpleLogger::info('ReflectionNode - Response: ', $data, truncate: false);

        $loopCount = $state->has('loopCount') ? $state->get('loopCount') + 1 : 1;
        $state->set('loopCount', $loopCount);

        if ($loopCount > 3) {
            SimpleLogger::info('ReflectionNode - Loop count exceeded 3, returning to AnswerNode anyway');
            return new AnswerNode();
        }

        if (!$data->isSufficient) {
            $state->set('queries', $data->followUpQueries ?? []);
            return new SearcherNode();
        }
        else {
            return new AnswerNode();
        }
    }
}
