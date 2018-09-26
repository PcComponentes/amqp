<?php
include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Pccomponentes\Amqp\Builder\BasicPublishBuilder;
use Pccomponentes\Amqp\Builder\MessageBuilder;
use Pccomponentes\Amqp\Publisher\Publisher;
use Pccomponentes\Amqp\Messenger\PublisherMiddleware;
use Pccomponentes\Amqp\Messenger\MessageSerializer;
use Symfony\Component\Messenger\MessageBus;

$connection = new AMQPStreamConnection('ampq-rabbitmq', 5672, 'guest', 'guest', 'my_vhost');

$basicPublishBuilder = (new BasicPublishBuilder('exchange_example'))
    ->noImmediate()
    ->noMandatory();

$messageBuilder = (new MessageBuilder())
    ->contentTypeJson()
    ->deliveryModePersistent();

$publisher = new Publisher($connection, $basicPublishBuilder, $messageBuilder);
$messageSerializer = new class implements MessageSerializer
{
    public function serialize($message): string
    {
        return \json_encode($message);
    }

    public function routingKey($message): string
    {
        return $message->topic;
    }
};
$publisherMiddleware = new PublisherMiddleware($publisher, $messageSerializer);

$messageBus = new MessageBus([$publisherMiddleware]);
$message = \json_decode(\json_encode(['body' => 'body example', 'topic' => 'topic_example']));
$messageBus->dispatch($message);