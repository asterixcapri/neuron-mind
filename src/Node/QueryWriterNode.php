<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronMind\Agent\BaseAgent;
use NeuronMind\Logger\SimpleLogger;
use Sixtynine\NeuronGraph\Graph\GraphState;
use Sixtynine\NeuronGraph\Nodes\Node;
use NeuronMind\Node\SearcherNode;
use NeuronMind\Service\JsonOutputParser;
use RuntimeException;

class QueryWriterNode extends Node
{
    public function run(GraphState|null $state, mixed $topic): SearcherNode
    {
        SimpleLogger::info('QueryWriterNode - Starting...');
        SimpleLogger::info('QueryWriterNode - Topic: ', $topic);

        $numberQueries = 3;
        $currentDate = date('Y-m-d');

        $agent = BaseAgent::make()
            ->withInstructions(
                <<<INSTRUCTIONS
                    Your goal is to generate sophisticated and diverse web search queries. These queries are intended
                    for an advanced automated web research tool capable of analyzing complex results, following links,
                    and synthesizing information.

                    Instructions:
                    - Always prefer a single search query, only add another query if the original question requests
                    multiple aspects or elements and one query is not enough.
                    - Each query should focus on one specific aspect of the original question.
                    - Don't produce more than {$numberQueries} queries.
                    - Queries should be diverse, if the topic is broad, generate more than 1 query.
                    - Don't generate multiple similar queries, 1 is enough.
                    - Query should ensure that the most current information is gathered. The current date is {$currentDate}.

                    Format: 
                    - Format your response as a JSON object with ALL three of these exact keys:
                    - "rationale": Brief explanation of why these queries are relevant
                    - "queries": A list of search queries

                    Example:

                    Topic: What revenue grew more last year apple stock or the number of people buying an iphone
                    ```json
                    {
                        "rationale": "To answer this comparative growth question accurately, we need specific data points on Apple's stock performance and iPhone sales metrics. These queries target the precise financial information needed: company revenue trends, product-specific unit sales figures, and stock price movement over the same fiscal period for direct comparison.",
                        "queries": [
                            "Apple total revenue growth fiscal year 2024",
                            "iPhone unit sales growth fiscal year 2024",
                            "Apple stock price growth fiscal year 2024"
                        ]
                    }
                    ```
                INSTRUCTIONS
            );

        $response = $agent->chat(new UserMessage("Topic: {$topic}"));
        $data = (new JsonOutputParser())->parse($response->getContent());

        if (is_null($data) || !isset($data->queries)) {
            throw new RuntimeException('Failed to decode query generation output.');
        }

        SimpleLogger::info('QueryWriterNode - Response: ', $data, truncate: false);

        $state->set('topic', $topic);
        $state->set('rationale', $data->rationale ?? '');
        $state->set('queries', $data->queries ?? []);

        return new SearcherNode();
    }
}
