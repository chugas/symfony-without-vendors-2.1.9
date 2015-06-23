<?php

namespace Application\Success\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InviteController extends Controller {

    public function redirectAction($service) {
        if($service == 'google') {

            try {
                $google = $this->get('success.google.contacts');
                $googleClient = $google->getClient();
                $googleClient->setup();
                $googleClient->authenticate($this->getRequest()->get("code"));
                $googleClient->setAccessToken($googleClient->getAccessToken());

                $contacts = $google->getContacts();

                $eventos = $this->get('success.repository.evento')->findProximas(10);

                if(!$eventos){
                  $eventos = $this->get('success.repository.evento')->findPasadas(4);
                }

                $response = $this->render('WebBundle:Frontend/Default:index.html.twig', array('eventos' => $eventos, 'contacts' => $contacts));
                
                return $response;

            } catch (\Exception $e) {
                return $this->redirect($this->generateUrl('homepage'));
            }
            
            return $this->redirect($this->generateUrl('homepage'));
        }
    }

}
