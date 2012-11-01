<?php
namespace Xsolla\Api\Client;

/**
 * @author Vitaliy Zakharov <v.zakharov@xsolla.com>
 */
interface ClientInterface
{
    /**
     * send request to Xsolla API
     * @param string $url
     * @return array
     * @throws InvalidResponseException If network problem or response has invalid format.
     * @throws ErrorResponseException   If response status is not succeed.
     */
    public function send($url);
}
