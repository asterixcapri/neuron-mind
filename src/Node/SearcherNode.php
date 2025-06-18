<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Tools\Toolkits\Jina\JinaWebSearch;
use NeuronMind\Agent\BaseAgent;
use NeuronMind\Logger\SimpleLogger;
use RuntimeException;
use Sixtynine\NeuronGraph\Graph\GraphState;
use Sixtynine\NeuronGraph\Nodes\Node;

class SearcherNode extends Node
{
    public function run(GraphState|null $state, mixed $input): ReflectionNode
    {
        SimpleLogger::info('SearcherNode - Starting...');

        $queries = $state->get('queries');

        if (!is_array($queries)) {
            throw new RuntimeException('Expected queries to be an array');
        }

        $currentDate = date('Y-m-d');

        $agent = BaseAgent::make()
            ->withInstructions(
                <<<INSTRUCTIONS
                    Conduct targeted searches to gather the most recent, credible information about the topic
                    and synthesize it into a verifiable text artifact.

                    Instructions:
                    - Query should ensure that the most current information is gathered. The current date is {$currentDate}.
                    - Conduct multiple, diverse searches to gather comprehensive information.
                    - Consolidate key findings while meticulously tracking the source(s) for each specific piece of information.
                    - The output should be a well-written summary or report based on your search findings. 
                    - Only include the information found in the search results, don't make up any information.
                INSTRUCTIONS
            )
            ->addTool(JinaWebSearch::make(key: $_ENV['JINA_API_KEY']));

        $searchResults = [];

        foreach ($queries as $query) {
            SimpleLogger::info('SearcherNode - Query: '.$query);

            $response = $agent->chat(new UserMessage("Topic: {$query}"));
            $content = $response->getContent();

            SimpleLogger::info('SearcherNode - Result: ', $content);

            $searchResults[] = $content;
        }

        if ($state->has('searchResults')) {
            $state->set('searchResults', array_merge($state->get('searchResults'), $searchResults));
        } else {
            $state->set('searchResults', $searchResults);
        }

        return new ReflectionNode();
    }
}
