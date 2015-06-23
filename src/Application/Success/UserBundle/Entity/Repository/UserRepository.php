<?php

namespace Application\Success\UserBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository {

  protected $max_per_page = 32;
  
  protected $default_filters = array(
        '_sort_order' => 'lastLogin',
        '_sort_by' => 'DESC'
  );

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
  
  public function getPublicQueryBuilder($id = null) {
    $qb = $this->createQueryBuilder('u')
            ->select('partial u.{id, firstname, lastname, gender, dateOfBirth, username, email}, partial a.{id, providerReference, context}')
            ->leftJoin('u.avatar', 'a')
            ->where('u.enabled = 1')
            ->andWhere('u.locked = 0');
    
    if (!is_null($id)) {
      $qb->andWhere('u.id != :id');
      $qb->setParameter('id', $id);
    }
    
    return $qb;
  }

  public function getListQuery($page = 1, $id = null){
    $qb = $this->getPublicQueryBuilder($id);

    $qb->orderBy($qb->getRootAlias() . '.' . $this->default_filters['_sort_order'], $this->default_filters['_sort_by'])
       ->setFirstResult( ($page - 1) * $this->max_per_page )
       ->setMaxResults($this->max_per_page);
    
    return $qb->getQuery();
  }

  public function getList($page = 1, $id = null){
    $q = $this->getListQuery($page, $id);

    //return $q->execute();
    return $q->getArrayResult();
  }
  
  /*public function suggest($q, $limit = 10) {
    $stmt = $this->getEntityManager()
      ->getConnection()
      ->prepare('
        SELECT u.id, CONCAT(u.firstname, " " ,u.lastname) as title, u.biography as description, u.gender, u.avatar, u.username, u.email 
        FROM success_users as u 
        WHERE u.enabled = 1 AND u.locked = 0 AND u.email LIKE :title OR u.firstname LIKE :title OR u.lastname LIKE :title 
        LIMIT ' . $limit);
    $stmt->bindValue('title', '%' . $q . '%');
    $stmt->execute();
    return $stmt->fetchAll();
  }*/
  
  public function findListByUsernames($usernames) {
    $stmt = $this->getEntityManager()
      ->getConnection()
      ->prepare('
        SELECT u.id, CONCAT(u.firstname, " " ,u.lastname) as title, u.username, u.email    
        FROM success_users as u 
        WHERE u.enabled = 1 AND u.locked = 0 AND FIND_IN_SET (u.username, :usernames)');
    $stmt->bindValue('usernames', implode(',', $usernames));
    $stmt->execute();
    return $stmt->fetchAll();    
  }
  
  /*public function findUserByUsername($username, $hydrationMode = Query::HYDRATE_OBJECT) {
    $qb = $this->createQueryBuilder('u')
            ->select('partial u.{id, firstname, lastname, biography, dateOfBirth, city, country, avatar, username, gender, is_columnista}, partial o.{ name, id }, partial c.{ name, id }')
            ->leftJoin('u.occupation', 'o')
            ->leftJoin('u.company_work', 'c')
            ->where('u.username = :username');

    $qb->setParameter('username', $username);

    $query = $qb->getQuery();
    if($hydrationMode == Query::HYDRATE_OBJECT) {
      $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
      return $query->getOneOrNullResult();
    }
    
    return $query->getOneOrNullResult(Query::HYDRATE_ARRAY);
  }
  
  public function findFatUser($id) {
    $qb = $this->createQueryBuilder('u')
            ->addSelect('o,c,i')
            ->leftJoin('u.occupation', 'o')
            ->leftJoin('u.companies', 'c')
            ->leftJoin('u.invitation', 'i')
            ->where('u.id = :id');

    $qb->setParameter('id', $id);

    $query = $qb->getQuery();
    //$query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);

    //return $query->getOneOrNullResult(Query::HYDRATE_ARRAY);
    return $query->getOneOrNullResult();    
  }*/
  
  public function suggest($q, $limit = 10) {
    $stmt = $this->getEntityManager()
      ->getConnection()
      ->prepare('
        SELECT u.id, CONCAT(u.firstname, " " ,u.lastname) as name  
        FROM success_user as u 
        WHERE u.enabled = 1 AND u.locked = 0 AND u.firstname LIKE :title OR u.lastname LIKE :title 
        LIMIT ' . $limit);
    $stmt->bindValue('title', '%' . $q . '%');
    $stmt->execute();
    return $stmt->fetchAll();
  }
  
  public function findMinData($id){
    $stmt = $this->getEntityManager()
      ->getConnection()
      ->prepare('
        SELECT u.id, CONCAT(u.firstname, " " ,u.lastname) as name, u.username   
        FROM success_user as u 
        WHERE u.enabled = 1 AND u.locked = 0 AND id = :id 
        LIMIT 1');
    $stmt->bindValue('id', $id);
    $stmt->execute();
    return $stmt->fetch();
  }
}
