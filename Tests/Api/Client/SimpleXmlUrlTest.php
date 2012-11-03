<?php
namespace Xsolla\Tests\Api\Client;

use Xsolla\Api\Client\SimpleXmlUrl;
/**
 * @author Vitaliy Zakharov <zakharovvi@gmail.com>
 */
class SimpleXmlUrlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    private $workspace;

    /**
     * @var string
     */
    private $xmlFile;

    /**
     * @var SimpleXmlUrl
     */
    private $client;

    public function setUp()
    {
        $this->workspace =
            sys_get_temp_dir().
                DIRECTORY_SEPARATOR.
                'xsolla-php-sdk-tests'.
                DIRECTORY_SEPARATOR.
                time().
                rand(0, 1000);
        mkdir($this->workspace, 0777, true);
        $this->xmlFile = $this->workspace.DIRECTORY_SEPARATOR.'test.xml';
        $this->client = new SimpleXmlUrl;
    }

    public function tearDown()
    {
        if (file_exists($this->xmlFile)) {
            unlink($this->xmlFile);
        }
        rmdir($this->workspace);
    }

    /**
     * @expectedException \Xsolla\Api\Exception\InvalidResponseException
     */
    public function testSendFailOnSimpleXmlException()
    {
        $this->client->send('nonexistenturl');
    }

    /**
     * @expectedException \Xsolla\Api\Exception\InvalidResponseException
     */
    public function testSendFailOnUndefinedResultCode()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <response>
                    <comment>OK</comment>
                </response>';
        file_put_contents($this->xmlFile, $xml);
        $this->client->send($this->xmlFile);
    }

    /**
     * @expectedException \Xsolla\Api\Exception\NonSucceedResultCodeException
     * @expectedExceptionCode 10
     * @expectedExceptionMessage Error
     */
    public function testSendFailOnNonSucceedResultCode()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <response>
                    <sum>10</sum>
                    <out>1</out>
                    <result>10</result>
                    <comment>Error</comment>
                </response>';
        file_put_contents($this->xmlFile, $xml);
        $this->client->send($this->xmlFile);
    }

    public function testSend()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <response>
                    <sum>10</sum>
                    <out>1</out>
                    <result>0</result>
                    <comment>OK</comment>
                </response>';
        file_put_contents($this->xmlFile, $xml);
        $xmlAsArray = array(
            'sum' => '10',
            'out' => '1',
            'result' => '0',
            'comment' => 'OK',
        );
        $this->assertEquals($xmlAsArray, $this->client->send($this->xmlFile));
    }
}
