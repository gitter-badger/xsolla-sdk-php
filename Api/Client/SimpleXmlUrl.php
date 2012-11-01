<?php
namespace Xsolla\Api\Client;

use Xsolla\Api\Exception\InvalidResponseException;
use Xsolla\Api\Exception\NonSucceedResultCodeException;
/**
 * @author Vitaliy Zakharov <v.zakharov@xsolla.com>
 */
class SimpleXmlUrl implements ClientInterface
{

    /**
     * {@inheritDoc}
     */
    public function send($url)
    {
        libxml_use_internal_errors(true);
        try {
            $xmlResponse = new \SimpleXMLElement($url, 0, true);
        } catch (\Exception $e) {
            throw new InvalidResponseException($e->getMessage(), $e->getCode());
        }
        //http://www.bookofzeus.com/articles/convert-simplexml-object-into-php-array/
        $arrayResponse = json_decode(json_encode($xmlResponse), true);
        if (!isset($arrayResponse['result']) OR !is_string($arrayResponse['result'])) {
            throw new InvalidResponseException('Invalid response from Xsolla. Result code undefined');
        }
        if ('0' !== $arrayResponse['result']) {
            throw new NonSucceedResultCodeException($arrayResponse['comment'], $arrayResponse['result']);
        }
        return $arrayResponse;
    }
}
