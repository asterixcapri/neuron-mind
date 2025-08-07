<?php

namespace NeuronMind\Agent;

use NeuronAI\Agent;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronMind\Service\WorkflowRegistry;

class IntentAgent extends Agent
{
    public function __construct(
        private readonly WorkflowRegistry $workflowRegistry
    ) {}

    protected function provider(): AIProviderInterface
    {
        return new OpenAI(
            key: $_ENV['OPENAI_API_KEY'],
            model: 'gpt-4.1-mini'
        );
    }

    public function determineIntent(string $userInput): string
    {
        $userMessage = new UserMessage($userInput);
        return trim($this->chat($userMessage)->getContent());
    }

    public function instructions(): string
    {
        $workflows = array_map(function ($workflow) {
            return "- {$workflow->name}: {$workflow->description}";
        }, $this->workflowRegistry->all());

        $workflowList = implode("\n", $workflows);

        return <<<PROMPT
            You are a super-precise, non-conversational intent classifier.
            Your SOLE TASK is to classify the user's last message into one of the following categories and respond with a SINGLE WORD.

            **DO NOT ANSWER THE USER. DO NOT BE FRIENDLY. DO NOT EXPLAIN YOURSELF.
            YOUR ONLY OUTPUT MUST BE ONE OF THE WORDS FROM THE LIST BELOW.**

            Analyze the user's last message in the context of the conversation history.

            **Classification Rules:**
            1.  If the user's message is a clear, new request that matches one of the workflows (e.g., "what's the weather?", "search for..."), respond with the workflow name.
            2.  If the user's message is a simple conversational reply (e.g., "thank you", "okay", "how are you?", "I don't understand"), respond with 'chat'.
            3.  If the user is answering a question the assistant just asked, the intent is 'chat'.
            4.  If in doubt, the default is always 'chat'.

            **Available Categories:**
            $workflowList
            - chat

            Respond with a single word.
        PROMPT;
    }
}
