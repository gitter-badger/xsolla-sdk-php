<?php
namespace Xsolla\Sdk\Api\Exception\ErrorCode;

/**
 * @link http://xsolla.com/docs/mobile-payment-api/response-codes
 * @author Vitaliy Zakharov <zakharovvi@gmail.com>
 */
class MobilePaymentException extends \RuntimeException implements ErrorCodeExceptionInterface
{
    private $codes = array(
        1 => 'Temporary technical error occurred',
        2 => 'Wrong phone number',
        3 => 'Wrong md5 security signature',
        4 => 'A required parameter is missing or invalid command',
        5 => 'Operator is not supported',
        6 => 'User not found',
        7 => 'The amount of payment is too big, or the user exceeded daily transactions limit'
    );

    public function __construct($message, $code, \Exception $previous = null)
    {
        if (!isset($this->codes[$code])) {
            throw new \InvalidArgumentException("Unknown error code - $code");
        }
        parent::__construct($message, $code, $previous);
    }

    public function getCodeDescription()
    {
        return$this->codes[$this->code];
    }
}
