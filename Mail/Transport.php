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

namespace MSP\SMTP\Mail;

use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterface;
use MSP\SMTP\Model\Config;

class Transport extends \Zend_Mail_Transport_Smtp implements TransportInterface
{
    protected $config;
    protected $message;

    public function __construct(
        Config $config,
        MessageInterface $message
    ) {
        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }

        $this->config = $config;
        $this->message = $message;

        $host = $this->config->getHost();

        $configuration = $this->getConfiguration();

        parent::__construct($host, $configuration);
    }

    /**
     * Send a mail using this transport
     *
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        try {
            parent::send($this->message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        $config = [
            'port' => $this->config->getPort(),
            'auth' => $this->config->getAuthType(),
        ];

        if ($this->config->getAuthType() == 'login') {
            $config['username'] = $this->config->getUsername();
            $config['password'] = $this->config->getPassword();
        }

        if ($this->config->getSSL() !== 'no') {
            $config['ssl'] = $this->config->getSSL();
        }

        return $config;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
