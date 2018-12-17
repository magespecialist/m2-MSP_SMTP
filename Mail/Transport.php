<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\SMTP\Mail;

use Magento\Framework\Mail\MailMessageInterface;
use Monolog\Logger;
use PHP_CodeSniffer\Tokenizers\PHP;
use Psr\Log\LoggerInterface;
use MSP\SMTP\Model\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
     * @var MailMessageInterface
     */
    private $message;

    /**
     * @var PHPMailer
     */
    private $mailer;

    public function __construct(
        Config $config,
        MailMessageInterface $message,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->message = $message;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function sendMessage()
    {
        try {
            $this->setSmtpOptions();
            $this->setRecipients();
            $this->setContent();

            $this->getMailer()->send();

            if ($this->config->getDebugMode()) {
                $zendMailMessage = \Zend\Mail\Message::fromString($this->getMessage()->getRawMessage());
                $this->logger->log(Logger::DEBUG, __("Mail sent to %1 with subject %2", $zendMailMessage->getTo()->rewind()->getEmail(), $zendMailMessage->getSubject()));
            }
        } catch (Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($this->getMailer()->ErrorInfo), $e);
        }
    }

    private function setSmtpOptions(): void
    {
        $this->getMailer()->isSMTP();
        $this->getMailer()->Host = $this->config->getHost();
        if ($this->config->getAuthType() === 'login') {
            $this->getMailer()->SMTPAuth = true;
            $this->getMailer()->Username = $this->config->getUsername();
            $this->getMailer()->Password = $this->config->getPassword();
        } else {
            $this->getMailer()->SMTPAuth = false;
        }
        $this->getMailer()->SMTPSecure = $this->config->getSecure();
        $this->getMailer()->Port = $this->config->getPort();
    }

    public function setRecipients(): void
    {
        /** @var \Zend\Mail\Message $zendMailMessage */
        $zendMailMessage = \Zend\Mail\Message::fromString($this->getMessage()->getRawMessage());

        $recipient = $zendMailMessage->getFrom()->rewind();
        $this->getMailer()->setFrom($recipient->getEmail(), $recipient->getName());

        $recipient = $zendMailMessage->getTo()->rewind();
        while ($recipient) {
            $this->getMailer()->addAddress($recipient->getEmail(), $recipient->getName());
            $recipient = $zendMailMessage->getTo()->next();
        }

        $recipient = $zendMailMessage->getReplyTo()->rewind();
        while ($recipient) {
            $this->getMailer()->addReplyTo($recipient->getEmail(), $recipient->getName());
            $recipient = $zendMailMessage->getReplyTo()->next();
        }

        $recipient = $zendMailMessage->getCc()->rewind();
        while ($recipient) {
            $this->getMailer()->addCC($recipient->getEmail(), $recipient->getName());
            $recipient = $zendMailMessage->getCc()->next();
        }

        $recipient = $zendMailMessage->getBCC()->rewind();
        while ($recipient) {
            $this->getMailer()->addBCC($recipient->getEmail(), $recipient->getName());
            $recipient = $zendMailMessage->addBCC()->next();
        }
    }

    public function setContent(): void
    {
        $this->getMailer()->isHTML(true);
        $this->getMailer()->Subject = $this->getMessage()->getSubject();

        $this->getMailer()->CharSet = PHPMailer::CHARSET_UTF8;

        /** @var \Zend\Mime\Message $body */
        $body = $this->getMessage()->getBody();

        /** @var \Zend\Mime\Part $part */
        $part = $body->getParts()[0];
        $part->setEncoding();
        $this->getMailer()->Body = $part->getContent();
        $this->getMailer()->AltBody = strip_tags($part->getContent());
    }

    public function getMailer(): \PHPMailer\PHPMailer\PHPMailer
    {
        if (!$this->mailer) {
            $this->mailer = new PHPMailer(true);
        }
        return $this->mailer;
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): \Magento\Framework\Mail\MailMessageInterface
    {
        return $this->message;
    }
}
