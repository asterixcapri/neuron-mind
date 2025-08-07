<?php

namespace NeuronMind\Agent;

use NeuronAI\Agent;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;

class SlotFillingAgent extends Agent
{
    private string $question;

    public function __construct(string $question)
    {
        $this->question = $question;
    }

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
            You are a hyper-focused, non-conversational data extraction bot.
            Your SOLE TASK is to extract a specific piece of information from the user's message.

            The information to extract is the answer to this question: "{$this->question}"

            **RULES:**
            1.  Read the user's message.
            2.  If the message contains a clear and direct answer to the question, respond with ONLY that answer.
            3.  If the message does NOT contain a clear answer (it's a question, a statement, a refusal, etc.), you MUST respond with the exact string "NULL".

            **DO NOT BE FRIENDLY. DO NOT EXPLAIN. DO NOT ADD EXTRA WORDS.**
            Your entire response must be either the extracted data or the word "NULL".
        INSTRUCTIONS;
    }

    public function extract(string $userInput): ?string
    {
        $userMessage = new UserMessage($userInput);
        $response = trim($this->chat($userMessage)->getContent());

        if ($response === 'NULL') {
            return null;
        }

        return $response;
    }
}
