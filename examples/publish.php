<?php
include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Pccomponentes\Amqp\Builder\BasicPublishBuilder;
use Pccomponentes\Amqp\Builder\MessageBuilder;
use Pccomponentes\Amqp\Publisher\Publisher;

$connection = new AMQPStreamConnection('ampq-rabbitmq', 5672, 'guest', 'guest', 'my_vhost');

$basicPublishBuilder = (new BasicPublishBuilder('exchange_example'))
    ->noImmediate()
    ->noMandatory();

$messageBuilder = (new MessageBuilder())
    ->contentTypeJson()
    ->deliveryModePersistent();

$publisher = new Publisher($connection, $basicPublishBuilder, $messageBuilder);
$publisher->send('{"message" : "example"}', 'example');
$publisher->close();
