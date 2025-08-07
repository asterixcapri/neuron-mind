<?php

namespace NeuronMind\Agent;

use NeuronAI\Agent;
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Chat\History\InMemoryChatHistory;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;

class ChatAgent extends Agent
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
            You are a friendly and helpful AI assistant. Your primary goal is to engage in a natural
            conversation with the user.

            **Key Principles:**
            1.  **Natural Conversation:** Be empathetic, and provide clear and helpful information.
                Avoid technical jargon and maintain a casual tone.
            2.  **Pay Attention to System Messages:** System messages are instructions for you from the main system.
                They have the highest priority. For example, if a system message says a task is "complete", do
                not ask for more information about that task. Assume the user is starting a new, unrelated
                conversation and respond accordingly (e.g., with "You're welcome!", "Is there anything else I
                can help with?", etc.).
        INSTRUCTIONS;
    }

    public function getContextWindow(int $numberOfMessages): ChatHistoryInterface
    {
        $messages = $this->resolveChatHistory()->getMessages();
        $contextWindow = array_slice($messages, -$numberOfMessages);

        $chatHistory = new InMemoryChatHistory();

        foreach ($contextWindow as $message) {
            $chatHistory->addMessage($message);
        }

        return $chatHistory;
    }
}
