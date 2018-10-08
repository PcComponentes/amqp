<?php
include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Pccomponentes\Amqp\Builder\ExchangeBuilder;
use Pccomponentes\Amqp\Builder\QueueBuilder;
use \Pccomponentes\Amqp\Builder\BindBuilder;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\MessageBus;
use Pccomponentes\Amqp\Builder\BasicConsumeBuilder;
use Pccomponentes\Amqp\Messenger\MessageBusSusbcriberCallback;
use Pccomponentes\Amqp\Subscriber\Subscriber;

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

/* Subscribe */
$messageBusMiddleware = new class() implements MiddlewareInterface
{
    public function handle($message, callable $next)
    {
        \var_dump($message->message()->getBody());
        $message->ack();
        return $next($message);
    }
};

$basicConsumeBuilder = new BasicConsumeBuilder('queue_example');
$basicConsumeBuilder
    ->wait()
    ->ack()
    ->local()
    ->prefetchSize(0)
    ->prefetchCount(1)
    ->noPrefetchGlobal();

$messageBus = new MessageBus([$messageBusMiddleware]);
$callback = new MessageBusSusbcriberCallback($messageBus);
$subscriber = new Subscriber($connection, $basicConsumeBuilder, $callback);

$subscriber->listen(1, 10);