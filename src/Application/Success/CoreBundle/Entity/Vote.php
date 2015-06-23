<?php

namespace Application\Success\CoreBundle\Entity;

use FOS\CommentBundle\Entity\Vote as BaseVote;
use FOS\CommentBundle\Model\SignedVoteInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Vote extends BaseVote implements SignedVoteInterface {

  protected $id;
  protected $voter;
  protected $comment;

  /**
   * Sets the owner of the vote
   *
   * @param string $user
   */
  public function setVoter(UserInterface $voter) {
    $this->voter = $voter;
  }

  /**
   * Gets the owner of the vote
   *
   * @return UserInterface
   */
  public function getVoter() {
    return $this->voter;
  }

}
