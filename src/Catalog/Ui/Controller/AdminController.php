<?php
namespace App\Catalog\Ui\Controller;

use App\Catalog\Ui\Form\NewProductForm;
use App\Listing\App\Query\ListOfId;
use App\SharedKernel\Bridge\CommandBus;
use App\SharedKernel\Bridge\QueryBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class AdminController
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

    public function new(Request $request, FormFactoryInterface $formFactory, $listId, RouterInterface $router)
    {
        $productId = Uuid::uuid4();
        $form = $formFactory->create(
            NewProductForm::class,
            null,
            [
                'list_id' => Uuid::fromString(base64_decode($listId)),
                'product_id' => $productId,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->execute($form->getData());

            return new RedirectResponse(
                $router->generate('admin_listing_show', ['listId' => $listId])
            );
        }

        return new Response(
            $this->twig->render(
                'Admin/Catalog/new.html.twig',
                [
                    'form' => $form->createView(),
                    'list' => $this->queryBus->query(new ListOfId(Uuid::fromString(base64_decode($listId)))),
                    'menu_item' => 'products',
                ]
            )
        );
    }
}
