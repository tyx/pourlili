<?php
declare(strict_types=1);

namespace App\Catalog\Ui\Controller;

use App\Catalog\App\Query\ListAllProducts;
use App\SharedKernel\Bridge\QueryBus;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ProductController
{
    private $twig;

    private $queryBus;

    public function __construct(Environment $twig, QueryBus $queryBus)
    {
        $this->twig = $twig;
        $this->queryBus = $queryBus;
    }

    public function index(string $currentList)
    {
        return new Response(
            $this->twig->render(
                'Catalog/index.html.twig',
                [
                    'products' => $this->queryBus->query(new ListAllProducts($currentList)),
                ]
            ),
            Response::HTTP_OK
        );
    }
}