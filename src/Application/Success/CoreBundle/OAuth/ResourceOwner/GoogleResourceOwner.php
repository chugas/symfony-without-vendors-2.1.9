<?php

namespace Application\Success\CoreBundle\OAuth\ResourceOwner;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Buzz\Message\RequestInterface as HttpRequestInterface;
use HWI\Bundle\OAuthBundle\Security\OAuthUtils;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GoogleResourceOwner as BaseResourceOwner;

class GoogleResourceOwner extends BaseResourceOwner {

    const CONTACTS_API_URL = 'https://www.google.com/m8/feeds/contacts/default/full?';
    //return $this->buzz->get("https://www.google.com/m8/feeds/contacts/default/full?access_token=" . $accessToken['access_token']);
    public function getContacts(array $accessToken, array $extraParameters = array()) {
        return $this->doGetContacts($accessToken, $extraParameters);
    }

    public function doGetContacts(array $accessToken, array $extraParameters = array()) {
        $urlParams = array();
        $startIndex = null;
        $maxResults = 9999;

        if (null !== $startIndex) {
            $urlParams['start-index'] = $startIndex;
        }

        if (null !== $maxResults) {
            $urlParams['max-results'] = $maxResults;
        }
        
        $urlParams['access_token'] = $accessToken['access_token'];

        $url = self::CONTACTS_API_URL . http_build_query($urlParams);

        /*$parameters['oauth_signature'] = OAuthUtils::signRequest(
                        HttpRequestInterface::METHOD_GET, 
                        $url, 
                        $parameters, 
                        $this->options['client_secret'], 
                        $accessToken['oauth_token_secret'], 
                        $this->options['signature_method']
        );*/
        
        $content = $this
                ->httpRequest(self::CONTACTS_API_URL, null, $urlParams, HttpRequestInterface::METHOD_GET)
                ->getContent();
var_dump($content);die();
        return $this->formatResponse($content);
    }

    protected function formatResponse($response) {
        if (is_array($response)) {
            return $response;
        } else {
            // First check that response exists, due too bug: https://bugs.php.net/bug.php?id=54484
            if (!$response) {
                return array();
            } else {
                return json_decode($response, true);
            }
        }
    }

}
