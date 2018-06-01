<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_SMTP
 * @copyright  Copyright (c) 2018 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\SMTP\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{


    const XML_PATH_SMTP_HOST = 'system/msp_smtp/host';
    const XML_PATH_SMTP_PORT = 'system/msp_smtp/port';
    const XML_PATH_SMTP_AUTH = 'system/msp_smtp/auth';
    const XML_PATH_SMTP_USERNAME = 'system/msp_smtp/username';
    const XML_PATH_SMTP_PASSWORD = 'system/msp_smtp/password';
    const XML_PATH_SMTP_SSL = 'system/msp_smtp/ssl';
    const XML_PATH_DEBUG_MODE = 'system/msp_smtp/debug';


    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfigInterface
    ) {

        $this->scopeConfig = $scopeConfigInterface;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->scopeConfig->getValue(static::XML_PATH_SMTP_HOST);
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return (int) $this->scopeConfig->getValue(static::XML_PATH_SMTP_PORT);
    }

    /**
     * @return string
     */
    public function getAuthType()
    {
        return $this->scopeConfig->getValue(static::XML_PATH_SMTP_AUTH);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->scopeConfig->getValue(static::XML_PATH_SMTP_USERNAME);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->scopeConfig->getValue(static::XML_PATH_SMTP_PASSWORD);
    }


    /**
     * @return string
     */
    public function getSSL()
    {
        return $this->scopeConfig->getValue(static::XML_PATH_SMTP_SSL);
    }

    /**
     * @return bool
     */
    public function getDebugMode()
    {
        return $this->scopeConfig->isSetFlag(static::XML_PATH_DEBUG_MODE);
    }
}
