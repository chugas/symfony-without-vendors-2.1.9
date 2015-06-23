<?php

namespace Application\Success\CoreBundle\Twig;

use Symfony\Component\Translation\TranslatorInterface;

class CoreExtension extends \Twig_Extension {

  private $sonata_media_pool;
  private $image_provider;
  private $translator;
  private $repo_user;

  public function __construct($image_provider, $sonata_media_pool, $repo_user, TranslatorInterface $translator = null) {
    //$this->container = $container;
    $this->image_provider = $image_provider;
    $this->sonata_media_pool = $sonata_media_pool;
    $this->translator = $translator;
    $this->repo_user = $repo_user;
  }

  public function getTranslator() {
    return $this->translator;
  }

  public function getName() {
    return 'success_core';
  }

  public function getFilters() {
    return array(
        'fecha' => new \Twig_Filter_Method($this, 'fecha'),
        'truncate_words' => new \Twig_Filter_Method($this, 'truncate_words'),
        'created_ago' => new \Twig_Filter_Method($this, 'createdAgo'),
        'bb_parse' => new \Twig_Filter_Method($this, 'bb_parse'),
        'iframe_src' => new \Twig_Filter_Method($this, 'iframe_src'),
        'age' => new \Twig_Filter_Method($this, 'age'),
    );
  }

  public function getFunctions() {
    return array(
        'web_path' => new \Twig_Function_Method($this, 'path'),
        'web_basic_path' => new \Twig_Function_Method($this, 'basic_path'),
        'web_local_path' => new \Twig_Function_Method($this, 'local_path')
    );
  }

  function iframe_src($iframe_code) {
    preg_match('/src="([^"]+)"/', $iframe_code, $match);
    return $match[1];
  }
  
  function bb_parse($string) {
    $string = strip_tags($string);
    $tags = 'b|i|size|color|center|quote|url|img|user|video';
    while (preg_match_all('`\[(' . $tags . ')=?(.*?)\](.+?)\[/\1\]`', $string, $matches))
      foreach ($matches[0] as $key => $match) {
        list($tag, $param, $innertext) = array($matches[1][$key], $matches[2][$key], $matches[3][$key]);
        switch ($tag) {
          case 'b': $replacement = "<strong>$innertext</strong>";
            break;
          case 'i': $replacement = "<em>$innertext</em>";
            break;
          case 'size': $replacement = "<span style=\"font-size: $param;\">$innertext</span>";
            break;
          case 'color': $replacement = "<span style=\"color: $param;\">$innertext</span>";
            break;
          case 'center': $replacement = "<div class=\"centered\">$innertext</div>";
            break;
          case 'quote': $replacement = "<blockquote>$innertext</blockquote>" . $param ? "<cite>$param</cite>" : '';
            break;
          case 'url': $replacement = '<a target="_blank" href="' . ($param ? $param : $innertext) . "\">$innertext</a>";
            break;
          case 'user': 
            $user = $this->repo_user->findMinData($innertext);
            $replacement = '<span class="nombre"><a href="/profile">'.$user['name'].'</a></span>';
            break;
          case 'img':
            //list($width, $height) = preg_split('[Xx]', $param);
            $width = false;
            $height = false;
            $replacement = "<img width='420' src=\"$innertext\" " . (is_numeric($width) ? "width=\"$width\" " : '') . (is_numeric($height) ? "height=\"$height\" " : '') . '/>';
            break;
          case 'video':
            $videourl = parse_url($innertext);
            parse_str($videourl['query'], $videoquery);
            if (strpos($videourl['host'], 'youtube.com') !== FALSE)
              $replacement = '<embed src="http://www.youtube.com/v/' . $videoquery['v'] . '" type="application/x-shockwave-flash" width="425" height="344"></embed>';
            if (strpos($videourl['host'], 'google.com') !== FALSE)
              $replacement = '<embed src="http://video.google.com/googleplayer.swf?docid=' . $videoquery['docid'] . '" width="400" height="326" type="application/x-shockwave-flash"></embed>';
            break;
        }
        $string = str_replace($match, $replacement, $string);
      }
    return $string;
  }

  public function path($media, $format) {
    $mediaservice = $this->sonata_media_pool;

    $provider = $mediaservice->getProvider($media->getProviderName());

    $format = $provider->getFormatName($media, $format);

    return $provider->generatePublicUrl($media, $format);
  }

  public function basic_path($id, $reference, $context) {
    $provider = $this->image_provider;
    return $provider->generatePublicUrl($id, $reference, $context);
  }

  public function local_path($reference, $context) {
    return '/' . $context . '/' . $reference;
  }

  function truncate_words($string, $words = 20) {
    $text = explode(' ', $string);
    if ($words > count($text)) {
      return $string;
    }
    return preg_replace('/((\w+\W*){' . ($words - 1) . '}(\w+))(.*)/', '${1}', $string) . '...';
  }

  public function createdAgo(\DateTime $dateTime) {
    $delta = time() - $dateTime->getTimestamp();
    if ($delta < 0)
      throw new \Exception("createdAgo is unable to handle dates in the future");

    $duration = "";
    if ($delta < 60) {
      // Segundos
      $time = $delta;
      $duration = $time . " second" . (($time > 1) ? "s" : "") . " ago";
    } else if ($delta <= 3600) {
      // Minutos
      $time = floor($delta / 60);
      $duration = $time . " minute" . (($time > 1) ? "s" : "") . " ago";
    } else if ($delta <= 86400) {
      // Horas
      $time = floor($delta / 3600);
      $duration = $time . " hour" . (($time > 1) ? "s" : "") . " ago";
    } else {
      // Días
      $time = floor($delta / 86400);
      $duration = $time . " day" . (($time > 1) ? "s" : "") . " ago";
    }

    return $duration;
  }

  /**
   * Formatea la fecha indicada según las características del locale seleccionado.
   * Se utiliza para mostrar correctamente las fechas en el idioma de cada usuario.
   *
   * @param string $fecha        Objeto que representa la fecha original
   * @param string $formatoFecha Formato con el que se muestra la fecha
   * @param string $formatoHora  Formato con el que se muestra la hora
   * @param string $locale       El locale al que se traduce la fecha
   */
  public function fecha($fecha, $formatoFecha = 'medium', $formatoHora = 'none', $locale = null) {
    // Código copiado de
    //   https://github.com/thaberkern/symfony/blob
    //   /b679a23c331471961d9b00eb4d44f196351067c8
    //   /src/Symfony/Bridge/Twig/Extension/TranslationExtension.php
    // Formatos: http://www.php.net/manual/en/class.intldateformatter.php#intl.intldateformatter-constants
    $formatos = array(
        // Fecha/Hora: (no se muestra nada)
        'none' => \IntlDateFormatter::NONE,
        // Fecha: 12/13/52  Hora: 3:30pm
        'short' => \IntlDateFormatter::SHORT,
        // Fecha: Jan 12, 1952  Hora:
        'medium' => \IntlDateFormatter::MEDIUM,
        // Fecha: January 12, 1952  Hora: 3:30:32pm
        'long' => \IntlDateFormatter::LONG,
        // Fecha: Tuesday, April 12, 1952 AD  Hora: 3:30:42pm PST
        'full' => \IntlDateFormatter::FULL,
    );

    $formateador = \IntlDateFormatter::create(
                    $locale != null ? $locale : $this->getTranslator()->getLocale(), $formatos[$formatoFecha], $formatos[$formatoHora]
    );

    if ($fecha instanceof \DateTime) {
      return $formateador->format($fecha);
    } else {
      return $formateador->format(new \DateTime($fecha));
    }
  }
  
  public function age($date) {

    if (!($date instanceof \DateTime)) {
      return '';
    }

    $now = new \DateTime();

    $diff = $now->diff($date);

    return $diff->y;
  }  

}

