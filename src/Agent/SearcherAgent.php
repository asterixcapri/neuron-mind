<?php

namespace NeuronMind\Agent;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\Tools\Toolkits\Jina\JinaToolkit;

class SearcherAgent extends Agent
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
            You are a web research agent.

            Your task is to search for the most recent, credible information on the user's query, using authoritative online sources.

            Instructions:
            - The current date is {$currentDate}. Always prioritize up-to-date sources.
            - Use the provided web search tool to gather and verify information. NEVER invent information that is not in the sources.
            - For each key point, explicitly cite the source and always include the direct URL.
            - If the answer requires covering multiple aspects, organize the summary with clear section headings (e.g., Definition, History, Implications).
            - Make your summary detailed and well-structured, but concise and focused on what the user actually asked.
            - If you cannot find sufficient information, state what is missing.
            - Your output should be a self-contained, readable summary, suitable to be read as-is by a user.
            - Always write in the same language as the user's question (unless instructed otherwise).
        INSTRUCTIONS;
    }

    protected function tools(): array
    {
        return [
            JinaToolkit::make(key: $_ENV['JINA_API_KEY'])
        ];
    }
}
