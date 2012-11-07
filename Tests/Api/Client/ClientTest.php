<?php
namespace Xsolla\Sdk\Tests\Api\Client;

use Xsolla\Sdk\Api\Client\Client;

/**
 * @author Vitaliy Zakharov <v.zakharov@xsolla.com>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
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

    private $schemaFileName;

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
        $this->client = new Client;
        $this->schemaFileName = __DIR__.'/../../../Resources/schema/api/mobilepayment/calculate.xsd';
    }

    public function tearDown()
    {
        if (file_exists($this->xmlFile)) {
            unlink($this->xmlFile);
        }
        rmdir($this->workspace);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSendFailWhenSchemaIsNotReadable()
    {
        $this->client->send('nonexistenturl','notreadableschma');
    }

    /**
     * @expectedException \Xsolla\Sdk\Api\Exception\InvalidResponseException
     */
    public function testSendFailWhenDomCantBeCreated()
    {
        $this->client->send('nonexistenturl', $this->schemaFileName);
    }

    /**
     * @expectedException \Xsolla\Sdk\Api\Exception\InvalidResponseException
     */
    public function testSendFailWhenXsollaResponseIsNotValidAgainstXsd()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <response>
                    <sum>10</sum>
                    <out>1</out>
                    <result>8</result>
                    <comment>OK</comment>
                </response>';
        file_put_contents($this->xmlFile, $xml);
        $this->client->send($this->xmlFile, $this->schemaFileName);
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
        $this->assertEquals($xmlAsArray, $this->client->send($this->xmlFile, $this->schemaFileName));
    }
}
