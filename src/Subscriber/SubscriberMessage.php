<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Subscriber;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class SubscriberMessage
{
    private $amqpMessage;

    public function __construct(AMQPMessage $amqpMessage)
    {
        $this->amqpMessage = $amqpMessage;
    }

    public function message(): AMQPMessage
    {
        return $this->amqpMessage;
    }

    public function ack(bool $multiple = false): void
    {
        $this->channel()->basic_ack($this->deliveryTag(), $multiple);
    }

    public function reject(bool $requeue = false): void
    {
        $this->channel()->basic_reject($this->deliveryTag(), $requeue);
    }

    public function nack(bool $multiple = false, bool $requeue = false): void
    {
        $this->channel()->basic_nack($this->deliveryTag(), $multiple, $requeue);
    }

    public function channel(): AMQPChannel
    {
        return $this->message()->delivery_info['channel'];
    }

    public function deliveryTag(): string
    {
        return (string) $this->message()->delivery_info['delivery_tag'];
    }
}
