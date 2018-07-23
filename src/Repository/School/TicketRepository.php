<?php

namespace App\Repository\School;

use App\Entity\School\Ticket;
use App\Entity\School\Vote;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function findOneByUserAndVote(User $user, Vote $vote) {
        return $this->findOneBy(["user"=>$user, "vote"=>$vote]);
    }
}
