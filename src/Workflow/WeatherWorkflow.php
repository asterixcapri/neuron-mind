<?php

namespace NeuronMind\Workflow;

use NeuronAI\Workflow\Edge;
use NeuronAI\Workflow\Workflow;
use NeuronMind\Node\Weather\CheckCityNode;
use NeuronMind\Node\Weather\CheckDateNode;
use NeuronMind\Node\Weather\FetchWeatherNode;

class WeatherWorkflow extends Workflow
{
    public function nodes(): array
    {
        return [
            new CheckCityNode(),
            new CheckDateNode(),
            new FetchWeatherNode(),
        ];
    }

    public function edges(): array
    {
        return [
            new Edge(CheckCityNode::class, CheckDateNode::class),
            new Edge(CheckDateNode::class, FetchWeatherNode::class),
        ];
    }

    protected function start(): string
    {
        return CheckCityNode::class;
    }

    protected function end(): array
    {
        return [
            FetchWeatherNode::class,
        ];
    }
}
