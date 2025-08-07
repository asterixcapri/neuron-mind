<?php

namespace NeuronMind\Agent;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;

class ContextSummarizerAgent extends Agent
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
            You are a system agent. Your task is to analyze a conversation and extract key information.
            DO NOT act as a chatbot. DO NOT answer the user.
            Your SOLE purpose is to read the conversation history and distill it into a concise, one-sentence summary.

            This summary must capture the key topics, entities, and user preferences mentioned
            that are relevant to understanding the user's most recent message.

            - Read the history.
            - Identify the core subject.
            - Extract key entities (names, places, topics).
            - Form a single, dense sentence.
            - Respond ONLY with that sentence. No preamble. No explanation.

            Example:
            History: [User: "I love sci-fi movies.", Assistant: "Me too! Seen any good ones?", User: "Yes, Arrival was amazing. Any other recommendations?"]
            Your response: The user likes sci-fi movies, specifically mentioning "Arrival", and is asking for a similar recommendation.
        INSTRUCTIONS;
    }

    public function summarize(): string
    {
        $lastMessage = $this->resolveChatHistory()->getLastMessage();

        return trim($this->chat($lastMessage)->getContent());
    }
}
