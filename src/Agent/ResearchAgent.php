<?php

namespace NeuronMind\Agent;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\Tools\Toolkits\Tavily\TavilySearchTool;

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
            TavilySearchTool::make(
                key: $_ENV['TAVILY_API_KEY']
            ),
        ];
    }
}
