<?php
namespace Xsolla\Sdk\Api\Exception\ErrorCode;

/**
 * @author Vitaliy Zakharov <v.zakharov@xsolla.com>
 */
interface ErrorCodeExceptionInterface
{
    /**
     * @return string
     */
    public function getCodeDescription();
}
