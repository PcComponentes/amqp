<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Builder;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Wire\AMQPTable;

class QueueBuilder
{
    private $name;
    private $passive;
    private $durable;
    private $exclusive;
    private $autoDelete;
    private $noWait;
    private $arguments;

    public function __construct(string $name)
    {
        $this
            ->name($name)
            ->reset();
    }

    public function reset(): self
    {
        return $this
            ->noPassive()
            ->noDurable()
            ->noExclusive()
            ->autoDelete()
            ->wait()
            ->clearArguments();
    }

    public function name(string $name): self
    {
        return $this->setName($name);
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

    public function exclusive(): self
    {
        return $this->setExclusive(true);
    }

    public function noExclusive(): self
    {
        return $this->setExclusive(false);
    }

    public function autoDelete(): self
    {
        return $this->setAutoDelete(true);
    }

    public function noAutoDelete(): self
    {
        return $this->setAutoDelete(false);
    }

    public function wait(): self
    {
        return $this->setNoWait(false);
    }

    public function noWait(): self
    {
        return $this->setNoWait(true);
    }

    public function messageTtl(int $ttl): self
    {
        if (1 > $ttl) {
            throw new \InvalidArgumentException('The queue message ttl should be positive');
        }
        return $this->argument('x-message-ttl', $ttl);
    }

    public function expires(int $expiration): self
    {
        if (1 > $expiration) {
            throw new \InvalidArgumentException('The queue expiration should be positive');
        }
        return $this->argument('x-expires', $expiration);
    }

    public function maxLength(int $maxLength): self
    {
        if (1 > $maxLength) {
            throw new \InvalidArgumentException('The queue maximun length should be positive');
        }
        return $this->argument('x-max-length', $maxLength);
    }

    public function maxLengthBytes(int $maxLengthBytes): self
    {
        if (1 > $maxLengthBytes) {
            throw new \InvalidArgumentException('The queue maximun length bytes should be positive');
        }
        return $this->argument('x-max-length-bytes', $maxLengthBytes);
    }

    public function overflowDropHead(): self
    {
        return $this->argument('x-overflow', 'drop-head');
    }

    public function overflowRejectPublish(): self
    {
        return $this->argument('x-overflow', 'reject-publish');
    }

    public function deadLetterExchange(string $exchange): self
    {
        return $this->argument('x-dead-letter-exchange', $exchange);
    }

    public function deadLetterRoutingKey(string $routingKey): self
    {
        return $this->argument('x-dead-letter-routing-key', $routingKey);
    }

    public function maxPriority(int $maxPriority): self
    {
        if (1 > $maxPriority || 255 < $maxPriority) {
            throw new \InvalidArgumentException('Queue max priority only accepts values between 1 and 255');
        }
        return $this->argument('x-max-priority', $maxPriority);
    }

    public function modeDefault(): self
    {
        return $this->argument('x-queue-mode', 'default');
    }

    public function modeLazy(): self
    {
        return $this->argument('x-queue-mode', 'lazy');
    }

    public function masterLocatorMinMasters(): self
    {
        return $this->argument('x-queue-master-locator', 'min-masters');
    }

    public function masterLocatorClientLocal(): self
    {
        return $this->argument('x-queue-master-locator', 'client-local');
    }

    public function masterLocatorRandom(): self
    {
        return $this->argument('x-queue-master-locator', 'random');
    }

    public function build(AMQPChannel $channel)
    {
        return $channel->queue_declare(
            $this->name,
            $this->passive,
            $this->durable,
            $this->exclusive,
            $this->autoDelete,
            $this->noWait,
            new AMQPTable($this->arguments)
        );
    }

    private function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    private function setArgument(string $name, $value): self
    {
        $this->arguments[$name] = $value;
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

    private function setExclusive(bool $exclusive): self
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    private function setAutoDelete(bool $autoDelete): self
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    private function setNoWait(bool $noWait): self
    {
        $this->noWait = $noWait;
        return $this;
    }
}
