<?php

namespace NeuronMind\Agent;

use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronMind\Tool\SearchTool;

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
            You are a helpful AI assistant.

            For any question that asks for information, facts, explanations, or definitions, you MUST call the `search` tool with the full user question.

            When you receive the research result from the `search` tool, you MUST:
            - Use the exact answer text provided by the tool as the main part of your response.
            - Ensure that ALL links included in the tool's result are **preserved exactly as they appear** in your final answer.
            - DO NOT remove, modify, or reformat any of the links.
            - You may add a brief introductory or closing sentence (optional), but do NOT change or omit the answer text or its links.
            - If the answer does not contain links, respond normally.

            Only reply directly to the user if the message is clearly small talk, a greeting, or a conversational remark that is not a request for information.
        INSTRUCTIONS;
    }

    protected function tools(): array
    {
        return [
            SearchTool::make()
        ];
    }
}
