<?php

namespace NeuronMind\Tool;

use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\ToolProperty;
use NeuronAI\Tools\Tool;
use NeuronAI\Workflow\Edge;
use NeuronAI\Workflow\Workflow;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Node\AnswerNode;
use NeuronMind\Node\QueryWriterNode;
use NeuronMind\Node\ReflectionNode;
use NeuronMind\Node\SearcherNode;

class SearchTool extends Tool
{
    public function __construct()
    {
        parent::__construct(
            'search',
            'Search information for a given question'
        );
    }

    protected function properties(): array
    {
        return [
            new ToolProperty(
                name: 'question',
                type: PropertyType::STRING,
                description: 'The full question to search for',
                required: true
            )
        ];
    }

    public function __invoke(string $question): string
    {
        $workflow = new Workflow();

        $workflow->addNodes([
            new QueryWriterNode(),
            new SearcherNode(),
            new ReflectionNode(),
            new AnswerNode(),
        ]);

        $workflow->addEdges([
            new Edge(QueryWriterNode::class, SearcherNode::class),
            new Edge(SearcherNode::class, ReflectionNode::class),
            new Edge(ReflectionNode::class, SearcherNode::class, function (WorkflowState $state) {
                // Loop back to searcher if info not sufficient and loop count <=3
                return !$state->get('isSufficient') && $state->get('loopCount') <= 3;
            }),
            new Edge(ReflectionNode::class, AnswerNode::class, function (WorkflowState $state) {
                // Finish when sufficient or exceeded loop
                return $state->get('isSufficient') || $state->get('loopCount') > 3;
            })
        ]);

        $workflow->setStart(QueryWriterNode::class);
        $workflow->setEnd(AnswerNode::class);

        $initialState = new WorkflowState();
        $initialState->set('question', $question);

        $resultState = $workflow->run($initialState);

        return $resultState->get('answer');
    }
}
