<?php

namespace Application\Success\CoreBundle\Repository\Doctrine\ORM;

use Doctrine\ORM\EntityRepository;

class ProductoraRepository extends EntityRepository {

  public function createNew() {
    $className = $this->getClassName();

    return new $className;
  }

  public function getQueryBuilder($alias = 'e'){
    return $this->createQueryBuilder($alias);
  }

  public function findProximas($limit = 20){
    $consulta = $this->getQueryBuilder()
            ->where('e.createdAt > CURRENT_TIMESTAMP()')
            ->orderBy('e.createdAt', 'ASC');

    $consulta->setMaxResults($limit);
    
    //$q->useResultCache(true, 300, 'eventos.proximos'); // 5 minutos
    
    return $consulta->getQuery()->execute();
  }
}
