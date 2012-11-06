<?php
namespace Xsolla\Sdk\Api\Client;

/**
 * @author Vitaliy Zakharov <v.zakharov@xsolla.com>
 */
interface ClientInterface
{
    /**
     * send request to Xsolla API
     * @param  string                    $url
     * @param  string                    $schemaFilename
     * @return array
     * @throws InvalidResponseException  If network problem or response has invalid format.
     * @throws \InvalidArgumentException If schema file is not readable.
     */
    public function send($url, $schemaFilename);
}
