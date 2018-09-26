<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Publisher;

use Pccomponentes\Amqp\Builder\BasicPublishBuilder;
use Pccomponentes\Amqp\Builder\MessageBuilder;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;

class Publisher
{
    private $connection;
    private $channel;
    private $basicPublishBuilder;
    private $messageBuilder;

    public function __construct(
        AbstractConnection $connection,
        BasicPublishBuilder $basicPublishBuilder,
        MessageBuilder $messageBuilder
    ) {
        $this->connection = $connection;
        $this->basicPublishBuilder = $basicPublishBuilder;
        $this->messageBuilder = $messageBuilder;
    }

    public function send(string $message, string $routingKey): void
    {
        $this->basicPublishBuilder->build(
            $this->channel(),
            $this->messageBuilder->build($message),
            $routingKey
        );
    }

    public function close(): void
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
