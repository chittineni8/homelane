<?php
namespace Codilar\SendEmail\Observer\Customer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OrderDetails
 * @package Codilar\SendEmail\Observer
 */
class Data implements ObserverInterface
{
    protected $logger;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var StateInterface
     */
    private $inlineTranslation;
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;


    private $scopeConfig;

    /**
     * OrderDetails constructor.
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $queryData = $observer->getEvent()->getQuery();
        $name=$queryData['name'];
        $phoneno=$queryData['phoneno'];
        $email=$queryData['email'];
        $pincode=$queryData['pincode'];
        $whatsapp=$queryData['whatsapp'];
        /* Receiver Detail */
        $receivers = $this->scopeConfig->getValue(
            'email_section/sendmail/receiver',
            ScopeInterface::SCOPE_STORE
        );
        $receiver_array=explode(",",$receivers);
        $store = $this->storeManager->getStore();
        $templateParams = ['store' => $store,
            'name'=>$name,
            'phoneno'=>$phoneno,
            'email'=>$email,
            'pincode'=>$pincode,
            'whatsapp'=>$whatsapp
        ];
        foreach ($receiver_array as $receiver) {
            $transport = $this->transportBuilder->setTemplateIdentifier(
                'email_section_sendmail_email_template'
            )->setTemplateOptions(
                ['area' => 'frontend', 'store' => $store->getId()]
            )->addTo(
                $receiver

            )->setTemplateVars(
                $templateParams
            )->setFrom(
                'general'
            )->getTransport();

            try {
                // Send an email
                $transport->sendMessage();
                $this->logger->info('Mail sent successfully');
            } catch (\Exception $e) {
                // Write a log message whenever get errors
                $this->logger->critical($e->getMessage());
            }
        }
        return $this;
    }
}

