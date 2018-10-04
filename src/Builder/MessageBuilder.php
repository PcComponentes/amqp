<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Builder;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class MessageBuilder
{
    private $properties;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): self
    {
        return $this->clearProperties();
    }

    public function property(string $name, $value): self
    {
        return $this->setProperty($name, $value);
    }

    public function clearProperties(): self
    {
        $this->properties = [];
        return $this;
    }

    public function contentType(string $contentType): self
    {
        return $this->property('content_type', $contentType);
    }

    public function contentTypePlain(): self
    {
        return $this->contentType('text/plain');
    }

    public function contentTypeJson(): self
    {
        return $this->contentType('application/json');
    }

    public function contentTypeOctetStream(): self
    {
        return $this->contentType('application/octet-stream');
    }

    public function deliveryMode(int $deliveryMode): self
    {
        return $this->property('delivery_mode', $deliveryMode);
    }

    public function deliveryModePersistent(): self
    {
        return $this->deliveryMode(AMQPMessage::DELIVERY_MODE_PERSISTENT);
    }

    public function deliveryModeNonPersistent(): self
    {
        return $this->deliveryMode(AMQPMessage::DELIVERY_MODE_NON_PERSISTENT);
    }

    public function contentEncoding(string $contentEncoding): self
    {
        return $this->property('content_encoding', $contentEncoding);
    }

    public function applicationHeaders(array $applicationHeaders): self
    {
        return $this->property('application_headers', new AMQPTable($applicationHeaders));
    }

    public function priority(int $priority): self
    {
        return $this->property('priority', $priority);
    }

    public function correlationId(string $correlationId): self
    {
        return $this->property('correlation_id', $correlationId);
    }

    public function replyTo(string $replyTo): self
    {
        return $this->property('reply_to', $replyTo);
    }

    public function expiration(string $expiration): self
    {
        return $this->property('expiration', $expiration);
    }

    public function messageId(string $messageId): self
    {
        return $this->property('message_id', $messageId);
    }

    public function timestamp(int $timestamp): self
    {
        return $this->property('timestamp', $timestamp);
    }

    public function type(string $type): self
    {
        return $this->property('type', $type);
    }

    public function userId(string $userId): self
    {
        return $this->property('user_id', $userId);
    }

    public function appId(string $appId): self
    {
        return $this->property('app_id', $appId);
    }

    public function clusterId(string $clusterId): self
    {
        return $this->property('cluster_id', $clusterId);
    }

    public function build(string $message): AMQPMessage
    {
        return new AMQPMessage($message, $this->properties);
    }

    private function setProperty(string $name, $value): self
    {
        $this->properties[$name] = $value;
        return $this;
    }
}
