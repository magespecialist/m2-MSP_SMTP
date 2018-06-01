<?php
/**
 * IDEALIAGroup srl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@idealiagroup.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2014 IDEALIAGroup srl (http://www.idealiagroup.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\SMTP\Console;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use MSP\SMTP\Model\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelftestCommand extends Command
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        $name = null
    ) {

        $this->objectManager = $objectManager;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('msp:smtp:selftest')
            ->setDescription('Test SMTP settings');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $state = $this->objectManager->get(State::class);

        try {
            $state->getAreaCode();
        } catch (\Exception $e) {
            $state->setAreaCode(Area::AREA_ADMINHTML);
        }

        $scopeConfig = $this->objectManager->get(ScopeConfigInterface::class);
        $transportBuilder = $this->objectManager->get(TransportBuilder::class);
        $config = $this->objectManager->get(Config::class);


        $contact = [
            'email' => $scopeConfig->getValue('contact/email/recipient_email'),
            'name' => 'SMTP self test'
        ];

        $output->writeln('<info>Starting test:</info>');
        $output->writeln('Attempting to send mail to ' . $contact['email'] . " ... plese wait");
        $output->write("Smtp configuration:\nHost: "
            . $config->getHost() . "\nPort: "
            . $config->getPort() . "\nUser: "
            . $config->getUsername() . "\nAuth: "
            . $config->getAuthType() . "\nTLS/SSL: "
            . $config->getSSL(), true);

        $postObject = new DataObject();
        $postObject->setData(
            [
                'name' => 'Test email',
                'email' => 'test@test.me',
                'telephone' => '12345',
                'comment' => 'This is a test email sent by msp_smtp self test function'
            ]
        );

        try {
            $transport = $transportBuilder
                ->setTemplateIdentifier($scopeConfig->getValue('contact/email/email_template', ScopeInterface::SCOPE_STORE))
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(
                    [
                        'data' => $postObject
                    ]
                )
                ->setFrom($contact)
                ->addTo($contact['email'])
                ->getTransport();

            $transport->sendMessage();
            $output->writeln('<info>Test successful!</info>');
            $output->writeln('Mail sent without errors on our side, check your mailbox for email.');
        } catch (\Exception $e) {
            $output->write("<error>Test failed with error:\n\n". $e->getMessage() . "</error>", true);
        }
    }
}
