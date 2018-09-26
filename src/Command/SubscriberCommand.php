<?php
/**
 * This disaster was designed by
 * @author Juan G. Rodríguez Carrión <juan.rodriguez@pccomponentes.com>
 */
declare(strict_types=1);
namespace Pccomponentes\Amqp\Command;

use Pccomponentes\Amqp\Subscriber\Subscriber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class SubscriberCommand extends Command
{
    private const DEFAULT_TIMEOUT = 60;

    private $subscriber;

    public function __construct(
        string $name,
        Subscriber $subscriber
    ) {
        parent::__construct("subscriber:{$name}");
        $this->subscriber = $subscriber;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Execute process manager to convert events in commands.')
            ->addOption('timeout', 't', InputOption::VALUE_OPTIONAL, 'Timeout', self::DEFAULT_TIMEOUT)
            ->addArgument('quantity', InputArgument::REQUIRED, 'Message quantity to process');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->subscriber->listen(
            (int) $input->getArgument('quantity'),
            (int) $input->getOption('timeout')
        );
    }
}
