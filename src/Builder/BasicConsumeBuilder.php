<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Builder;

use PhpAmqpLib\Channel\AMQPChannel;

class BasicConsumeBuilder
{
    private $queue;
    private $consumerTag;
    private $noLocal;
    private $noAck;
    private $exclusive;
    private $noWait;
    private $arguments;
    private $prefetchSize;
    private $prefetchCount;
    private $prefetchGlobal;

    public function __construct(string $queue)
    {
        $this
            ->queue($queue)
            ->reset();
    }

    public function reset(): self
    {
        return $this
            ->consumerTag('')
            ->local()
            ->ack()
            ->noExclusive()
            ->wait()
            ->clearArguments()
            ->prefetchSize(0)
            ->prefetchCount(0)
            ->noPrefetchGlobal();
    }

    public function argument(string $name, $value): self
    {
        $this->arguments[$name] = $value;
        return $this;
    }

    public function clearArguments(): self
    {
        $this->arguments = [];
        return $this;
    }

    public function queue(string $queue): self
    {
        return $this->setQueue($queue);
    }

    public function consumerTag(string $consumerTag): self
    {
        return $this->setConsumerTag($consumerTag);
    }

    public function local(): self
    {
        return $this->setNoLocal(false);
    }

    public function noLocal(): self
    {
        return $this->setNoLocal(true);
    }

    public function ack(): self
    {
        return $this->setNoAck(false);
    }

    public function noAck(): self
    {
        return $this->setNoAck(true);
    }

    public function exclusive(): self
    {
        return $this->setExclusive(true);
    }

    public function noExclusive(): self
    {
        return $this->setExclusive(false);
    }

    public function wait(): self
    {
        return $this->setNoWait(false);
    }

    public function noWait(): self
    {
        return $this->setNoWait(true);
    }

    public function prefetchSize(int $prefetchSize): self
    {
        return $this->setPrefetchSize($prefetchSize);
    }

    public function prefetchCount(int $prefetchCount): self
    {
        return $this->setPrefetchCount($prefetchCount);
    }

    public function prefetchGlobal(): self
    {
        return $this->setPrefetchGlobal(true);
    }

    public function noPrefetchGlobal(): self
    {
        return $this->setPrefetchGlobal(false);
    }

    public function build(AMQPChannel $channel, callable $callback)
    {
        $channel->basic_qos(
            $this->prefetchSize,
            $this->prefetchCount,
            $this->prefetchGlobal
        );

        return $channel->basic_consume(
            $this->queue,
            $this->consumerTag,
            $this->noLocal,
            $this->noAck,
            $this->exclusive,
            $this->noWait,
            $callback,
            null,
            $this->arguments
        );
    }

    private function setQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    private function setConsumerTag(string $consumerTag): self
    {
        $this->consumerTag = $consumerTag;
        return $this;
    }

    private function setNoLocal(bool $noLocal): self
    {
        $this->noLocal = $noLocal;
        return $this;
    }

    private function setNoAck(bool $noAck): self
    {
        $this->noAck = $noAck;
        return $this;
    }

    private function setExclusive(bool $exclusive): self
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    private function setNoWait(bool $noWait): self
    {
        $this->noWait = $noWait;
        return $this;
    }

    private function setPrefetchSize(int $prefetchSize): self
    {
        $this->prefetchSize = $prefetchSize;
        return $this;
    }

    private function setPrefetchCount(int $prefetchCount): self
    {
        $this->prefetchCount = $prefetchCount;
        return $this;
    }

    private function setPrefetchGlobal(bool $prefetchGlobal): self
    {
        $this->prefetchGlobal = $prefetchGlobal;
        return $this;
    }
}
