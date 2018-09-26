<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Subscriber;

use Pccomponentes\Amqp\Builder\BasicConsumeBuilder;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class Subscriber
{
    private $connection;
    private $channel;
    private $basicConsumeBuilder;
    private $callback;
    private $secondsToWait;
    private $quantity;
    private $processed;
    private $stop;

    public function __construct(
        AbstractConnection $connection,
        BasicConsumeBuilder $basicConsumeBuilder,
        SubscriberCallback $callback
    ) {
        $this->connection = $connection;
        $this->basicConsumeBuilder = $basicConsumeBuilder;
        $this->callback = $callback;
    }

    public function listen(int $quantity, int $secondsToWait): void
    {
        $this->stop = false;
        $this->quantity = $quantity;
        $this->secondsToWait = $secondsToWait;
        $this->processed = 0;
        $this->prepareConsumer();
        $this->run();
        $this->close();
    }

    private function prepareConsumer(): void
    {
        $this->basicConsumeBuilder->build(
            $this->channel(),
            function (AMQPMessage $message) {
                $this->callback->execute(new SubscriberMessage($message));
                $this->processed++;
            }
        );
    }

    private function run(): void
    {
        while ($this->continueRunning()) {
            try {
                $this->channel()->wait(null, false, $this->secondsToWait);
            } catch (AMQPTimeoutException $ex) {
                $this->stop = true;
            }
        }
    }

    private function continueRunning(): bool
    {
        return $this->channelReady() && $this->jobsPending() && false === $this->stop;
    }

    private function channelReady(): bool
    {
        return \count($this->channel()->callbacks) > 0;
    }

    private function jobsPending(): bool
    {
        return $this->quantity > $this->processed;
    }

    private function close(): void
    {
        $this->channel()->close();
        $this->connection->close();
    }

    private function channel(): AMQPChannel
    {
        if (null === $this->channel) {
            $this->channel = $this->connection->channel();
        }

        return $this->channel;
    }
}
