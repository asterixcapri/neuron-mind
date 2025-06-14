<?php

namespace NeuronMind\Agent;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\Tools\Toolkits\Jina\JinaWebSearch;

class ResearchAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return new OpenAI(
            key: $_ENV['OPENAI_API_KEY'],
            model: 'gpt-4o-mini'
        );
    }

    protected function tools(): array
    {
        return [
            JinaWebSearch::make(
                key: $_ENV['JINA_API_KEY']
            )
        ];
    }
}
