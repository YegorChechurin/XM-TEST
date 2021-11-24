<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\CannotConvertToCompanyName;
use Webmozart\Assert\Assert;

final class DefaultCompanySymbolToNameConverter implements CompanySymbolToNameConverter
{
    /** @var CompanySymbolToNameConverter[] */
    private iterable $convertersChain;

    public function __construct(iterable $convertersChain)
    {
        Assert::notEmpty($convertersChain, 'Converters chain cannot be empty');
        Assert::allImplementsInterface(
            $convertersChain,
            CompanySymbolToNameConverter::class,
            sprintf('All converters must implement %s interface', CompanySymbolToNameConverter::class)
        );

        $this->convertersChain = $convertersChain;
    }

    public function convert(string $companySymbol): string
    {
        foreach ($this->convertersChain as $converter) {
            try {
                return $converter->convert($companySymbol);
            } catch (CannotConvertToCompanyName $e) {
                continue;
            }
        }

        throw CannotConvertToCompanyName::forCompanySymbol($companySymbol);
    }
}
