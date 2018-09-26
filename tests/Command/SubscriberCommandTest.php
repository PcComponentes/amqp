<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Tests\Command;

use Pccomponentes\Amqp\Subscriber\Subscriber;
use Pccomponentes\Amqp\Command\SubscriberCommand;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Tester\CommandTester;

class SubscriberCommandTest extends \PHPUnit\Framework\TestCase
{
    /** @var MockObject */
    private $subscriber;
    private $command;
    private $tester;

    public function setUp()
    {
        $this->subscriber = $this->getMockBuilder(Subscriber::class)
            ->disableOriginalConstructor()
            ->setMethods(['listen'])
            ->getMock();

        $this->command = new SubscriberCommand('test', $this->subscriber);
        $this->tester = new CommandTester($this->command);
    }

    /**
     * @test
     */
    public function happyPath()
    {
        $this->subscriber
            ->expects($this->once())
            ->method('listen')
            ->with($this->equalTo(10), $this->equalTo(15));

        $this->tester->execute(
            [
                'quantity' => 10,
                '--timeout' => 15
            ]
        );

        $this->assertEquals(0, $this->tester->getStatusCode());
    }
}
