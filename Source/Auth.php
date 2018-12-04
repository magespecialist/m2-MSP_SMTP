<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\SMTP\Source;

use Magento\Framework\Option\ArrayInterface;

class Auth implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'login',
                'label' => 'Login'
            ],
            [
                'value' => 'plain',
                'label' => 'Plain'
            ]
        ];
    }
}
