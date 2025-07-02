<?php

namespace NeuronMind\Workflow;

use NeuronAI\Workflow\Edge;
use NeuronAI\Workflow\Workflow;
use NeuronAI\Workflow\WorkflowState;
use NeuronMind\Node\AnswerNode;
use NeuronMind\Node\QueryWriterNode;
use NeuronMind\Node\ReflectionNode;
use NeuronMind\Node\SearcherNode;

class SearchWorkflow extends Workflow
{
    public function nodes(): array
    {
        return [
            new QueryWriterNode(),
            new SearcherNode(),
            new ReflectionNode(),
            new AnswerNode()
        ];
    }

    public function edges(): array
    {
        return [
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
        ];
    }

    protected function start(): string
    {
        return QueryWriterNode::class;
    }

    protected function end(): array
    {
        return [
            AnswerNode::class
        ];
    }
}
