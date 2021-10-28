<?php

namespace App\Controller;

use App\Form\GetHistoricalDataType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    /**
     * @Route("/index")
     */
    public function indexIndex(): Response
    {
        return $this->renderForm('index/index_index.html.twig');
    }
}
