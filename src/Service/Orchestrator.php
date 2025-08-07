<?php

namespace NeuronMind\Service;

use Generator;
use NeuronAI\Chat\Enums\MessageRole;
use NeuronAI\Chat\History\InMemoryChatHistory;
use NeuronAI\Chat\Messages\AssistantMessage;
use NeuronAI\Chat\Messages\Message;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronMind\Agent\ChatAgent;
use NeuronMind\Agent\ContextSummarizerAgent;
use NeuronMind\Agent\IntentAgent;
use NeuronMind\Agent\SlotFillingAgent;
use NeuronMind\Workflow\SearchWorkflow;
use NeuronMind\Workflow\WeatherWorkflow;

class Orchestrator
{
    private WorkflowRegistry $workflowRegistry;
    private WorkflowRunner $workflowRunner;
    private ChatAgent $chatAgent;

    public function __construct()
    {
        $this->workflowRegistry = new WorkflowRegistry();

        $this->workflowRegistry->register(
            "weather",
            "Weather forecasts",
            new WeatherWorkflow(),
            [
                [
                    'slot_name' => 'city',
                    'extraction_prompt' => 'What is the city mentioned in the user\'s message? Respond with the city name only. If no city is mentioned, respond with "NULL".'
                ],
                [
                    'slot_name' => 'date',
                    'extraction_prompt' => 'What is the date or day mentioned in the user\'s message (e.g., "today", "tomorrow", "Friday")? Respond with the value only. If no date is mentioned, respond with "NULL".'
                ]
            ]
        );

        $this->workflowRegistry->register(
            "search",
            "Search for information on a given topic",
            new SearchWorkflow()
        );

        $this->workflowRunner = new WorkflowRunner($this->workflowRegistry);
        $this->chatAgent = new ChatAgent();
    }

    public function orchestrate(string $userInput): Generator
    {
        if ($this->workflowRunner->isInterrupted()) {
            $result = $this->handleInterruptedWorkflow($userInput);
        }
        else {
            $result = $this->handleNewInteraction($userInput);
        }

        if ($result !== null) {
            yield from $this->handleWorkflowResult($result);
        } else {
            // Fallback to chat agent if no workflow was run or resumed successfully.
            yield from $this->chatAgent->stream(new UserMessage($userInput));
        }
    }

    private function handleInterruptedWorkflow(string $userInput): ?array
    {
        $interruption = $this->workflowRunner->getInterruptionDetails();

        if ($interruption === null || $interruption['type'] !== 'fill_slot') {
            return null;
        }

        $question = $interruption['question'];
        $slotToFill = $interruption['slot_name'];
        
        echo "DEBUG question: {$question}\n";
        echo "DEBUG slot to fill: {$slotToFill}\n";

        $slotFillingAgent = new SlotFillingAgent($question);
        $slotFillingAgent->withChatHistory($this->chatAgent->getContextWindow(6));
        $extractedData = $slotFillingAgent->extract($userInput);
        
        echo "DEBUG extractedData: "; print_r($extractedData); echo "\n";

        if ($extractedData === null) {
            return null;
        }

        return $this->workflowRunner->resume([$slotToFill => $extractedData]);
    }

    private function handleNewInteraction(string $userInput): ?array
    {
        $intentAgent = new IntentAgent($this->workflowRegistry);
        $intentAgent->withChatHistory($this->chatAgent->getContextWindow(6));
        $intent = $intentAgent->determineIntent($userInput);

        echo "DEBUG intent: {$intent}\n";

        if ($intent === 'chat' || $this->workflowRegistry->getInstance($intent) === null) {
            return null;
        }

        // It's a workflow-triggering message. Add it to history.
        $this->chatAgent->fillChatHistory(new UserMessage($userInput));

        $summarizer = new ContextSummarizerAgent();
        $summarizer->withChatHistory($this->chatAgent->getContextWindow(6));
        $contextSummary = $summarizer->summarize();
        
        echo "DEBUG contextSummary: "; print_r($contextSummary); echo "\n";

        $initialData = ['question' => "Context: {$contextSummary}\n\nQuestion: {$userInput}"];
        
        $slots = $this->workflowRegistry->get($intent)->slots ?? [];

        foreach ($slots as $slot) {
            $extractor = new SlotFillingAgent($slot['extraction_prompt']);
            $extractor->withChatHistory($this->chatAgent->getContextWindow(2));
            $extractedValue = $extractor->extract($userInput);
            if ($extractedValue) {
                $initialData[$slot['slot_name']] = $extractedValue;
            }
        }
        
        return $this->workflowRunner->run($intent, $initialData);
    }

    private function handleWorkflowResult(array $result): Generator
    {
        if ($result['status'] === 'completed') {
            $answer = $result['data']['answer'] ?? null;
            if ($answer) {
                $this->chatAgent->fillChatHistory(new AssistantMessage($answer));
                $this->chatAgent->fillChatHistory(new Message(MessageRole::SYSTEM, "The previous task is complete. The user is now starting a new turn. Respond naturally."));
                yield $answer;
            } else {
                $errorMessage = "I'm sorry, an error occurred while processing the request. The final answer is missing.";
                $this->chatAgent->fillChatHistory(new AssistantMessage($errorMessage));
                yield $errorMessage;
            }
        } else { // Interrupted or error
            $question = $result['details']['question'] ?? "An error occurred.";
            $this->chatAgent->fillChatHistory(new AssistantMessage($question));
            yield $question;
        }
    }
}
