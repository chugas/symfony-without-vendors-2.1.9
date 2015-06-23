<?php

namespace Application\Success\CoreBundle\OAuth\ResourceOwner;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Buzz\Message\RequestInterface as HttpRequestInterface;
use HWI\Bundle\OAuthBundle\Security\OAuthUtils;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\YahooResourceOwner as BaseResourceOwner;

class YahooResourceOwner extends BaseResourceOwner {

  public function getContacts(array $accessToken, array $extraParameters = array()) {
    $this->options['contacts_url'] = str_replace('{guid}', $accessToken['xoauth_yahoo_guid'], $this->options['contacts_url']);

    return $this->doGetContacts($accessToken, $extraParameters);
  }

  public function doGetContacts(array $accessToken, array $extraParameters = array()) {
    $parameters = array_merge(array(
        'oauth_consumer_key' => $this->options['client_id'],
        'oauth_timestamp' => time(),
        'oauth_nonce' => $this->generateNonce(),
        'oauth_version' => '1.0',
        'oauth_signature_method' => $this->options['signature_method'],
        'oauth_token' => $accessToken['oauth_token'],
            ), $extraParameters);

    $url = $this->options['contacts_url'];
    $parameters['oauth_signature'] = OAuthUtils::signRequest(
                    HttpRequestInterface::METHOD_GET, $url, $parameters, $this->options['client_secret'], $accessToken['oauth_token_secret'], $this->options['signature_method']
    );

    $content = $this
            ->httpRequest($url, null, $parameters, array('Accept: application/json'))
            ->getContent();

    return $this->formatResponse($content);
  }

  /**
   * {@inheritDoc}
   */
  protected function configureOptions(OptionsResolverInterface $resolver) {
    parent::configureOptions($resolver);

    $resolver->setDefaults(array(
        'authorization_url' => 'https://api.login.yahoo.com/oauth/v2/request_auth',
        'request_token_url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
        'access_token_url' => 'https://api.login.yahoo.com/oauth/v2/get_token',
        'infos_url' => 'http://social.yahooapis.com/v1/user/{guid}/profile',
        'contacts_url' => 'http://social.yahooapis.com/v1/user/{guid}/contacts',
        'realm' => 'yahooapis.com',
    ));
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