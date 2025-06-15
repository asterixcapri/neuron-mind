<?php

namespace NeuronMind\Node;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Tools\Toolkits\Jina\JinaWebSearch;
use NeuronMind\Agent\BaseAgent;
use NeuronMind\Logger\SimpleLogger;
use Sixtynine\NeuronGraph\Graph\GraphState;
use Sixtynine\NeuronGraph\Nodes\Node;

class SearcherNode extends Node
{
    public function run(GraphState|null $state, mixed $input): ReflectionNode
    {
        SimpleLogger::info('SearcherNode - Starting search');

        $queries = $state->get('queries');

        if (!is_array($queries)) {
            throw new \RuntimeException('Expected queries to be an array');
        }

        $agent = BaseAgent::make()
            ->addTool(JinaWebSearch::make(key: $_ENV['JINA_API_KEY']));

        $searchResults = [];

        foreach ($queries as $query) {
            SimpleLogger::info('SearcherNode - Query: '.$query);

            $response = $agent->chat(new UserMessage(
                <<<PROMPT
                    Search the web for the following query and summarize the key results:
                    {$query}
                PROMPT
            ));

            $content = $response->getContent();

            SimpleLogger::info('SearcherNode - Search result: ', $content);

            $searchResults[] = $content;
        }

        if ($state->has('search_results')) {
            $state->set('search_results', array_merge($state->get('search_results'), $searchResults));
        } else {
            $state->set('search_results', $searchResults);
        }

        return new ReflectionNode();
    }
}
