<?php
include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\MessageBus;
use Pccomponentes\Amqp\Builder\BasicConsumeBuilder;
use Pccomponentes\Amqp\Messenger\MessageBusSusbcriberCallback;
use Pccomponentes\Amqp\Subscriber\Subscriber;

$connection = new AMQPStreamConnection('ampq-rabbitmq', 5672, 'guest', 'guest', 'my_vhost');

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