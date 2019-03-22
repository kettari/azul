<?php
declare(strict_types=1);

namespace AppBundle\Endpoint;


use AppBundle\UrlShortener;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ShortcutCreateEndpoint extends AbstractSecuredApiEndpoint implements PostMethodInterface {

  /**
   * @var UrlShortener
   */
  private $shortener;

  /**
   * @var array
   */
  private $inputData;

  /**
   * ShortcutEndpoint constructor.
   *
   * @param LoggerInterface $logger
   * @param RegistryInterface $doctrine
   * @param \AppBundle\UrlShortener $shortener
   */
  public function __construct(LoggerInterface $logger, RegistryInterface $doctrine, UrlShortener $shortener) {
    parent::__construct($logger, $doctrine);
    $this->shortener = $shortener;
  }

  /**
   * Validate incoming JSON against scheme
   *
   * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   * @return AbstractEndpoint
   */
  function validate() {
    $json = $this->scrapIncomingData();

    // Check whole object is array
    if (!is_array($json)) {
      throw new BadRequestHttpException('JSON expected to be array of items');
    }
    // At least 1 item required
    if (!count($json)) {
      throw new BadRequestHttpException('JSON array expected to have at least 1 item');
    }
    // Validate each item
    foreach ($json as $key => $item) {
      // Field 'url' required
      if (!isset($item['url'])) {
        throw new BadRequestHttpException(sprintf('Item "%s" has no mandatory field "url"',
          $key));
      }
      // Field 'url' should be non-empty
      if (empty($item['url'])) {
        throw new BadRequestHttpException(sprintf('Item "%s" has empty field "url"',
          $key));
      }
      // Field 'url' should be valid URL
      /**
       * For URL regex check website by Mathias:
       *
       * @link https://mathiasbynens.be/demo/url-regex
       */
      if (!preg_match($this->getUrlRegex(), $item['url'])) {
        throw new BadRequestHttpException(sprintf('Item "%s" has invalid value in the field "url" = "%s"',
          $key, $item['url']));
      }
      // Field 'type' should be 'permanent' or 'temporary'
      if (isset($item['type']) &&
        ('permanent' != $item['type'] || 'temporary' != $item['type'])) {
        throw new BadRequestHttpException(sprintf('Item "%s" has invalid value in the field "type" = "%s"',
          $key, $item['type']));
      }
      // Field 'expirationDate' should be date or date-time
      if (isset($item['expirationDate']) &&
        !(preg_match('/^([0-9]+)-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/',
            $item['expirationDate']) ||
          preg_match('/^([0-9]+)-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])[Tt]([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9]|60)(\.[0-9]+)?(([Zz])|([\+|\-]([01][0-9]|2[0-3]):[0-5][0-9]))$/',
            $item['expirationDate']))) {
        throw new BadRequestHttpException(sprintf('Item "%s" has invalid value in the field "expirationDate" = "%s"',
          $key, $item['expirationDate']));
      }
      // Field 'shortcut' should match pattern
      if (isset($item['shortcut']) &&
        !preg_match('/^[0-9a-zA-Z-_]{3,100}$/', $item['shortcut'])) {
        throw new BadRequestHttpException(sprintf('Item "%s" has invalid value in the field "shortcut" = "%s"',
          $key, $item['shortcut']));
      }
      $this->getLogger()
        ->info('Collection item {item_index} successfully validated',
          array_merge(['item_index' => $key], $item));
    }
    $this->inputData = $json;

    return $this;
  }

  /**
   * Returns regex to validate URL
   * @return string
   */
  private function getUrlRegex() {
    return '%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu';
  }

  /**
   * Scrap incoming data into array.
   *
   * @return array
   */
  private function scrapIncomingData() {
    $updateData = json_decode(file_get_contents('php://input'), TRUE);
    if (JSON_ERROR_NONE != json_last_error()) {
      throw new \InvalidArgumentException('JSON error: '.json_last_error_msg());
    }

    return $updateData;
  }

  /**
   * Handle request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  function handle() {
    $result = [];
    foreach ($this->inputData as $key => $urlItem) {
      if (isset($urlItem['expirationDate']) &&
        !empty($urlItem['expirationDate'])) {
        $expirationDate = $this->createExpirationDate($urlItem['expirationDate']);
      } else {
        $expirationDate = NULL;
      }
      if (isset($urlItem['shortcut']) && !empty($urlItem['shortcut'])) {
        $shortcut = $urlItem['shortcut'];
      } else {
        $shortcut = NULL;
      }
      $this->getLogger()
        ->info('Shortening item index {item_index}',
          array_merge(['item_index' => $key], $urlItem));
      $link = $this->shortener->shorten($urlItem['url'], $this->getOwner(),
        $expirationDate, $shortcut);
      $shortenedItem = [
        'url'            => $urlItem['url'],
        'type'           => $link->getType(),
        'expirationDate' => is_null($link->getDateExpires()) ? NULL : $link->getDateExpires()
          ->format('Y-m-d\TH:i:sP'),
        'shortcut'       => $link->getShortcut(),
        'shortcutUrl'    => $this->getRequest()
            ->getSchemeAndHttpHost().'/'.$link->getShortcut(),
      ];
      $this->getLogger()
        ->info('Successfully shortened item index {item_index}',
          array_merge(['item_index' => $key], $shortenedItem));
      $result[] = $shortenedItem;
    }

    return new JsonResponse($result, JsonResponse::HTTP_CREATED);
  }

  /**
   * @param $stringDate
   * @return \DateTime
   */
  private function createExpirationDate($stringDate) {
    if (preg_match('/^([0-9]+)-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/',
      $stringDate)) {
      return \DateTime::createFromFormat('Y-m-d', $stringDate);
    } elseif (preg_match('/^([0-9]+)-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])[Tt]([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9]|60)(\.[0-9]+)?(([Zz])|([\+|\-]([01][0-9]|2[0-3]):[0-5][0-9]))$/',
      $stringDate, $matches)) {
      return \DateTime::createFromFormat('Y-m-d\TH:i:sP', $stringDate);
    } else {
      throw new \InvalidArgumentException('Invalid expiration date format: '.
        $stringDate);
    }
  }


}