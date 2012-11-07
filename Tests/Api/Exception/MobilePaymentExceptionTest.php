<?php
namespace Xsolla\Sdk\Tests\Api\Exception\ErrorCode;

use Xsolla\Sdk\Api\Exception\ErrorCode\MobilePaymentException;

/**
 * @author Vitaliy Zakharov <v.zakharov@xsolla.com>
 */
class MobilePaymentExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorFailWhenErrorCodeIsWrong()
    {
        new MobilePaymentException('message', 125);
    }

    public function testGetCodeDescription()
    {
        $mpe = new MobilePaymentException('message', 4);
        $this->assertEquals('A required parameter is missing or invalid command', $mpe->getCodeDescription());
    }
}
