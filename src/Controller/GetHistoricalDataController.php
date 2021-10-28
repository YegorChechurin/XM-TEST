<?php

namespace App\Controller;

use App\Dto\HistoricalDataRequest;
use App\Exception\CannotFindHistoricalDataWithinRequestedDateRange;
use App\Exception\InvalidHistoricalDataRequest;
use App\Normalizer\HistoricalDataCollectionNormalizer;
use App\Service\CompanyHistoricalDataQuoter;
use App\Service\HistoricalDataRequestValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

final class GetHistoricalDataController extends AbstractController
{
    private HistoricalDataRequestValidator $validator;
    private CompanyHistoricalDataQuoter $companyHistoricalDataQuoter;
    private HistoricalDataCollectionNormalizer $dataNormalizer;

    public function __construct(
        HistoricalDataRequestValidator $validator,
        CompanyHistoricalDataQuoter $companyHistoricalDataQuoter,
        HistoricalDataCollectionNormalizer $dataNormalizer
    ) {
        $this->validator = $validator;
        $this->companyHistoricalDataQuoter = $companyHistoricalDataQuoter;
        $this->dataNormalizer = $dataNormalizer;
    }

    /**
     * @Route("/historical-data", name="get_historical_data")
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $historicalDataRequest = new HistoricalDataRequest(
                (string) $request->request->get('companySymbol'),
                (string) $request->request->get('startDate'),
                (string) $request->request->get('endDate'),
                (string) $request->request->get('email')
            );
            $this->validator->validate($historicalDataRequest);

            $quote = $this->companyHistoricalDataQuoter->quoteForDateRange(
                $historicalDataRequest->getCompanySymbol(),
                $historicalDataRequest->getStartDateAsDateTimeImmutable(),
                $historicalDataRequest->getEndDateAsDateTimeImmutable(),
                $historicalDataRequest->getEmail()
            );
        } catch (InvalidHistoricalDataRequest $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (CannotFindHistoricalDataWithinRequestedDateRange $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return new JsonResponse('Sorry, something went wrong. Please try again', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->dataNormalizer->normalize($quote));
    }
}
