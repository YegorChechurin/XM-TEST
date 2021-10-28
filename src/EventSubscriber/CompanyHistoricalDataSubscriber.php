<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\CompanyHistoricalDataQuoted;
use App\Message\CompanyHistoricalDataQuoteEmailNotification;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final class CompanyHistoricalDataSubscriber implements EventSubscriberInterface
{
    private const DATE_FORMAT = 'Y-m-d';

    private MessageBusInterface $messageBus;
    private LoggerInterface $logger;

    public function __construct(MessageBusInterface $messageBus, LoggerInterface $logger)
    {
        $this->messageBus = $messageBus;
        $this->logger = $logger;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [CompanyHistoricalDataQuoted::class => 'onCompanyHistoricalDataQuote'];
    }

    public function onCompanyHistoricalDataQuote(CompanyHistoricalDataQuoted $event): void
    {
        try {
            $this->messageBus->dispatch(
                new CompanyHistoricalDataQuoteEmailNotification(
                    $event->getCompanySymbol(),
                    $event->getStartDate(),
                    $event->getEndDate(),
                    $event->getEmail()
                )
            );
        } catch (Throwable $e) {
            $this->logger->error(
                sprintf('Failed to dispatch %s message', CompanyHistoricalDataQuoteEmailNotification::class),
                [
                    'companySymbol' => $event->getCompanySymbol(),
                    'startDate' => $event->getStartDate()->format(self::DATE_FORMAT),
                    'endDate' => $event->getEndDate()->format(self::DATE_FORMAT),
                    'quoteRequesterEmail' => $event->getEmail(),
                    'exception' => $e,
                ]
            );
        }
    }
}
