<?php

namespace NeuronMind\Observability;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogObserver implements \SplObserver
{
    public function __construct(
        private LoggerInterface $logger,
        private OutputInterface $output
    ) {}

    public function update(\SplSubject $subject, ?string $event = null, mixed $data = null): void
    {
        if ($event !== null) {
            if ($this->output->isDebug()) {
                $this->logger->debug(sprintf('[%s] %s', $event, print_r($data, true)));
            } elseif ($this->output->isVeryVerbose()) {
                $this->logger->info($event);
            }
        }
    }
}
