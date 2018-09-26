<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Tests\Builder;

use Pccomponentes\Amqp\Builder\ExchangeBuilder;
use PhpAmqpLib\Channel\AMQPChannel;
use PHPUnit\Framework\TestCase;

class ExchangeBuilderTest extends TestCase
{
    private $channel;

    public function setUp()
    {
        $this->channel = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->channel
           ->method('exchange_declare')
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
        $builder = new ExchangeBuilder('example', ExchangeBuilder::TYPE_FANOUT);
        $builder
            ->durable()
            ->autoDelete()
            ->wait()
            ->passive()
            ->alternateExchange('alternate_exchange_name')
            ->internal();

        $returned = $builder->build($this->channel);
        $this->assertEquals(
            [
                'example',
                'fanout',
                true,
                true,
                true,
                true,
                false,
                [
                    'alternate-exchange' => 'alternate_exchange_name'
                ],
                null
            ],
            $returned
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function unknowType()
    {
        $builder = new ExchangeBuilder('example', 'unknow');
        $builder->build($this->channel);
    }
}
