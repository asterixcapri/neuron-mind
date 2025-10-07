<?php

namespace NeuronMind\Workflow;

use NeuronAI\Workflow\Workflow;
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
}
