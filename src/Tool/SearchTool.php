<?php

namespace NeuronMind\Tool;

use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\ToolProperty;
use NeuronAI\Tools\Tool;
use NeuronMind\Node\AnswerNode;
use NeuronMind\Node\QueryWriterNode;
use NeuronMind\Node\ReflectionNode;
use NeuronMind\Node\SearcherNode;
use Sixtynine\NeuronGraph\Graph\Graph;
use Sixtynine\NeuronGraph\Graph\GraphState;

class SearchTool extends Tool
{
    public function __construct()
    {
        parent::__construct(
            'search',
            'Search information for a given topic'
        );

        $this->addProperty(
            new ToolProperty(
                name: 'topic',
                type: PropertyType::STRING,
                description: 'A topic or a question',
                required: true
            )
        )->setCallable($this);
    }

    public function __invoke(string $topic): string
    {
        $graph = new Graph([
            QueryWriterNode::class,
            SearcherNode::class,
            ReflectionNode::class,
            AnswerNode::class,
        ]);

        $result = $graph->run(new QueryWriterNode(), new GraphState(), $topic);
        return $result->data;
    }
}
