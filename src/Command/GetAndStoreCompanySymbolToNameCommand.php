<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\CompanySymbolToNameRepository;
use App\Service\DatahubClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GetAndStoreCompanySymbolToNameCommand extends Command
{
    protected static $defaultName = 'company-symbol-to-name:get-and-store';

    private DatahubClient $datahubClient;
    private CompanySymbolToNameRepository $companySymbolToNameRepo;

    public function __construct(DatahubClient $datahubClient, CompanySymbolToNameRepository $companySymbolToNameRepo)
    {
        parent::__construct();

        $this->datahubClient = $datahubClient;
        $this->companySymbolToNameRepo = $companySymbolToNameRepo;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $metaData = $this->datahubClient->getCompanyMetaDataList();
        foreach ($metaData as $datum) {
            if (empty($datum['Symbol']) || empty($datum['Company Name'])) {
                continue;
            }

            $this->companySymbolToNameRepo->saveCompanySymbolToName($datum['Symbol'], $datum['Company Name']);
        }

        return Command::SUCCESS;
    }
}
