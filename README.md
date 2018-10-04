# Utilidades para mensajería AMQP
Esta biblioteca proporciona una serie de utilidades para lidiar con sistemas de mensajería basados en el protocolo AMQP 0.9.1, y en especial con [Rabbit MQ](https://www.rabbitmq.com/). Entre las utilidades también se incluyen adaptadores a middlewares del bus de symfony para publicar, y un comando de consola para consumir.

Las herramientas disponibles en este repositorio son en su mayoría clases de alto nivel que usan intensivamente clases de bajo nivel que trae la implementación en PHP del cliente Rabbit, que puedes encontrar en [https://github.com/php-amqplib/php-amqplib]().

## Creación de exchanges, colas y binds.

Para la declaración de exchanges, colas y binds, disponemos de tres builders, que son "atajos" para las funciones ```****_declare``` de la librería ampqlib original, a excepción de ```BindBuilder```, que usa ```queue_bind```.
- ```namespace Pccomponentes\Amqp\Builder\ExchangeBuilder```
- ```namespace Pccomponentes\Amqp\Builder\QueueBuilder```
- ```namespace Pccomponentes\Amqp\Builder\BindBuilder```

Todos ellos disponen de métodos para ir seteando sus distintas opciones.

Para conocer qué opciones acepta cada builder, consulte:
- [exchange_declare](http://www.rabbitmq.com/amqp-0-9-1-reference.html#exchange.declare)
- [queue_declare](http://www.rabbitmq.com/amqp-0-9-1-reference.html#queue.declare)
- [queue_bind](http://www.rabbitmq.com/amqp-0-9-1-reference.html#queue.bind)

### Ejemplo de uso
```php
<?php
include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Pccomponentes\Amqp\Builder\ExchangeBuilder;
use Pccomponentes\Amqp\Builder\QueueBuilder;
use Pccomponentes\Amqp\Builder\BindBuilder;

$connection = new AMQPStreamConnection('ampq-rabbitmq', 5672, 'guest', 'guest', 'my_vhost');

$queueBuilder = (new QueueBuilder('queue_example'))
    ->durable()
    ->noAutoDelete();

$exchangeBuilder = (new ExchangeBuilder('exchange_example', ExchangeBuilder::TYPE_FANOUT))
    ->durable()
    ->noAutoDelete();

$bindBuilder = new BindBuilder('queue_example', 'exchange_example', '');

$channel = $connection->channel();
$queueBuilder->build($channel);
$exchangeBuilder->build($channel);
$bindBuilder->build($channel);
```

## Publicar mensajes en una cola
Para publicar un mensaje en una cola, se proporcionan las siguientes clases:
- ```namespace Pccomponentes\Amqp\Publisher\Publisher```
- ```namespace Pccomponentes\Amqp\Builder\BasicPublishBuilder```
- ```namespace Pccomponentes\Amqp\Builder\MessageBuilder```

La clase principal ```Publisher``` requiere de una instancia de ```PhpAmqpLib\Connection\AbstractConnection```, de ```BasicPublishBuilder```, y de ```MessageBuilder```, y proporciona un método ```send``` que enviará el mensaje con el topic indicados al exchange correspondiente.

La elección de a qué exchange enviar el mensaje, y la configuración del envío, se declara con la clase ```BasicPublishBuilder```, que es un atajo a ```basic_publish``` de la librería original.

Por último, para facilitar la creación del mensaje ```PhpAmqpLib\Message\AMQPMessage```, se proporciona su correspondiente ```MessageBuilder```, donde podrá configurar el ```content_type```, el ```delivery_mode```, y otra multitud de parámetros, a todos los mensajes que construya y envíe la clase ```Publisher```.

Mas información:
- [basic_publish](http://www.rabbitmq.com/amqp-0-9-1-reference.html#basic.publish)
- [Message creation](https://www.rabbitmq.com/tutorials/amqp-concepts.html#messages)

### Ejemplo de uso:

```php
<?php
include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Pccomponentes\Amqp\Builder\BasicPublishBuilder;
use Pccomponentes\Amqp\Builder\MessageBuilder;
use Pccomponentes\Amqp\Publisher\Publisher;

$connection = new AMQPStreamConnection('ampq-rabbitmq', 5672, 'guest', 'guest', 'my_vhost');

$basicPublishBuilder = (new BasicPublishBuilder('exchange_example'))
    ->noImmediate()
    ->noMandatory();

$messageBuilder = (new MessageBuilder())
    ->contentTypeJson()
    ->deliveryModePersistent();

$publisher = new Publisher($connection, $basicPublishBuilder, $messageBuilder);
$publisher->send('{"message" : "example"}', 'example');
$publisher->close();
```

## Consumir una cola
Para consumir un mensaje en una cola, se proporcionan las siguientes clases:
- ```namespace Pccomponentes\Amqp\Subscriber\Subscriber```
- ```namespace Pccomponentes\Amqp\Builder\BasicConsumeBuilder```
- ```namespace Pccomponentes\Amqp\Subscriber\SubscriberCallback```
- ```namespace Pccomponentes\Amqp\Subscriber\SubscriberMessage```

La clase principal ```Subscriber``` requiere de una instancia de ```PhpAmqpLib\Connection\AbstractConnection```, de ```BasicConsumeBuilder``` y de ```SubscriberCallback```, al que le enviará un mensaje de tipo ```SubscriberMessage```.

La configuración de cómo consumir una cola, se delegará a ```BasicConsumerBuilder```, que son atajos a los métodos de la librería original ```basic_qos``` y ```basic_consume```.

La interfaz ```SubscriberCallback``` será la que tu proyecto tenga que implementar, y programar allí las tareas que quieras ejecutar cuando recuperes un mensaje. Este mensaje será de tipo ```SubscriberMessage```, que simplemente es un wrapper de ```PhpAmqpLib\Message\AMQPMessage``` que viene con métodos para hacer un __ACK__, __NACK__ y __REJECT__ sobre el mensaje de manera simple. Además proporciona un método ```message``` para acceder a la clase original.

Mas información:
- [basic_qos](http://www.rabbitmq.com/amqp-0-9-1-reference.html#basic.qos)
- [basic_consume](http://www.rabbitmq.com/amqp-0-9-1-reference.html#basic.consume)
- [message.ack](http://www.rabbitmq.com/amqp-0-9-1-reference.html#basic.ack)
- [message.nack](http://www.rabbitmq.com/amqp-0-9-1-reference.html#basic.nack)
- [message.reject](http://www.rabbitmq.com/amqp-0-9-1-reference.html#basic.reject)

### Ejemplo de uso
En el siguiente ejemplo, declararemos un ```Subscriber``` que hará un simple ```var_dump``` de cada mensaje que consuma.
```php
<?php
include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Pccomponentes\Amqp\Builder\BasicConsumeBuilder;
use Pccomponentes\Amqp\Subscriber\SubscriberMessage;
use Pccomponentes\Amqp\Subscriber\SubscriberCallback;
use Pccomponentes\Amqp\Subscriber\Subscriber;

$connection = new AMQPStreamConnection('ampq-rabbitmq', 5672, 'guest', 'guest', 'my_vhost');

$basicConsumeBuilder = new BasicConsumeBuilder('queue_example');
$basicConsumeBuilder
    ->wait()
    ->ack()
    ->local()
    ->prefetchSize(0)
    ->prefetchCount(1)
    ->noPrefetchGlobal();

$callback = new class implements SubscriberCallback
{
    public function execute(SubscriberMessage $message): void
    {
        \var_dump($message->message()->getBody());
        $message->ack();
    }
};
$subscriber = new Subscriber($connection, $basicConsumeBuilder, $callback);
$subscriber->listen(3, 10);
```

## Subscriber + Message Bus
Si se requiere enviar los mensajes consumidos de una cola de rabbit, a un bus, se proporciona la clase ```Pccomponentes\Amqp\Messenger\MessageBusSusbcriberCallback```, que es una implmenetación concreta del callback de ```Subscriber``` para este fin.
Mas información:
- [Symfony Messenger](https://symfony.com/doc/current/components/messenger.html) 
### Ejemplo de uso
```php
<?php
include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\MessageBus;
use Pccomponentes\Amqp\Builder\BasicConsumeBuilder;
use Pccomponentes\Amqp\Messenger\MessageBusSusbcriberCallback;
use Pccomponentes\Amqp\Subscriber\Subscriber;

$connection = new AMQPStreamConnection('ampq-rabbitmq', 5672, 'guest', 'guest', 'my_vhost');

$messageBusMiddleware = new class() implements MiddlewareInterface
{
    public function handle($message, callable $next)
    {
        \var_dump($message->message()->getBody());
        $message->ack();
        return $next($message);
    }
};

$basicConsumeBuilder = new BasicConsumeBuilder('queue_example');
$basicConsumeBuilder
    ->wait()
    ->ack()
    ->local()
    ->prefetchSize(0)
    ->prefetchCount(1)
    ->noPrefetchGlobal();

$messageBus = new MessageBus([$messageBusMiddleware]);
$toMessageBusCallback = new MessageBusSusbcriberCallback($messageBus);
$subscriber = new Subscriber($connection, $basicConsumeBuilder, $toMessageBusCallback);

$subscriber->listen(1, 10);
```

## Message Bus + Publisher
Para meter un middleware que publique mensajes en una cola, tenemos dos clases auxiliares: ```Pccomponentes\Amqp\Messenger\PublisherMiddleware```, que es el middleware del bus de symfony, y ```Pccomponentes\Amqp\Messenger\MessageSerializer```, que es una interfaz que implementará nuestro proyecto para indicar el cómo serializar los mensajes antes de enviarlos al sistema de mensajería, y a qué _topic_ o _routing key_ hacerlo.

Mas información:
- [Symfony Messenger](https://symfony.com/doc/current/components/messenger.html) 
### Ejemplo de uso
```php
<?php
include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Pccomponentes\Amqp\Builder\BasicPublishBuilder;
use Pccomponentes\Amqp\Builder\MessageBuilder;
use Pccomponentes\Amqp\Publisher\Publisher;
use Pccomponentes\Amqp\Messenger\PublisherMiddleware;
use Pccomponentes\Amqp\Messenger\MessageSerializer;
use Symfony\Component\Messenger\MessageBus;

$connection = new AMQPStreamConnection('ampq-rabbitmq', 5672, 'guest', 'guest', 'my_vhost');

$basicPublishBuilder = (new BasicPublishBuilder('exchange_example'))
    ->noImmediate()
    ->noMandatory();

$messageBuilder = (new MessageBuilder())
    ->contentTypeJson()
    ->deliveryModePersistent();

$publisher = new Publisher($connection, $basicPublishBuilder, $messageBuilder);
$messageSerializer = new class implements MessageSerializer
{
    public function serialize($message): string
    {
        return \json_encode($message);
    }

    public function routingKey($message): string
    {
        return $message->topic;
    }
};
$publisherMiddleware = new PublisherMiddleware($publisher, $messageSerializer);

$messageBus = new MessageBus([$publisherMiddleware]);
$message = \json_decode(\json_encode(['body' => 'body example', 'topic' => 'topic_example']));
$messageBus->dispatch($message);
```

## Comandos de consola
A continuación, se detallarán los comandos de consola que proporciona esta librería. Todos ellos dependen del componente __console__ de Symfony.
Mas información:
- [Componente console](https://symfony.com/doc/current/components/console.html)
### Consumir mensajes
#### Con el framework symfony
Si nuestro proyecto cuenta con el framework de symfony, podemos meter el comando directamente en el contenedor de dependencias, marcándolo con el tag correspondiente.

Por ejemplo:
```yaml
      
pdo_migration_command:
    class: Pccomponentes\Amqp\Command\SubscriberCommand
    arguments:
        - 'custom'                  # nombre del comando, que se concatenará a "subscriber:"
        - '@project.subscriber'     # Servicio subscriptor
    tags:
        - { name: console.command }
```
### Creando nuestra propia aplicación de consola
Para poder ejecutar el comando, previamente tenemos que generar una aplicación. Para ello, deberíamos crear un fichero PHP con el siguiente contenido, modificado lo necesario para adaptarlo a tu nuestro proyecto. Como será un ejecutable de consola, lo llamaremos console sin extensión, y lo pondremos en un directorio __bin__ en la raíz de tu proyecto.

```php
#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Pccomponentes\Amqp\Command\SubscriberCommand;
use Pccomponentes\Amqp\Subscriber\Subscriber;

$subscriber = new Subscriber(/* argumentos */);
$application = new Application();
$application->addCommands(
    [
        new SubscriberCommand('custom', $subscriber)
    ]
);

$application->run();
```

### Ejecutar el comando
Para ejecutar el comando, basta con escribir en la terminal:
```bash
bin/console subscriber:custom --timeout=10 20
```
En él le estamos indicando al sistema que consuma 20 mensajes, y cuando no haya mensajes en la cola, quedará esperando 10 segundos como máximo, o terminarña de ejecutar.