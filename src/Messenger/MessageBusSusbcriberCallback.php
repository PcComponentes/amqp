<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Messenger;

use Pccomponentes\Amqp\Subscriber\SubscriberCallback;
use Pccomponentes\Amqp\Subscriber\SubscriberMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageBusSusbcriberCallback implements SubscriberCallback
{
    private $bus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->bus = $messageBus;
    }

    public function execute(SubscriberMessage $message): void
    {
        $this->bus->dispatch($message);
    }
}
