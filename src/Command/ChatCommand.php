<?php

namespace NeuronMind\Command;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronMind\Agent\ResearchAgent;
use NeuronMind\Observability\LogObserver;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
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
        $logger = new ConsoleLogger($output);
        $observer = new LogObserver($logger, $output);

        $researchAgent = ResearchAgent::make()
            ->observe($observer);

        $message = $input->getOption('message');

        if ($message !== null) {
            $response = $researchAgent->chat(new UserMessage($message));
            $output->writeln($response->getContent());
        } else {
            $output->writeln("<info>NeuronMind CLI - type 'exit' to quit</info>");
            $helper = new QuestionHelper();

            while (true) {
                $question = new Question('You> ');
                $userInput = $helper->ask($input, $output, $question);

                if ($userInput === 'exit') {
                    break;
                }

                $response = $researchAgent->chat(new UserMessage($userInput));
                $output->writeln("NeuronMind> ".$response->getContent());
            }
        }

        return Command::SUCCESS;
    }
}
