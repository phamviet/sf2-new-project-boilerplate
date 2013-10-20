<?php

namespace MY\EntityBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MY\EntityBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\NoResultException;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
        ;

        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf('Unable to find User object identified by "%s".', $username);
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    /**
     * @param $q
     * @param array $params
     * @return array
     */
    public function suggestFriend($q, $params = array())
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u.id AS value, u.name AS label')
                ->where('u.enabled = 1')
                ->andWhere('u.locked = 0')
                ->andWhere('u.expired = 0');

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->like('u.usernameCanonical', $qb->expr()->literal('%' . $q . '%')),
                $qb->expr()->like('u.name', $qb->expr()->literal('%' . $q . '%'))
            )
        );

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQuery()
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->where('u.enabled = 1')
            ->andWhere('u.locked = 0')
            ->andWhere('u.expired = 0');

        return $qb;
    }
}
