<?php

namespace Application\Success\ForoBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ForoBundle extends Bundle
{
  public function getParent() {
    return 'CCDNForumForumBundle';
  }  
}
