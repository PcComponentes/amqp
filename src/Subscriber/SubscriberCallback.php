<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Subscriber;

interface SubscriberCallback
{
    public function execute(SubscriberMessage $message): void;
}
