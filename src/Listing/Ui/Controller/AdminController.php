<?php

namespace App\Listing\Ui\Controller;

use App\Catalog\App\Query\ListAllProducts;
use App\Listing\App\Command\DisableList;
use App\Listing\App\Command\EnableList;
use App\Listing\App\Command\StartList;
use App\Listing\App\Query\AllLists;
use App\Listing\App\Query\ListOfId;
use App\SharedKernel\Bridge\CommandBus;
use App\SharedKernel\Bridge\QueryBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class AdminController
{
    private $twig;

    private $queryBus;

    private $commandBus;

    public function __construct(Environment $twig, QueryBus $queryBus, CommandBus $commandBus)
    {
        $this->twig = $twig;
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
    }

    public function index(Request $request, RouterInterface $router)
    {
        if ($request->request->has('listId')) {
            return new RedirectResponse(
                $router->generate('admin_listing_show', ['listId' => $request->request->get('listId')])
            );
        }

        return new Response(
            $this->twig->render(
                'Admin/Listing/choose.html.twig',
                [
                    'lists' => $this->queryBus->query(new AllLists()),
                ]
            )
        );
    }

    public function show(string $listId)
    {
        return new Response(
            $this->twig->render(
                'Admin/Listing/show.html.twig',
                [
                    'items' => $this->queryBus->query(new ListAllProducts(base64_decode($listId))),
                    'list' => $this->queryBus->query(new ListOfId(Uuid::fromString(base64_decode($listId)))),
                ]
            )
        );
    }

    public function new(Request $request, RouterInterface $router, FormFactoryInterface $formFactory)
    {
        $form = $formFactory->createBuilder()
            ->add('host', TextType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id = Uuid::uuid4();
            $host = $form->get('host')->getData();
            $this->commandBus->execute(new StartList($id, $host));

            return new RedirectResponse(
                $router->generate('admin_listing_show', ['listId' => base64_encode($id->toString())])
            );
        }

        return new Response(
            $this->twig->render(
                'Admin/Listing/new.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }

    public function enable($listId, RouterInterface $router)
    {
        $this->commandBus->execute(
            new EnableList(Uuid::fromString(base64_decode($listId)))
        );

        return new RedirectResponse(
            $router->generate('admin_listing_show', ['listId' => $listId])
        );
    }

    public function disable($listId, RouterInterface $router)
    {
        $this->commandBus->execute(
            new DisableList(Uuid::fromString(base64_decode($listId)))
        );

        return new RedirectResponse(
            $router->generate('admin_listing_show', ['listId' => $listId])
        );
    }
}