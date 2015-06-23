<?php

namespace Application\Success\CoreBundle\Entity;

use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\SignedCommentInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Comment extends BaseComment implements SignedCommentInterface, VotableCommentInterface {

  protected $id;
  
  protected $author;

  protected $thread;

  protected $score = 0;

  /**
   * Sets the score of the comment.
   *
   * @param integer $score
   */
  public function setScore($score) {
    $this->score = $score;
  }

  /**
   * Returns the current score of the comment.
   *
   * @return integer
   */
  public function getScore() {
    return $this->score;
  }

  /**
   * Increments the comment score by the provided
   * value.
   *
   * @param integer value
   *
   * @return integer The new comment score
   */
  public function incrementScore($by = 1) {
    $this->score += $by;
  }

  public function setAuthor(UserInterface $author) {
    $this->author = $author;
  }

  public function getAuthor() {
    return $this->author;
  }

  public function getAuthorName() {
    if (null === $this->getAuthor()) {
      return 'Anonymous';
    }

    return $this->getAuthor();
  }

}
