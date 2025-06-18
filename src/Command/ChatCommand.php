<?php

namespace NeuronMind\Command;

use Generator;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronMind\Agent\ChatAgent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'chat')]
class ChatCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('message', 'm', InputOption::VALUE_REQUIRED, 'Message to send to the AI (switch to non-interactive mode)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $agent = ChatAgent::make();

        $message = $input->getOption('message');

        if ($message !== null) {
            $response = $agent->stream(new UserMessage($message));
            $this->handleStreamResponse($output, $response);
        } else {
            $output->writeln("<info>NeuronMind CLI - type 'exit' to quit</info>");
            $helper = new QuestionHelper();

            while (true) {
                $question = new Question('You> ');
                $userInput = $helper->ask($input, $output, $question);

                if ($userInput === null) {
                    continue;
                }
                elseif ($userInput === 'exit') {
                    break;
                }

                $response = $agent->stream(new UserMessage($userInput));
                $this->handleStreamResponse($output, $response);
            }
        }

        return Command::SUCCESS;
    }

    private function handleStreamResponse(OutputInterface $output, Generator $response): void
    {
        foreach ($response as $i => $chunk) {
            if ($i === 0) {
                $output->write("NeuronMind> ");
            }

            $output->write($chunk);
        }

        $output->writeln("");
    }
}
