<?php
namespace App\Contribution\Ui\Controller;

use App\Basket\App\Query\ShowBasket;
use App\Contribution\App\Command\ConfirmContribution;
use App\Contribution\App\Command\ContributeToList;
use App\Contribution\App\Query\PaymentUrlOfContribution;
use App\Contribution\Ui\Form\CheckoutForm;
use App\SharedKernel\Bridge\CommandBus;
use App\SharedKernel\Bridge\QueryBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

class ContributionController
{
    private $twig;

    private $formFactory;

    private $commandBus;

    private $queryBus;

    public function __construct(Environment $twig, FormFactoryInterface $formFactory, CommandBus $commandBus, QueryBus $queryBus)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    public function checkout(Request $request, SessionInterface $session, $currentList)
    {
        $basketId = $session->get('basket_id');

        if (null === $basketId) {
            throw new \LogicException('Cannot checkout undefined basket');
        }

        $form = $this->formFactory->create(CheckoutForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $contributionId = Uuid::uuid4();
            $basket = $this->queryBus->query(new ShowBasket($basketId));

            $this->commandBus->execute(
                new ContributeToList(
                    $contributionId,
                    $currentList,
                    $basketId,
                    $basket['total'],
                    $data['email'],
                    $data['signature'],
                    $data['message']
                )
            );

            return new RedirectResponse(
                $this->queryBus->query(
                    new PaymentUrlOfContribution($contributionId)
                )
            );
        }

        return new Response(
            $this->twig->render(
                'Contribution/checkout.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }

    public function confirm(Request $request, $contributionId)
    {
        // Need a token
        $contributionId = Uuid::fromString(base64_decode($contributionId));
        $this->commandBus->execute(
            new ConfirmContribution($contributionId)
        );

        return new JsonResponse(['status' => 'ok']);
    }

    public function thanks(SessionInterface $session)
    {
        if (null === $session->get('basket_id')) {
            return new Response('Not found', Response::HTTP_NOT_FOUND);
        }

        $session->remove('basket_id');

        return new Response(
            $this->twig->render(
                'Contribution/thanks.html.twig'
            ),
            Response::HTTP_OK
        );
    }
}