<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Tests\Builder;

use Pccomponentes\Amqp\Builder\QueueBuilder;
use PhpAmqpLib\Channel\AMQPChannel;
use PHPUnit\Framework\TestCase;

class QueueBuilderTest extends TestCase
{
    private $channel;

    public function setUp()
    {
        $this->channel = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->channel
           ->method('queue_declare')
           ->will($this->returnCallback(function () {
            return \func_get_args();
           }));
    }

    /**
     * @test
     */
    public function happyPath()
    {
        $builder = new QueueBuilder('example');
        $builder
            ->durable()
            ->autoDelete()
            ->wait()
            ->passive()
            ->maxPriority(5)
            ->exclusive()
            ->deadLetterExchange('dead_example')
            ->deadLetterRoutingKey('dead_routing')
            ->expires(100)
            ->masterLocatorRandom()
            ->maxLength(60)
            ->maxLengthBytes(77)
            ->messageTtl(8)
            ->modeLazy()
            ->overflowDropHead();


        $returned = $builder->build($this->channel);
        $this->assertEquals(
            [
                'example',
                true,
                true,
                true,
                true,
                false,
                [
                    'x-max-priority' =>  5,
                    'x-dead-letter-exchange' => 'dead_example',
                    'x-dead-letter-routing-key' => 'dead_routing',
                    'x-expires' => 100,
                    'x-queue-master-locator' => 'random',
                    'x-max-length' => 60,
                    'x-max-length-bytes' => 77,
                    'x-message-ttl' => 8,
                    'x-queue-mode' => 'lazy',
                    'x-overflow' => 'drop-head'
                ],
                null
            ],
            $returned
        );
    }
}
