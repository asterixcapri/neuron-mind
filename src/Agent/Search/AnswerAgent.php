<?php

namespace NeuronMind\Agent\Search;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;

class AnswerAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return new OpenAI(
            key: $_ENV['OPENAI_API_KEY'],
            model: 'gpt-4.1-mini'
        );
    }

    public function instructions(): string
    {
        $currentDate = date('Y-m-d');

        return <<<INSTRUCTIONS
            The current date is {$currentDate}.
            Generate a clear, complete answer to the user's question, using only the information from the provided summaries.
            - Integrate all key points from the summaries, referencing the user's question.
            - You must include all the links from the summaries, directly in the answer, as inline references (e.g. [source1](url)).
            - If the answer is uncertain or evidence is conflicting, state so, and still include all relevant links.
            - Do not mention that you are part of a multi-step process.
        INSTRUCTIONS;
    }
}
