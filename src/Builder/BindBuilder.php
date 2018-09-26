<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Builder;

use PhpAmqpLib\Channel\AMQPChannel;

class BindBuilder
{
    private $queue;
    private $exchange;
    private $routingKey;
    private $noWait;
    private $arguments;

    public function __construct(string $queue, string $exchange, string $routingKey)
    {
        $this
            ->queue($queue)
            ->exchange($exchange)
            ->routingKey($routingKey)
            ->reset();
    }

    public function reset(): self
    {
        return $this
            ->wait()
            ->clearArguments();
    }

    public function argument(string $name, $value): self
    {
        return $this->setArgument($name, $value);
    }

    public function clearArguments(): self
    {
        $this->arguments = [];
        return $this;
    }

    public function queue(string $name): self
    {
        return $this->setQueue($name);
    }

    public function exchange(string $name): self
    {
        return $this->setExchange($name);
    }

    public function routingKey(string $routinkKey): self
    {
        return $this->setRoutingKey($routinkKey);
    }

    public function wait(): self
    {
        return $this->setNoWait(false);
    }

    public function noWait(): self
    {
        return $this->setNoWait(true);
    }

    public function build(AMQPChannel $channel)
    {
        return $channel->queue_bind(
            $this->queue,
            $this->exchange,
            $this->routingKey,
            $this->noWait,
            $this->arguments
        );
    }

    private function setQueue(string $name): self
    {
        $this->queue = $name;
        return $this;
    }

    private function setExchange(string $name): self
    {
        $this->exchange = $name;
        return $this;
    }

    private function setRoutingKey(string $routinkKey): self
    {
        $this->routingKey = $routinkKey;
        return $this;
    }

    private function setArgument(string $name, $value): self
    {
        $this->arguments[$name] = $value;
        return $this;
    }

    private function setNoWait(bool $noWait): self
    {
        $this->noWait = $noWait;
        return $this;
    }
}
