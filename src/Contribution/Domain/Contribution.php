<?php
namespace App\Contribution\Domain;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\Uuid;

class Contribution extends AggregateRoot
{
    private $id;

    private $listId;

    private $basketId;

    private $amount;

    private $author;

    private $signature;

    private $message;

    private $confirmed = false;

    private $successfulPayment;

    public static function checkout(Uuid $contributionId, $listId, $basketId, $amount, $author)
    {
        $self = new self;
        $self->recordThat(
            ContributionWasCheckedOut::occur($contributionId->toString(), [
                'list_id' => $listId,
                'basket_id' => $basketId,
                'amount' => $amount,
                'author' => $author,
            ])
        );

        return $self;
    }

    public function pay(PaymentGateway $paymentGateway)
    {
        if (true === $this->successfulPayment) {
            return;
        }

        try {
            $paymentResult = $paymentGateway->checkout($this->aggregateId(), $this->amount, $this->author);

            if (false === $paymentResult->succeeded()) {
                throw PaymentError::gateway($paymentResult->details());
            }

            $this->recordThat(
                PaymentWasSuccessful::occur($this->aggregateId(), [
                    'list_id' => $this->listId,
                    'payment_url' => $paymentResult->paymentUrl(),
                    'details' => $paymentResult->details(),
                ])
            );
        } catch (PaymentError $exception) {
            $this->recordThat(
                PaymentWasFailed::occur($this->aggregateId(), ['list_id' => $this->listId])
            );
        }
    }

    public function confirm()
    {
        /*if (true === $this->confirmed) {
            return;
        }*/

        $this->recordThat(
            ContributionWasConfirmed::occur($this->aggregateId(), ['basket_id' => $this->basketId, 'list_id' => $this->listId])
        );
    }

    public function sign(string $signature, ?string $message)
    {
        $this->recordThat(
            ContributionWasSigned::occur($this->aggregateId(), ['list_id' => $this->listId, 'signature' => $signature, 'message' => $message])
        );
    }

    protected function aggregateId(): string
    {
        return $this->id->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        switch ($event->messageName()) {
            case ContributionWasCheckedOut::class:
                $this->id = Uuid::fromString($event->aggregateId());
                $this->amount = $event->amount();
                $this->author = $event->author();
                $this->basketId = $event->basketId();
                $this->listId = $event->listId();
                break;
            case PaymentWasFailed::class:
                $this->successfulPayment = false;
                break;
            case PaymentWasSuccessful::class:
                $this->successfulPayment = true;
                break;
            case ContributionWasConfirmed::class:
                $this->confirmed = true;
        }
    }
}