<?php

namespace App\Contribution\Ui\Controller;

use App\Contribution\App\Command\ConfirmContribution;
use App\Contribution\App\Query\AllContributionOfList;
use App\Contribution\App\Query\GuestBookOfList;
use App\Listing\App\Query\ListOfId;
use App\SharedKernel\Bridge\CommandBus;
use App\SharedKernel\Bridge\QueryBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    public function index($listId)
    {
        return new Response(
            $this->twig->render('Admin/Contribution/index.html.twig', [
                'contributions' => $this->queryBus->query(new AllContributionOfList(base64_decode($listId))),
                'menu_item' => 'contributions',
                'list' => $this->queryBus->query(new ListOfId(Uuid::fromString(base64_decode($listId)))),
            ])
        );
    }

    public function guestbook($listId)
    {
        return new Response(
            $this->twig->render('Admin/Contribution/guestbook.html.twig', [
                'guestbook' => $this->queryBus->query(new GuestBookOfList(base64_decode($listId))),
                'menu_item' => 'guestbook',
                'list' => $this->queryBus->query(new ListOfId(Uuid::fromString(base64_decode($listId)))),
            ])
        );
    }

    public function confirm($listId, $contributionId, RouterInterface $router)
    {
        $this->commandBus->execute(
            new ConfirmContribution(Uuid::fromString(base64_decode($contributionId)))
        );

        return new RedirectResponse(
            $router->generate('admin_contribution_index', ['listId' => $listId])
        );
    }
}
