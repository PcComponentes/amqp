<?php
include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Pccomponentes\Amqp\Builder\ExchangeBuilder;
use Pccomponentes\Amqp\Builder\QueueBuilder;
use \Pccomponentes\Amqp\Builder\BindBuilder;
use Pccomponentes\Amqp\Builder\BasicPublishBuilder;
use Pccomponentes\Amqp\Builder\MessageBuilder;
use Pccomponentes\Amqp\Publisher\Publisher;

$connection = new AMQPStreamConnection('ampq-rabbitmq', 5672, 'guest', 'guest', 'my_vhost');

/* Infrastructure */
$queueBuilder = (new QueueBuilder('queue_example'))
    ->durable()
    ->noAutoDelete();

$exchangeBuilder = (new ExchangeBuilder('exchange_example', ExchangeBuilder::TYPE_FANOUT))
    ->durable()
    ->noAutoDelete();

$bindBuilder = new BindBuilder('queue_example', 'exchange_example', '');

$channel = $connection->channel();
$queueBuilder->build($channel);
$exchangeBuilder->build($channel);
$bindBuilder->build($channel);

/* Publish */
$basicPublishBuilder = (new BasicPublishBuilder('exchange_example'))
    ->noImmediate()
    ->noMandatory();

$messageBuilder = (new MessageBuilder())
    ->contentTypeJson()
    ->deliveryModePersistent();

$publisher = new Publisher($connection, $basicPublishBuilder, $messageBuilder);
$publisher->send('{"message" : "example"}', 'example');
$publisher->close();
