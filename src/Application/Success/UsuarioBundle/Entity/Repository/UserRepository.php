<?php

namespace Application\Success\UsuarioBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
  public function getActiveColumnistas($limit){
    $em = $this->getEntityManager();

    $consulta = $em->createQuery("
        SELECT u, n 
        FROM UsuarioBundle:User u JOIN u.news n 
        WHERE n.status = :status 
        and n.published = 1 
        and n.date_published <= CURRENT_TIMESTAMP() 
        ORDER BY n.date_published DESC");

    //and n.date_published > DATE_SUB(CURRENT_TIMESTAMP(), 1, 'MONTH')
    $consulta->setParameter('status', 'aceptada');
    $consulta->setMaxResults($limit);
    return $consulta->getResult(); 
  }
}
