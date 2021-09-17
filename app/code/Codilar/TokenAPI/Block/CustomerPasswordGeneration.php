<?php
namespace Codilar\TokenAPI\Block;

class CustomerPasswordGeneration  extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Math\Random $mathRandom
    ) {        
        $this->mathRandom = $mathRandom;
        parent::__construct($context);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length = 10)
    {
        $chars = \Magento\Framework\Math\Random::CHARS_LOWERS
            . \Magento\Framework\Math\Random::CHARS_UPPERS
            . \Magento\Framework\Math\Random::CHARS_DIGITS;

        return $password = $this->mathRandom->getRandomString($length, $chars);
    }
}