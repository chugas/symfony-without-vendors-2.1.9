<?php

namespace Application\Success\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller {

//$response = array('files' => array());
//return new Response(json_encode($response), 200, array('Content-Type' => 'application/json'));

/* $response = array(
  'files' => array(
  0 => array(
  'name' => 'chugas.pdf',
  'size' => 9865446,
  'type' => 'application/pdf',
  'media' => 4234
  )
  )
  );
  //{"files":[{"name":"aerea.png","size":2485724,"type":"image\/png","url":"http:\/\/localhost\/upload890\/server\/php\/files\/aerea.png","thumbnailUrl":"http:\/\/localhost\/upload890\/server\/php\/files\/thumbnail\/aerea.png","deleteUrl":"http:\/\/localhost\/upload890\/server\/php\/?file=aerea.png","deleteType":"DELETE"}]}
  return new Response(json_encode($response), 200, array('Content-Type' => 'application/json')); */
  //$context, $providerName, $object_id = null, $object_class = null
  public function uploadAction($context, $providerName, $objectClass = null, $objectId = null) {
    try {
      if($providerName == 'sonata.media.provider.soundcloud') {
        return $this->uploadSoundcloud($context, $providerName, $objectClass, $objectId);
      }
      
      $request = $this->get('request');

      if ($request->isMethod('POST')) {
        $files = $request->files->get('files');

        $mediaManager = $this->get('sonata.media.manager.media');

        $mediaAdmin = $this->get('sonata.media.admin.media');

        $provider = $this->get($providerName);

        $mediaClass = $mediaAdmin->getClass();

        foreach ($files as $file) {
          $media = new $mediaClass();

          $media->setProviderName($provider->getName());
          $media->setContext($context);
          $media->setEnabled(true);
          $media->setName($file->getClientOriginalName());
          $media->setBinaryContent($file);
          $mediaManager->save($media);
        }

        $thumb = '';
        if ($provider->getName() == 'sonata.media.provider.image') {
          $avalancheService = $this->get('imagine.cache.path.resolver');
          $path = $this->get('success.twig.core')->path($media, 'reference');
        }

        if(!is_null($objectId)){
          switch ($objectClass) {
            case 'evento':
                $thumb = $avalancheService->getBrowserPath($path, 'evento_thumb');              
                //$this->mediaCompany($media, $objectId);
              break;
            case 'users':
                $thumb = $avalancheService->getBrowserPath($path, 'users_small');
                $this->avatarUser('image', $media);
              break;
            default: throw new \Exception('invalid context object'); break;
          }
        } else {
          switch ($objectClass) {
            case 'evento':
                $thumb = $avalancheService->getBrowserPath($path, 'evento_thumb');
              break;
            case 'users':
                $thumb = $avalancheService->getBrowserPath($path, 'users_small');
              break;
            default: throw new \Exception('invalid context object'); break;
          }
        }

        $response = array(
            'files' => array(
                0 => array(
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'media' => $media->getId(),
                    'thumbnailUrl' => $thumb
                )
            )
        );
        
        return new Response(json_encode($response), 200, array('Content-Type' => 'application/json'));
        
      }
      
      return new Response(json_encode(array('files' => array())), 200, array('Content-Type' => 'application/json'));
      
    } catch (\Exception $e) {
      return new Response(json_encode(array('data' => 'nok' . '' . $e->getMessage())), 200, array('Content-Type' => 'application/json'));
    } catch (\InvalidArgumentException $e) {
      return new Response(json_encode(array('data' => 'nok' . '' . $e->getMessage())), 200, array('Content-Type' => 'application/json'));
    }
  }

  public function uploadVideoAction($context, $providerName, $objectClass = null, $objectId = null) {
    try {
      $request = $this->get('request');

      if ($request->isMethod('POST')) {
        $mediaManager = $this->get('sonata.media.manager.media');

        $mediaAdmin = $this->get('sonata.media.admin.media');

        $provider = $this->get($providerName);

        $mediaClass = $mediaAdmin->getClass();
        $media = new $mediaClass();
        $media->setProviderName($provider->getName());
        $media->setContext($context);
        $media->setEnabled(true);
        $media->setBinaryContent($this->getRequest()->get('url'));
        $mediaManager->save($media);
        $metadata = $media->getProviderMetadata();

        // Funciona unicamente para usuarios por el momento
        if(!is_null($objectId) && !is_null($objectClass)){
          $user = $this->getUser();
          $user->addYoutube($media);
          $em = $this->getDoctrine()->getManager();
          $em->persist($user);
          $em->flush($user);
        }
        /*array(13) {
          ["author_url"]=>
          string(45) "http://www.youtube.com/user/newagemusicgarden"
          ["thumbnail_width"]=>
          int(480)
          ["height"]=>
          int(344)
          ["author_name"]=>
          string(30) "Relaxing New Age Music Channel"
          ["html"]=>
          string(136) "<iframe width="459" height="344" src="http://www.youtube.com/embed/DSeiwdJt4EQ?feature=oembed" frameborder="0" allowfullscreen></iframe>"
          ["version"]=>
          string(3) "1.0"
          ["thumbnail_url"]=>
          string(47) "http://i.ytimg.com/vi/DSeiwdJt4EQ/hqdefault.jpg"
          ["provider_name"]=>
          string(7) "YouTube"
          ["width"]=>
          int(459)
          ["provider_url"]=>
          string(23) "http://www.youtube.com/"
          ["type"]=>
          string(5) "video"
          ["title"]=>
          string(50) "3 Horas de Musica Relajante | Musica de Relajacion"
          ["thumbnail_height"]=>
          int(360)
        }*/        

        $response = array(
                    'name' => $media->getName(),
                    'author' => $media->getAuthorName(),
                    'size' => $media->getSize(),
                    'media' => $media->getId(),
                    'reference' => $media->getProviderReference(),
                    'thumbnailUrl' => $metadata['thumbnail_url']);
        
        return new Response(json_encode($response), 200, array('Content-Type' => 'application/json'));
        
      }
      
      return new Response(json_encode(array('files' => array())), 200, array('Content-Type' => 'application/json'));      
      
    } catch (\Exception $e) {
      return new Response(json_encode(array('data' => 'nok' . '' . $e->getMessage())), 200, array('Content-Type' => 'application/json'));
    } catch (\InvalidArgumentException $e) {
      return new Response(json_encode(array('data' => 'nok' . '' . $e->getMessage())), 200, array('Content-Type' => 'application/json'));
    }
  }

  public function uploadSoundcloud($context, $providerName, $objectClass, $objectId) {
    try {
      $request = $this->get('request');

      if ($request->isMethod('POST')) {
        $mediaManager = $this->get('sonata.media.manager.media');

        $mediaAdmin = $this->get('sonata.media.admin.media');

        $provider = $this->get($providerName);

        $mediaClass = $mediaAdmin->getClass();
        $media = new $mediaClass();
        $media->setProviderName($provider->getName());
        $media->setContext($context);
        $media->setEnabled(true);
        $media->setBinaryContent($this->getRequest()->get('url'));
        $mediaManager->save($media);
        $metadata = $media->getProviderMetadata();
        
        // Funciona unicamente para usuarios por el momento
        if(!is_null($objectId) && !is_null($objectClass)){
          $user = $this->getUser();
          $user->addSong($media);
          $em = $this->getDoctrine()->getManager();
          $em->persist($user);
          $em->flush($user);
        }

        $iframe_code = $metadata['html'];
        $reference = $this->get('success.twig.core')->iframe_src($iframe_code);
        $response = array(
                    'name' => $media->getName(),
                    'author' => $media->getAuthorName(),
                    'media' => $media->getId(),
                    'reference' => $reference,
                    'thumbnailUrl' => $metadata['thumbnail_url']);
        
        return new Response(json_encode($response), 200, array('Content-Type' => 'application/json'));
        
      }
      
      return new Response(json_encode(array('files' => array())), 200, array('Content-Type' => 'application/json'));
      
    } catch (\Exception $e) {
      return new Response(json_encode(array('data' => 'nok' . ' ' . $e->getMessage())), 200, array('Content-Type' => 'application/json'));
    } catch (\InvalidArgumentException $e) {
      return new Response(json_encode(array('data' => 'nok' . ' ' . $e->getMessage())), 200, array('Content-Type' => 'application/json'));
    }
  }
  
  public function updateAvatarAction($providerName) {
    $mediaId = $this->getRequest()->get('mediaId');
    $media = $this->get('sonata.media.manager.media')->find($mediaId);
    $this->avatarUser($providerName, $media);
    return new Response(json_encode(array('response' => 'ok')), 200, array('Content-Type' => 'application/json'));
  }

  /*public function deleteAction(Request $request, Application $app, $media_id, $object_id, $object_class) {
    $repository = new MediaRepository($app['db']);

    // Obtenemos la media
    $media = $repository->find($media_id);

    if (!$media) {
      $app->abort(404, 'The requested media was not found.');
    }

    // Ejecutamos el postSave de la media
    $this->postDelete($app, $media, $object_id, $object_class);

    $filename = $media->getName();

    $repository->delete($media);

    $data = array($filename => true);
    return new Response(json_encode($data), 200, array('Content-Type' => 'application/json'));
  }*/
  
  public function getUser() {
    if (null === $token = $this->get('security.context')->getToken()) {
      return null;
    }

    if (!is_object($user = $token->getUser())) {
      return null;
    }

    return $user;
  }

  private function avatarUser($provider, $media) {
    $user = $this->getUser();

    if(!is_null($user)) {
      if($provider == 'image') {
        $user->setAvatar($media);
      } elseif ($provider == 'youtube') {
        $user->setYoutube($media);      
      } elseif ($provider == 'song') {
        $user->setSong($media);        
      }

      $this->get('success.user.manager')->updateUser($user);
      
      return true;
    }

    return false;
  }

}




