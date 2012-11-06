<?php
namespace Xsolla\Sdk\Api;

use Xsolla\Sdk\Api\Client\ClientInterface;
use Xsolla\Sdk\Api\Exception\ErrorCode\MobilePaymentException;
use Xsolla\Sdk\Api\Exception\ErrorCode\ErrorCodeExceptionInterface;

/**
 * @link http://xsolla.com/docs/mobile-payment-api
 * @author Vitaliy Zakharov <v.zakharov@xsolla.com>
 */
class MobilePayment
{
    const URL = 'https://api.xsolla.com/mobile/payment/index.php?';

    const XSD_PATH_CALCULATE = '/mobilepayment/calculate.xsd';

    const XSD_PATH_INVOICE = '/mobilepayment/invoice.xsd';

    /**
     * @var string
     */
    private $projectId;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $schemaDir;

    /**
     * @param  ClientInterface           $client
     * @param  int                       $projectId
     * @param  string                    $secretKey
     * @throws \InvalidArgumentException
     */
    public function __construct(ClientInterface $client, $schemaDir, $projectId, $secretKey)
    {
        if (!is_dir($schemaDir)) {
            throw new \InvalidArgumentException("$schemaDir is not a dir.");
        }
        if (!filter_var($projectId, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException("$projectId is not a int.");
        }
        if (!is_scalar($secretKey)) {
            throw new \InvalidArgumentException("$secretKey is not a string.");
        }
        $this->schemaDir = $schemaDir;
        $this->client = $client;
        $this->projectId = $projectId;
        $this->secretKey = $secretKey;
    }

    public function invoice()
    {

    }

    /**
     * @param  int                         $phone
     * @param  float                       $out
     * @return float
     * @throws \InvalidArgumentException   If phone or sum has invalid format
     * @throws InvalidResponseException    If network problem or response has invalid format.
     * @throws ErrorCodeExceptionInterface If xsolla response is not succeed.
     */
    public function calculateSum($phone, $out)
    {
        if (!$this->isValidPhoneFormat($phone)) {
            throw new \InvalidArgumentException("$phone - phone number must contain only digits and have a length of 10 characters");
        }
        if (!$this->isValidSumFormat($out)) {
            throw new \InvalidArgumentException("$out - out is not a float");
        }

        return $this->calculate($phone, $out, 'out');
    }

    /**
     * @param  int                         $phone
     * @param  float                       $sum
     * @return float
     * @throws \InvalidArgumentException   If phone or sum has invalid format
     * @throws InvalidResponseException    If network problem or response has invalid format.
     * @throws ErrorCodeExceptionInterface If xsolla response is not succeed.
     */
    public function calculateOut($phone, $sum)
    {
        if (!$this->isValidPhoneFormat($phone, true)) {
            throw new \InvalidArgumentException("$phone - phone number must contain only digits and have a length of 10 characters");
        }
        if (!$this->isValidSumFormat($sum)) {
            throw new \InvalidArgumentException("$sum - sum is not a float");
        }

        return $this->calculate($phone, $sum, 'sum');
    }

    private function calculate($phone, $number, $numberType)
    {
        $urlVars = array('command' => 'calculate', 'project' => $this->projectId);
        $stringForSignature = 'calculate'.$this->projectId;
        $urlVars[$numberType] = $number;
        $stringForSignature .= $number;
        $stringForSignature .= $phone;
        $urlVars['phone'] = $phone;
        $urlVars['md5'] = md5($stringForSignature.$this->secretKey);
        $url = self::URL.http_build_query($urlVars);
        $xsdFileName = $this->schemaDir.self::XSD_PATH_CALCULATE;
        $xsollaResponse = $this->client->send($url, $xsdFileName);
        if ('0' !== $xsollaResponse['result']) {
            throw new MobilePaymentException($xsollaResponse['comment'], $xsollaResponse['result']);
        }
        if ('sum' === $numberType) {
            return (float) $xsollaResponse['out'];
        }

        return (float) $xsollaResponse['sum'];
    }

    private function isValidSumFormat($sum, $isMoney = false)
    {
        if ($isMoney) {
            $pattern = '~^\d+(\.\d{1,2})?$~';
        } else {
            $pattern = '~^\d+(\.\d{1,})?$~';
        }

        return preg_match($pattern, $sum) AND (0 < $sum);
    }

    private function isValidPhoneFormat($phone)
    {
        return ctype_digit((string) $phone) AND (10 === strlen($phone));
    }

}
