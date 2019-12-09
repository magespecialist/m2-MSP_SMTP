<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\SMTP\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Config
{
    private const XML_PATH_SMTP_HOST = 'system/msp_smtp/host';
    private const XML_PATH_SMTP_PORT = 'system/msp_smtp/port';
    private const XML_PATH_SMTP_AUTH = 'system/msp_smtp/auth';
    private const XML_PATH_SMTP_USERNAME = 'system/msp_smtp/username';
    private const XML_PATH_SMTP_PASSWORD = 'system/msp_smtp/password';
    private const XML_PATH_SMTP_SECURE = 'system/msp_smtp/secure';
    private const XML_PATH_DEBUG_MODE = 'system/msp_smtp/debug';

    protected $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    public function __construct(
        ScopeConfigInterface $scopeConfigInterface,
        EncryptorInterface $encryptor
    ) {

        $this->scopeConfig = $scopeConfigInterface;
        $this->encryptor = $encryptor;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->scopeConfig->getValue(static::XML_PATH_SMTP_HOST);
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return (int)$this->scopeConfig->getValue(static::XML_PATH_SMTP_PORT);
    }

    /**
     * @return string
     */
    public function getAuthType(): string
    {
        return $this->scopeConfig->getValue(static::XML_PATH_SMTP_AUTH);
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->scopeConfig->getValue(static::XML_PATH_SMTP_USERNAME);
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        $cryptedValue = $this->scopeConfig->getValue(static::XML_PATH_SMTP_PASSWORD);
        return $this->encryptor->decrypt($cryptedValue);
    }

    /**
     * @return string
     */
    public function getSecure(): string
    {
        return $this->scopeConfig->getValue(static::XML_PATH_SMTP_SECURE) ?: '';
    }

    /**
     * @return bool
     */
    public function getDebugMode(): bool
    {
        return $this->scopeConfig->isSetFlag(static::XML_PATH_DEBUG_MODE);
    }
}
