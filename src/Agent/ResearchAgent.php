<?php

namespace NeuronMind\Agent;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;

class ResearchAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return new OpenAI(
            key: $_ENV['OPENAI_API_KEY'],
            model: 'gpt-4o-mini'
        );
    }
}
