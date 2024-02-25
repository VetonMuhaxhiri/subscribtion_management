<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\UserSubscription;
use App\Repository\SubscriptionRepository;
use App\Repository\UserSubscriptionRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\Clock\now;

class SubscriptionsController extends ApiController
{
    
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private TokenStorageInterface $tokenStorage,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('api/subscriptions', methods:'GET')]
    public function getAll(#[MapQueryParameter] int $page = 1, #[MapQueryParameter] int $perPage = 5): JsonResponse
    {            
        return $this->json($this->subscriptionRepository->getAllPaginated($page, $perPage));
    }

    #[Route('api/subscriptions/me', methods:'GET')]
    public function getMySubscriptions()
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        return $this->json($this->subscriptionRepository->getUserSubscriptions($user, 1, 10));
    }

    #[Route('api/subscriptions/{id}', methods:'GET')]
    public function findOne(int $id)
    {
        $subscription = $this->subscriptionRepository->findOneBy(['id' => $id]);
        
        if(!$subscription) {
            return $this->respondNotFound('Subscription not found');
        }
        
        return $this->json($subscription);
    }

    #[Route('api/subscriptions/{id}/subscribe', methods:'POST')]
    public function subscribe(int $id, UserSubscriptionRepository $userSubscriptionRepository)
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        $subscription = $this->subscriptionRepository->findOneBy(['id' => $id]);

        if(!$subscription) {
            return $this->respondNotFound('Subscription not found');
        }

        $alreadySubscribed = $userSubscriptionRepository->findOneBy(['user' => $user->getId(), 'subscription' => $subscription->getId()]);
        
        if($alreadySubscribed) {
            return $this->respondValidationError('Already subscribed to this subscription.');
        }

        $startDate = new DateTimeImmutable();
        $newSubscription = new UserSubscription();
        $newSubscription->setUser($user);
        $newSubscription->setSubscription($subscription);
        $newSubscription->setStatus(Subscription::ACTIVE);
        $newSubscription->setStartDate(now());
        $newSubscription->setEndDate($startDate->modify('+' . $subscription->getDuration() . ' days'));
        
        $this->entityManager->persist($newSubscription);
        $this->entityManager->flush();
        
        return $this->json($subscription, Response::HTTP_CREATED);
    }

    #[Route('api/subscriptions/{id}/unsubscribe', methods:'POST')]
    public function unsubscribe(int $id, UserSubscriptionRepository $userSubscriptionRepository)
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        $subscription = $this->subscriptionRepository->findOneBy(['id' => $id]);

        if(!$subscription) {
            return $this->respondNotFound('Subscription not found');
        }

        $userSubscription = $userSubscriptionRepository->findOneBy(['user' => $user->getId(), 'subscription' => $subscription->getId()]);

        if(!$userSubscription) {
            return $this->respondValidationError('You are not subscribed to this subscription.');
        }

        $this->entityManager->remove($userSubscription);
        $this->entityManager->flush();
        
        return $this->respondWithSuccess('You are now unsubcribed.');
    }
}
