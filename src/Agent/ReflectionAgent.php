<?php

namespace NeuronMind\Agent;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;

class ReflectionAgent extends Agent
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
        return <<<INSTRUCTIONS
            You are an expert research assistant analyzing summaries about a question.

            Instructions:
            - Carefully analyze the user's original question and **all explicit requests/aspects**.
            - For each distinct aspect or explicit subquestion (definition, example, application, comparison, etc.), check if the answer provides specific, complete, and relevant information.
            - If **any aspect explicitly requested** by the user is missing or only answered in a generic/vague way, the answer is NOT sufficient. Clearly describe the gap.
            - Only mark as sufficient if *every* requested aspect is addressed specifically and concretely.

            Output Format:
            - Format your response as a JSON object with these exact keys:
            - "isSufficient": true or false
            - "knowledgeGap": (If true, leave blank. If false, explain precisely what is missing)
            - "followUpQueries": (If true, empty array. If false, add a concrete follow-up question for each missing aspect)

            Example:

            Question: "Explain the difference between X and Y and give a real-world example."
            Summary: "X and Y differ in speed and size. (no example given)"
            Response:
            ```json
            {
            "isSufficient": false,
            "knowledgeGap": "The summary explains the difference between X and Y but does not provide a real-world example as requested.",
            "followUpQueries": ["What is a real-world example illustrating the difference between X and Y?"]
            }
        INSTRUCTIONS;
    }
}
