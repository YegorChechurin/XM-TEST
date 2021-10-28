<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CompanyHistoricalDataQuoteEmailNotification;
use App\Service\CompanySymbolToNameConverter;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;
use Throwable;

final class CompanyHistoricalDataQuoteEmailNotificationHandler implements MessageHandlerInterface
{
    private const DATE_FORMAT = 'Y-m-d';

    private CompanySymbolToNameConverter $companySymbolToNameConverter;
    private MailerInterface $mailer;

    public function __construct(CompanySymbolToNameConverter $companySymbolToNameConverter, MailerInterface $mailer)
    {
        $this->companySymbolToNameConverter = $companySymbolToNameConverter;
        $this->mailer = $mailer;
    }

    public function __invoke(CompanyHistoricalDataQuoteEmailNotification $notification): void
    {
        try {
            $companyName = $this->companySymbolToNameConverter->convert($notification->getCompanySymbol());
        } catch (Throwable $e) {
            $companyName = $notification->getCompanySymbol();
        }

        $email = (new Email())
            ->from('yegor.chechurin@gmail.com')
            ->to($notification->getEmail())
            ->subject($companyName)
            ->text(
                sprintf(
                    'From %s to %s',
                    $notification->getStartDate()->format(self::DATE_FORMAT),
                    $notification->getEndDate()->format(self::DATE_FORMAT)
                )
            );

        $this->mailer->send($email);
    }
}
