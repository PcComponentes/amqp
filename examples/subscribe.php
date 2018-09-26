<?php
include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Pccomponentes\Amqp\Builder\BasicConsumeBuilder;
use Pccomponentes\Amqp\Subscriber\SubscriberMessage;
use Pccomponentes\Amqp\Subscriber\SubscriberCallback;
use Pccomponentes\Amqp\Subscriber\Subscriber;

$connection = new AMQPStreamConnection('ampq-rabbitmq', 5672, 'guest', 'guest', 'my_vhost');

$basicConsumeBuilder = new BasicConsumeBuilder('queue_example');
$basicConsumeBuilder
    ->wait()
    ->ack()
    ->local()
    ->prefetchSize(0)
    ->prefetchCount(1)
    ->noPrefetchGlobal();

$callback = new class implements SubscriberCallback
{
    public function execute(SubscriberMessage $message): void
    {
        \var_dump($message->message()->getBody());
        $message->ack();
    }
};
$subscriber = new Subscriber($connection, $basicConsumeBuilder, $callback);
$subscriber->listen(3, 10);