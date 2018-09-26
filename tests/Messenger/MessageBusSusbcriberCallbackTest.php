<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Tests\Messenger;

use Pccomponentes\Amqp\Messenger\MessageBusSusbcriberCallback;
use Pccomponentes\Amqp\Subscriber\SubscriberMessage;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageBusSusbcriberCallbackTest extends TestCase
{
    /** @var MockObject */
    private $bus;

    public function setUp()
    {
        $this->bus = $this->getMockBuilder(MessageBusInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['dispatch'])
            ->getMock();
    }

    /**
     * @test
     */
    public function happyPath()
    {
        $message = new SubscriberMessage(new AMQPMessage('example'));
        $callback = new MessageBusSusbcriberCallback($this->bus);
        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($message));

        $callback->execute($message);
    }
}
