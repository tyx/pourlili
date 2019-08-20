<?php
namespace App\SharedKernel\Bridge;

use App\Listing\App\Query\ListIdOfHost;
use App\SharedKernel\Bridge\QueryBus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CurrentListListener implements EventSubscriberInterface
{
    private $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['guessCurrentList', 10],
            ],
        ];
    }

    public function guessCurrentList(RequestEvent $event)
    {
        if (false === $event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');

        if ('waiting' === $routeName || 0 === strpos($routeName, 'admin_') || 0 === strpos($routeName, '_')) {
            return;
        }

        $listId = $this->queryBus->query(new ListIdOfHost($request->getHost()));

        if (null === $listId) {
            $event->setResponse(
                new RedirectResponse('/waiting.html')
            );

            return;
        }

        $listId = base64_decode($listId);

        putenv("CURRENT_LIST={$listId}");
    }
}
