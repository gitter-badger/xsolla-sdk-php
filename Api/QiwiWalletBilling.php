<?php
namespace Xsolla\Sdk\Api;

/**
 * @link http://xsolla.com/docs/quick-qiwi
 * @author Vitaliy Zakharov <v.zakharov@xsolla.com>
 */
class QiwiWalletBilling extends MobilePayment
{
    protected $url = 'https://api.xsolla.com/invoicing/index.php?';

    protected function send(array $urlVars, $stringForSignature, $schemaFile)
    {
        $urlVars['ps'] = 'qiwi';
        $stringForSignature .= 'qiwi';

        return parent::send($urlVars, $stringForSignature, $schemaFile);
    }
}
