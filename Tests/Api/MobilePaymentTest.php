<?php
namespace Xsolla\Sdk\Tests\Api;

use Xsolla\Sdk\Api\MobilePayment;

/**
 * @author Vitaliy Zakharov <v.zakharov@xsolla.com>
 */
class MobilePaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MobilePayment
     */
    protected $mobilePayment;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $clientMock;

    const PROJECT = 4783;

    const SECRET_KEY = 'key';

    const SCHEMA_DIR = '/';

    const PHONE = 9630123817;

    protected $calculateSumTestUrl =  'https://api.xsolla.com/mobile/payment/index.php?command=calculate&project=4783&out=10&phone=9630123817&md5=15ab2801202b594dc0706176616771d2';

    /**
     * $md5 = md5('calculate4783109630123817key');
     */
    protected $calculateOutTestUrl = 'https://api.xsolla.com/mobile/payment/index.php?command=calculate&project=4783&sum=10&phone=9630123817&md5=15ab2801202b594dc0706176616771d2';

    /*
     * $md5 = md5('invoice4783demo543.89999120000000192.33.19.70mail@example.comkey');
     */
    protected $invoiceTestUrl = 'https://api.xsolla.com/mobile/payment/index.php?command=invoice&project=4783&v1=demo&v2=demo-v2&v3=demo-v3&out=543.8999&phone=9120000000&userip=192.33.19.70&email=mail%40example.com&md5=3ee70d833166f544674d67767969a84e';

    private $calculateResponse = array(
        'sum' => '11.07',
        'out' => '1.5',
        'result' => '0',
        'comment' => 'ok',
    );

    public function setUp()
    {
        $this->clientMock = $this->getMock('\Xsolla\Sdk\Api\Client\ClientInterface');
        $this->mobilePayment = new MobilePayment(
            $this->clientMock,
            self::PROJECT,
            self::SECRET_KEY,
            self::SCHEMA_DIR
        );
    }

    public function testSchemaFilePathWhenSchemaDirIsNotPassed()
    {
        $mobilePayment = new MobilePayment(
            $this->clientMock,
            self::PROJECT,
            self::SECRET_KEY
        );
        $assertSchemaFilePathEquals = function($actual){
            return realpath($actual) === realpath( __DIR__.'/../../Resources/schema/api/mobilepayment/calculate.xsd');
        };
        $this->clientMock->expects($this->once())
            ->method('send')
            ->with(
                $this->anything(),
                new \PHPUnit_Framework_Constraint_Callback($assertSchemaFilePathEquals)
            )
            ->will($this->returnValue($this->calculateResponse));;
        $mobilePayment->calculateSum(self::PHONE, 150);
    }

    /**
     * @expectedException \Xsolla\Sdk\Api\Exception\ErrorCode\MobilePaymentException
     * @expectedExceptionCode 5
     * @expectedExceptionMessage error comment
     */
    public function testCalculateFailWhenXsollaResponseNotSucceed()
    {
        $this->clientMock->expects($this->once())
                ->method('send')
                ->will($this->returnValue(array('result' => 5, 'comment' => 'error comment')));
        $this->mobilePayment->calculateOut(self::PHONE, 1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCalculateFailWhenPhoneHasInvalidFormat()
    {
        $this->clientMock->expects($this->never())
            ->method('send');
        $this->mobilePayment->calculateOut('123456789', 1);
    }

    /**
     * @dataProvider InvalidOutProvider
     * @expectedException \InvalidArgumentException
     */
    public function testCalculateSumFailWhenOutHasInvalidFormat($invalidOut)
    {
        $this->clientMock->expects($this->never())
            ->method('send');
        $this->mobilePayment->calculateSum(self::PHONE, $invalidOut);
    }

    public function InvalidOutProvider()
    {
        return array(
            array(0),
            array(array(1)),
            array(''),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCalculateOutFailWhenSumHasInvalidFormat()
    {
        $this->clientMock->expects($this->never())
            ->method('send');
        $this->mobilePayment->calculateOut(self::PHONE, 12.345);
    }

    public function testCalculateSum()
    {
        $expectedXsdFileName = self::SCHEMA_DIR.MobilePayment::XSD_PATH_CALCULATE;
        $this->clientMock->expects($this->once())
                ->method('send')
                ->with($this->calculateSumTestUrl, $expectedXsdFileName)
                ->will($this->returnValue($this->calculateResponse));
        $this->assertEquals(
            11.07,
            $this->mobilePayment->calculateSum(self::PHONE, 10)
        );
    }

    public function testCalculateOut()
    {
        $expectedXsdFileName = self::SCHEMA_DIR.MobilePayment::XSD_PATH_CALCULATE;
        $this->clientMock->expects($this->once())
                ->method('send')
                ->with($this->calculateOutTestUrl, $expectedXsdFileName)
                ->will($this->returnValue($this->calculateResponse));
        $this->assertEquals(
            1.5,
            $this->mobilePayment->calculateOut(self::PHONE, 10)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvoiceFailWhenPhoneIsNotValid()
    {
        $this->clientMock->expects($this->never())
            ->method('send');
        $this->mobilePayment->invoice(123456789, 'demo', null, null, 11);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvoiceFailWhenEmailIsNotValid()
    {
        $this->clientMock->expects($this->never())
            ->method('send');
        $this->mobilePayment->invoice(self::PHONE, 'demo', null, null, 11, null, 'invalidEmail');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvoiceFailWhenUserIpIsNotValid()
    {
        $this->clientMock->expects($this->never())
            ->method('send');
        $this->mobilePayment->invoice(self::PHONE, 'demo', null, null, 11, null, 'example@example.com', '127.0.0.1');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvoiceFailWhenSumAndOutPassed()
    {
        $this->clientMock->expects($this->never())
            ->method('send');
        $this->mobilePayment->invoice(self::PHONE, 'demo', null, null, 11, 11);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvoiceFailWhenSumAndOutNotPassed()
    {
        $this->clientMock->expects($this->never())
            ->method('send');
        $this->mobilePayment->invoice(self::PHONE, 'demo');
    }

    /**
     * @expectedException \Xsolla\Sdk\Api\Exception\ErrorCode\MobilePaymentException
     * @expectedExceptionCode 3
     * @expectedExceptionMessage error comment 3
     */
    public function testInvoiceFailWhenXsollaResponseNotSucceed()
    {
        $expectedXsdFileName = self::SCHEMA_DIR.MobilePayment::XSD_PATH_INVOICE;
        $this->clientMock->expects($this->once())
            ->method('send')
            ->with(
               $this->anything(),
               $expectedXsdFileName
            )
            ->will($this->returnValue(array('result' => '3', 'comment' => 'error comment 3')));
        $this->mobilePayment->invoice(9120000000, 'demo', null, null, 12.45);
    }

    /**
     * @expectedException \Xsolla\Sdk\Api\Exception\InvalidResponseException
     * @expectedExceptionMessage Xsolla response doesn't contain invoice number.
     */
    public function testInvoiceFailWhenRepsonseCodeIsSucceedButInvoiceUndefined()
    {
        $this->clientMock->expects($this->once())
            ->method('send')
            ->will($this->returnValue(array('result' => '0', 'comment' => 'Not ok')));
        $this->mobilePayment->invoice(9120000000, 'demo', null, null, 12.45);
    }

    public function testInvoice()
    {
        $expectedXsdFileName = self::SCHEMA_DIR.MobilePayment::XSD_PATH_INVOICE;
        $this->clientMock->expects($this->once())
            ->method('send')
            ->with(
               $this->invoiceTestUrl,
               $expectedXsdFileName
            )
            ->will($this->returnValue(array(
                'result'  => '0',
                'comment' => 'OK',
                'invoice' => '141235145',
            )));
        $this->assertEquals(
            '141235145',
            $this->mobilePayment->invoice(9120000000, 'demo', 'demo-v2', 'demo-v3', null, 543.8999, 'mail@example.com', '192.33.19.70')
        );
    }
}
