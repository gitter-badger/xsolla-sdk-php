<?php
namespace Xsolla\Sdk\Tests\Api;

use Xsolla\Sdk\Api\QiwiWalletBilling;
use Xsolla\Sdk\Api\MobilePayment;

/**
 * @author Vitaliy Zakharov <v.zakharov@xsolla.com>
 */
class QiwiWalletBillingTest extends MobilePaymentTest
{

    protected $calculateSumTestUrl =  'https://api.xsolla.com/invoicing/index.php?command=calculate&project=4783&out=10&phone=9630123817&ps=qiwi&md5=af24afc05e67a343f9c6d7d1cf891556';

    /**
     * $md5 = md5('calculate4783109630123817qiwikey');
     */
    protected $calculateOutTestUrl = 'https://api.xsolla.com/invoicing/index.php?command=calculate&project=4783&sum=10&phone=9630123817&ps=qiwi&md5=af24afc05e67a343f9c6d7d1cf891556';

    /*
     * $md5 = md5('invoice4783demo543.89999120000000192.33.19.70mail@example.comqiwikey');
     */
    protected $invoiceTestUrl = 'https://api.xsolla.com/invoicing/index.php?command=invoice&project=4783&v1=demo&v2=demo-v2&v3=demo-v3&out=543.8999&phone=9120000000&userip=192.33.19.70&email=mail%40example.com&ps=qiwi&md5=a6e40216b44f737febd8c78cbc3ee27c';

    public function setUp()
    {
        $this->clientMock = $this->getMock('\Xsolla\Sdk\Api\Client\ClientInterface');
        $this->mobilePayment = new QiwiWalletBilling(
            $this->clientMock,
            self::PROJECT,
            self::SECRET_KEY,
            self::SCHEMA_DIR
        );
    }
}
