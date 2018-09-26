<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Tests\Messenger;

use Pccomponentes\Amqp\Messenger\MessageSerializer;
use Pccomponentes\Amqp\Messenger\PublisherMiddleware;
use Pccomponentes\Amqp\Publisher\Publisher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PublisherMiddlewareTest extends TestCase
{
    /** @var MockObject */
    private $publisher;
    private $serializer;

    public function setUp()
    {
        $this->publisher = $this->getMockBuilder(Publisher::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
        $this->serializer = new class implements MessageSerializer
        {

            public function serialize($message): string
            {
                return $message->body;
            }

            public function routingKey($message): string
            {
                return $message->key;
            }
        };
    }

    /**
     * @test
     */
    public function happyPath()
    {
        $message = \json_decode(\json_encode(['body' => 'message_example', 'key' => 'key_example']));
        $middleware = new PublisherMiddleware($this->publisher, $this->serializer);
        $this->publisher
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo('message_example'), 'key_example');

        $middleware->handle($message, function ($message) {
            return $message;
        });
    }
}
