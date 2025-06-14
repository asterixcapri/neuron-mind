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
                $this->logger->debug(sprintf('[%s] %s', $event, $this->getJsonData($data)));
            } elseif ($this->output->isVeryVerbose()) {
                $this->logger->info($event);
            }
        }
    }

    private function getJsonData(mixed $data, int $depth = 0): string
    {
        try {
            if ($depth > 10) {
                return '...';
            }

            if ($data === null) {
                return 'null';
            }

            if (is_scalar($data)) {
                return json_encode($data, JSON_UNESCAPED_UNICODE);
            }

            if (is_object($data)) {
                if ($data instanceof \Traversable) {
                    $data = iterator_to_array($data);
                } elseif ($data instanceof \JsonSerializable) {
                    return json_encode($data->jsonSerialize(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                } elseif (method_exists($data, 'toArray')) {
                    return json_encode($data->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                } elseif (method_exists($data, 'jsonSerialize')) {
                    return json_encode($data->jsonSerialize(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                } elseif (method_exists($data, '__toString')) {
                    return json_encode($data->__toString(), JSON_UNESCAPED_UNICODE);
                } else {
                    $data = (array) $data;
                }
            }

            if (is_array($data)) {
                $formattedData = [];
                foreach ($data as $key => $value) {
                    if (is_object($value) || is_array($value)) {
                        $formattedData[$key] = json_decode($this->getJsonData($value, $depth + 1), true);
                    } else {
                        $formattedData[$key] = $value;
                    }
                }
                return json_encode($formattedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            return sprintf('Error serializing data: %s (%s)', $e->getMessage(), get_class($e));
        }
    }
}
