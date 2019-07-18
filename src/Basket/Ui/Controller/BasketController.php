<?php

namespace App\Basket\Ui\Controller;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ramsey\Uuid\Uuid;
use App\SharedKernel\Bridge\QueryBus;
use App\SharedKernel\Bridge\CommandBus;
use App\Basket\App\Query\ShowBasket;
use App\Basket\App\Command;

class BasketController
{
    private $twig;

    private $commandBus;

    private $queryBus;

    public function __construct(Environment $twig, CommandBus $commandBus, QueryBus $queryBus)
    {
        $this->twig = $twig;
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    public function index(SessionInterface $session)
    {
        $basketId = $session->get('basket_id');

        if (null === $basketId) {
            return new Response(
                $this->twig->render(
                    'Basket/empty.html.twig'
                ),
                Response::HTTP_OK
            );
        }

        return new Response(
            $this->twig->render(
                'Basket/index.html.twig',
                [
                   'basket' => $this->queryBus->query(new ShowBasket($basketId))
                ]
            ),
            Response::HTTP_OK
        );
    }

    public function addProduct(Request $request, SessionInterface $session, string $currentList)
    {
        $basketId = $session->get('basket_id');

        if (null !== $basketId) {
            $basketId = Uuid::fromString($basketId);
        }
    
        if (null === $basketId) {
            $basketId = Uuid::uuid4();
            $this->commandBus->execute(
                new Command\PickUpBasket($basketId, $currentList)
            );
            $session->set('basket_id', $basketId->toString());
        }

        $this->commandBus->execute(
            new Command\AddProductToBasket(
                $basketId,
                $request->request->get('product_id'),
                $request->request->get('amount')
            )
        );

        return new JsonResponse(['status' => 'ok']);
    }
}