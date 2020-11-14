<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class StatsService.
 */
class StatsService
{
    private EntityManagerInterface $em;

    /**
     * StatsService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getStats()
    {
        $users = $this->getUsersCount();
        $city = $this->getCityCount();
        $bookings = $this->getBookingsCount();
        $ads = $this->getAdsCount();

        return compact('users', 'city', 'bookings', 'ads');
    }

    public function getUsersCount()
    {
        return $this->em->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getCityCount()
    {
        return $this->em->createQuery('SELECT COUNT(c) FROM App\Entity\City c')->getSingleScalarResult();
    }

    public function getBookingsCount()
    {
        return $this->em->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
    }

    public function getAdsCount()
    {
        return $this->em->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }
}