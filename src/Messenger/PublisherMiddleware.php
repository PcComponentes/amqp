<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Messenger;

use Pccomponentes\Amqp\Publisher\Publisher;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;

class PublisherMiddleware implements MiddlewareInterface
{
    private $publisher;
    private $serializer;

    public function __construct(
        Publisher $publisher,
        MessageSerializer $serializer
    ) {
        $this->publisher = $publisher;
        $this->serializer = $serializer;
    }

    public function handle($message, callable $next)
    {
        $this->publisher->send(
            $this->serializer->serialize($message),
            $this->serializer->routingKey($message)
        );
        return $next($message);
    }
}
