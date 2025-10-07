<?php

namespace NeuronMind\Agent;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronMind\Dto\QueryWriterDto;

class QueryWriterAgent extends Agent
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
        $numberQueries = 3;
        $currentDate = date('Y-m-d');

        return <<<INSTRUCTIONS
            Your goal is to generate the most effective web search queries for an advanced automated research tool.

            Instructions:
            - Analyze the user's question carefully. Identify if it involves **multiple clearly distinct aspects** (e.g., definition, history, implications) or just a single, focused topic.
            - If the question asks about only one aspect, generate **one precise and well-focused query**.
            - If the question explicitly or clearly requires information on multiple aspects, generate **one query for each aspect** (never more than {$numberQueries} queries total).
            - Never generate multiple queries for the same aspect or rephrase the same search in different ways.
            - For vague, very general, or unclear questions, generate only a single, best-guess query that is as targeted as possible.
            - Each query must be self-contained, focused, and directly usable in a search engine. Avoid vague, broad, or redundant queries.
            - Include the current date "{$currentDate}" in queries where it helps ensure recent information.
            - Prefer English queries unless the user question is clearly in another language.

            Format:
            - Output a JSON object with **exactly** these keys:
                - "rationale": Briefly explain your reasoning for the queries.
                - "queries": Array of your queries (one per aspect).

            Example:

            Question: What revenue grew more last year, Apple stock or the number of people buying an iPhone?
            ```json
            {
                "rationale": "To answer this comparative growth question, we need specific data points on Apple's stock performance and iPhone sales. These queries target the necessary revenue, sales, and stock information for the same fiscal period.",
                "queries": [
                    "Apple total revenue growth fiscal year 2024",
                    "iPhone unit sales growth fiscal year 2024",
                    "Apple stock price growth fiscal year 2024"
                ]
            }
            ```
        INSTRUCTIONS;
    }

    protected function getOutputClass(): string
    {
        return QueryWriterDto::class;
    }
}
