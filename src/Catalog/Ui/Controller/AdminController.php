<?php
namespace App\Catalog\Ui\Controller;

use App\Catalog\Ui\Form\NewProductForm;
use App\SharedKernel\Bridge\CommandBus;
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

    public function __construct(Environment $twig, CommandBus $commandBus)
    {
        $this->twig = $twig;
        $this->commandBus = $commandBus;
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
                ]
            )
        );
    }
}