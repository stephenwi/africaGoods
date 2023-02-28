<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoodsController extends AbstractController
{
    #[Route('/', name: 'africa_goods')]
    public function index(): Response
    {
        return $this->render('goods/index.html.twig', [
            'controller_name' => 'GoodsController',
        ]);
    }
}
