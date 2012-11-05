<?php
namespace Xsolla\Sdk\Api\Client;

use Xsolla\Sdk\Api\Exception\InvalidResponseException;

/**
 * @author Vitaliy Zakharov <v.zakharov@xsolla.com>
 */
class Client implements ClientInterface
{
    /**
     * @var array
     */
    private $errors = array();

    /**
     * {@inheritDoc}
     */
    public function send($url, $schemaFilename)
    {
        if (!is_readable($schemaFilename)) {
            throw new \InvalidArgumentException("Schema file: $schemaFilename is not readable.");
        }
        set_error_handler(array($this,'addError'));
        $dom = $this->loadDocument($url);
        if (false === $dom->schemaValidate($schemaFilename)) {
            $this->throwInvalidResponseException();
        }
        restore_error_handler();
        //http://www.bookofzeus.com/articles/convert-simplexml-object-into-php-array/
        return json_decode(json_encode(simplexml_import_dom($dom)), true);
    }

    /**
     * @param string $url
     * @return \DOMDocument
     * @throws InvalidResponseException If http errors or xml is not valid.
     */
    private function loadDocument($url)
    {
        $dom = new \DOMDocument;
        if (false === $dom->load($url)) {
            $this->throwInvalidResponseException();
        }
        return $dom;
    }

    /**
     * @param int    $type
     * @param string $message
     */
    public function addError($type, $message)
    {
        $this->errors[] = $message;
    }

    private function throwInvalidResponseException()
    {
        $exceptionMessage = implode(PHP_EOL, $this->errors);
        $this->errors = array();
        restore_error_handler();
        throw new InvalidResponseException($exceptionMessage);
    }

}
