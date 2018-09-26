<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Builder;

use PhpAmqpLib\Channel\AMQPChannel;

class ExchangeBuilder
{
    public const TYPE_HEADERS = 'headers';
    public const TYPE_FANOUT = 'fanout';
    public const TYPE_TOPIC = 'topic';
    public const TYPE_DIRECT = 'direct';

    private $name;
    private $type;
    private $passive;
    private $durable;
    private $autoDelete;
    private $internal;
    private $noWait;
    private $arguments;

    public function __construct(string $name, string $type)
    {
        $this
            ->name($name)
            ->type($type)
            ->reset();
    }

    public function reset(): self
    {
        return $this
            ->noPassive()
            ->noDurable()
            ->autoDelete()
            ->noInternal()
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

    public function name(string $name): self
    {
        return $this->setName($name);
    }

    public function type(string $type): self
    {
        return $this->setType($type);
    }

    public function passive(): self
    {
        return $this->setPassive(true);
    }

    public function noPassive(): self
    {
        return $this->setPassive(false);
    }

    public function durable(): self
    {
        return $this->setDurable(true);
    }

    public function noDurable(): self
    {
        return $this->setDurable(false);
    }

    public function autoDelete(): self
    {
        return $this->setAutoDelete(true);
    }

    public function noAutoDelete(): self
    {
        return $this->setAutoDelete(false);
    }

    public function internal(): self
    {
        return $this->setInternal(true);
    }

    public function noInternal(): self
    {
        return $this->setInternal(false);
    }

    public function wait(): self
    {
        return $this->setNoWait(false);
    }

    public function noWait(): self
    {
        return $this->setNoWait(true);
    }

    public function alternateExchange(string $exchangeName): self
    {
        return $this->argument('alternate-exchange', $exchangeName);
    }

    public function build(AMQPChannel $channel)
    {
        return $channel->exchange_declare(
            $this->name,
            $this->type,
            $this->passive,
            $this->durable,
            $this->autoDelete,
            $this->internal,
            $this->noWait,
            $this->arguments
        );
    }

    private function setType(string $type): self
    {
        $this->assertType($type);
        $this->type = $type;
        return $this;
    }

    private function assertType(string $type): void
    {
        $isValid = \in_array($type, [self::TYPE_HEADERS, self::TYPE_FANOUT, self::TYPE_TOPIC, self::TYPE_DIRECT], true);
        if (false === $isValid) {
            throw new \InvalidArgumentException('Invalid exchange type');
        }
    }

    private function setArgument(string $name, $value): self
    {
        $this->arguments[$name] = $value;
        return $this;
    }

    private function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    private function setPassive(bool $passive): self
    {
        $this->passive = $passive;
        return $this;
    }

    private function setDurable(bool $durable): self
    {
        $this->durable = $durable;
        return $this;
    }

    private function setAutoDelete(bool $autoDelete): self
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    private function setInternal(bool $internal): self
    {
        $this->internal = $internal;
        return $this;
    }

    private function setNoWait(bool $noWait): self
    {
        $this->noWait = $noWait;
        return $this;
    }
}
