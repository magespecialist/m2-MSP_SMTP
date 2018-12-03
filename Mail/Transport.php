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
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use MSP\SMTP\Model\Config;
use Zend\Mail\Message as ZendMessage;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class Transport implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Smtp
     */
    private $zendTransport;
    /**
     * @var MessageInterface
     */
    private $message;

    public function __construct(
        Config $config,
        MessageInterface $message,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->message = $message;
        $this->logger = $logger;
        $this->zendTransport = new Smtp();
    }

    /**
     * @inheritdoc
     */
    public function sendMessage()
    {
        try {
            $options = new SmtpOptions($this->getConfiguration());
            $this->zendTransport->setOptions($options);
            /** @var \Zend\Mail\Message $message */
            $message = ZendMessage::fromString($this->message->getRawMessage());
            $this->zendTransport->send($message);

            if ($this->config->getDebugMode()) {
                /** @var \Zend\Mail\Message $message */
                $message = ZendMessage::fromString($this->message->getRawMessage());
                $this->logger->log(Logger::DEBUG, __("Mail sent to %1 with subject %2", $message->getTo()->current()->getEmail(), $message->getSubject()));
            }
        } catch (\Exception $e) {
            /** @var \Zend\Mail\Message $message */
            $message = ZendMessage::fromString($this->message->getRawMessage());
            $this->logger->log(Logger::ERROR, __("Failed to send email %1 with subject %2; error: %3", $message->getTo()->current()->getEmail(), $message->getSubject(), $e->getMessage()));
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
            'connection_class' => $this->config->getAuthType(),
            'host' => $this->config->getHost(),
            'connection_config' => []
        ];

        if ($this->config->getAuthType() == 'login') {
            $config['connection_config']['username'] = $this->config->getUsername();
            $config['connection_config']['password'] = $this->config->getPassword();
        }

        if ($this->config->getSSL() !== 'no') {
            $config['connection_config']['ssl'] = $this->config->getSSL();
        }

        return $config;
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return $this->message;
    }
}
