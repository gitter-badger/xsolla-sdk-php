<?php
namespace Xsolla\Sdk\Tests\Api;

use Xsolla\Sdk\Api\MobilePayment;

/**
 * @author Vitaliy Zakharov <zakharovvi@gmail.com>
 */
class MobilePaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MobilePayment
     */
    private $mobilePayment;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $clientMock;

    const PROJECT = 4783;

    const SECRET_KEY = 'key';

    const SCHEMA_DIR = '/';

    const PHONE = 9630123817;

    public function setUp()
    {
        $this->clientMock = $this->getMock('\Xsolla\Sdk\Api\Client\ClientInterface');
        $this->mobilePayment = new MobilePayment(
            $this->clientMock,
            self::SCHEMA_DIR,
            self::PROJECT,
            self::SECRET_KEY
        );
    }

    /**
     * @expectedException \Xsolla\Sdk\Api\Exception\ErrorCode\MobilePaymentException
     * @expectedExceptionCode 5
     * @expectedExceptionMessage error comment
     */
    public function testCalculateFailWhenXsollaResponseMotSucceed()
    {
        $this->clientMock->expects($this->once())
                ->method('send')
                ->will($this->returnValue(array('result' => 5, 'comment' => 'error comment')));
        $this->mobilePayment->calculateOut(self::PHONE, 1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCalculateOutFailWhenPhoneHasInvalidFormat()
    {
        $this->mobilePayment->calculateOut('123456789', 1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCalculateSumFailWhenSumHasInvalidFormat()
    {
        $this->mobilePayment->calculateSum(self::PHONE, 0);
    }

    public function testCalculateSum()
    {
        $clientResponse = array(
            'sum' => '11.07',
            'out' => '1.5',
            'result' => '0',
            'comment' => 'ok',
        );
        $expectedXsdFileName = self::SCHEMA_DIR.MobilePayment::XSD_PATH_CALCULATE;
        $expectedUrl = 'https://api.xsolla.com/mobile/payment/index.php?command=calculate&project=4783&out=10&phone=9630123817&md5=15ab2801202b594dc0706176616771d2';
        $this->clientMock->expects($this->once())
                ->method('send')
                ->with($expectedUrl, $expectedXsdFileName)
                ->will($this->returnValue($clientResponse));
        $this->assertEquals(
            11.07,
            $this->mobilePayment->calculateSum(self::PHONE, 10)
        );
    }

    public function testCalculateOut()
    {
        $clientResponse = array(
            'sum' => '11.07',
            'out' => '1.5',
            'result' => '0',
            'comment' => 'ok',
        );
        $expectedXsdFileName = self::SCHEMA_DIR.MobilePayment::XSD_PATH_CALCULATE;
        $expectedUrl = 'https://api.xsolla.com/mobile/payment/index.php?command=calculate&project=4783&sum=10&phone=9630123817&md5=15ab2801202b594dc0706176616771d2';
        $this->clientMock->expects($this->once())
                ->method('send')
                ->with($expectedUrl, $expectedXsdFileName)
                ->will($this->returnValue($clientResponse));
        $this->assertEquals(
            1.5,
            $this->mobilePayment->calculateOut(self::PHONE, 10)
        );
    }
}
