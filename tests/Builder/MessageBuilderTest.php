<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Tests\Builder;

use Pccomponentes\Amqp\Builder\MessageBuilder;
use PHPUnit\Framework\TestCase;

class MessageBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function happyPath()
    {
        $builder = new MessageBuilder();
        $builder
            ->contentTypeJson()
            ->deliveryModePersistent()
            ->expiration('expiration_example')
            ->type('type_example')
            ->messageId('message_id_example')
            ->userId('user_id_example')
            ->appId('app_id_example')
            ->applicationHeaders([])
            ->clusterId('cluster_id_example')
            ->correlationId('correlation_id_example')
            ->priority(100)
            ->replyTo('reply_to_example')
            ->timestamp(1537794399);

        $message = $builder->build('example');

        $this->assertEquals(
            [
                'delivery_mode' => 2,
                'content_type' => 'application/json',
                'expiration' => 'expiration_example',
                'type' => 'type_example',
                'message_id' => 'message_id_example',
                'user_id' => 'user_id_example',
                'app_id' => 'app_id_example',
                'application_headers' => [],
                'cluster_id' => 'cluster_id_example',
                'correlation_id' => 'correlation_id_example',
                'priority' => 100,
                'reply_to' => 'reply_to_example',
                'timestamp' => 1537794399
            ],
            $message->get_properties()
        );
    }
}
