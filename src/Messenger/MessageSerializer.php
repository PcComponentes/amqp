<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Messenger;

interface MessageSerializer
{
    public function serialize($message): string;
    public function routingKey($message): string;
}
