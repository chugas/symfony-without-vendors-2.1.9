<?php

namespace Application\Success\CoreBundle\Repository\Doctrine\ORM;

use Doctrine\ORM\EntityRepository;

class EventoRepository extends EntityRepository {
  
  protected $max_per_page = 5;
  
  protected $default_filters = array(
        '_sort_order' => 'timeAt',
        '_sort_by' => 'DESC'
  );
  
  public function createNew() {
    $className = $this->getClassName();

    return new $className;
  }
  
  public function setMaxPerPage($value) {
    $this->max_per_page = $value;
  }
  
  public function getMaxPerPage(){
    return $this->max_per_page;
  }
  
  public function setDefaultFilters($sort_order, $sort_by){
    $this->default_filters['_sort_order'] = $sort_order;
    $this->default_filters['_sort_by'] = $sort_by;
  }
  
  public function getPublicQueryBuilder() {
    $qb = $this->createQueryBuilder('e')
            ->select('partial e.{id, name, slug, description, timeAt, flyer, lineup}, partial p.{id, name, slug }')
            ->leftJoin('e.productora', 'p');

    return $qb;
  }

  public function getQueryBuilder($alias = 'e'){
    return $this->createQueryBuilder($alias);
  }

  public function findProximas($limit = 20){
    $qb = $this->getPublicQueryBuilder()
            ->where('e.timeAt >= CURRENT_TIMESTAMP()')
            ->orderBy('e.timeAt', 'ASC');

    $qb->setMaxResults($limit);

    //$q->useResultCache(true, 300, 'eventos.proximos'); // 5 minutos
    $q = $qb->getQuery();

    return $q->getArrayResult();
  }

  public function findPasadas($limit = 10){
    //fecha, titulo, descripcion
    $qb = $this->getPublicQueryBuilder()
            ->where('e.timeAt < CURRENT_TIMESTAMP()')
            ->orderBy('e.timeAt', 'DESC');

    $qb->setMaxResults($limit);
    
    //$q->useResultCache(true, 300, 'eventos.proximos'); // 5 minutos
    
    $q = $qb->getQuery();

    return $q->getArrayResult();
  }

  public function findByProductora($productora_id, $id = null, $limit = 10){
    //fecha, titulo, descripcion
    $qb = $this->getPublicQueryBuilder()
            ->where('IDENTITY(e.productora) = :productora')
            ->orderBy('e.timeAt', 'DESC');

    if(!is_null($id)){
      $qb->andWhere('e.id <> :id');
      $qb->setParameter('id', $id);
    }

    $qb->setParameter('productora', $productora_id);
    $qb->setMaxResults($limit);
    
    //$q->useResultCache(true, 300, 'eventos.proximos'); // 5 minutos
    
    $q = $qb->getQuery();

    return $q->getArrayResult();
  }

  public function getListQuery($page = 1){
    $qb = $this->getPublicQueryBuilder();

    $qb->orderBy($qb->getRootAlias() . '.' . $this->default_filters['_sort_order'], $this->default_filters['_sort_by'])
       ->setFirstResult( ($page - 1) * $this->max_per_page )
       ->setMaxResults($this->max_per_page);
    
    //$q->useResultCache(true, 300, 'eventos.proximos'); // 5 minutos
    
    return $qb->getQuery();
  }

  public function getList($page = 1){
    $q = $this->getListQuery($page);

    return $q->getArrayResult();
  }    
  
  /*public function findFat($id){
    $qb = $this->createQueryBuilder('n')
            ->select('n,hm,m,hd,d,u,a')
            ->leftJoin('n.newsHasMedias', 'hm')
            ->leftJoin('n.newsHasDocuments', 'hd')
            ->leftJoin('hm.media', 'm')
            ->leftJoin('hd.media', 'd')
            ->leftJoin('n.columnista', 'u')
            ->leftJoin('n.avatar', 'a')
            ->where('n.id = :id');

    $qb->setParameter('id', $id);
    
    $query = $qb->getQuery();
    $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
    return $query->getOneOrNullResult(Query::HYDRATE_ARRAY);
  }*/  
}
