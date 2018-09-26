<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Builder;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class BasicPublishBuilder
{
    private $exchange;
    private $mandatory;
    private $immediate;

    public function __construct(string $exchange)
    {
        $this
            ->exchange($exchange)
            ->reset();
    }

    public function reset(): self
    {
        return $this
            ->noMandatory()
            ->noImmediate();
    }

    public function exchange(string $exchange): self
    {
        return $this->setExchange($exchange);
    }

    public function mandatory(): self
    {
        return $this->setMandatory(true);
    }

    public function noMandatory(): self
    {
        return $this->setMandatory(false);
    }

    public function immediate(): self
    {
        return $this->setImmediate(true);
    }

    public function noImmediate(): self
    {
        return $this->setImmediate(false);
    }

    public function build(AMQPChannel $channel, AMQPMessage $message, string $routingKey)
    {
        return $channel->basic_publish(
            $message,
            $this->exchange,
            $routingKey,
            $this->mandatory,
            $this->immediate
        );
    }

    private function setExchange(string $exchange): self
    {
        $this->exchange = $exchange;
        return $this;
    }

    private function setMandatory(bool $mandatory): self
    {
        $this->mandatory = $mandatory;
        return $this;
    }

    private function setImmediate(bool $immediate): self
    {
        $this->immediate = $immediate;
        return $this;
    }
}
