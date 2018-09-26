<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Tests\Builder;

use Pccomponentes\Amqp\Builder\BindBuilder;
use PhpAmqpLib\Channel\AMQPChannel;
use PHPUnit\Framework\TestCase;

class BindBuilderTest extends TestCase
{
    private $channel;

    public function setUp()
    {
        $this->channel = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->channel
           ->method('queue_bind')
           ->will(
               $this->returnCallback(function () {
                return \func_get_args();
               })
           );
    }

    /**
     * @test
     */
    public function happyPath()
    {
        $builder = new BindBuilder('queue_example', 'exchange_example', 'routing_key_example');
        $builder
            ->wait();

        $returned = $builder->build($this->channel);
        $this->assertEquals(
            [
                'queue_example',
                'exchange_example',
                'routing_key_example',
                false,
                [],
                null
            ],
            $returned
        );
    }
}
