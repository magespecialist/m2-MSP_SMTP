<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\SMTP\Source;

use Magento\Framework\Option\ArrayInterface;

class Secure implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '',
                'label' => 'No'
            ],
            [
                'value' => 'tls',
                'label' => 'TLS'
            ],
            [
                'value' => 'ssl',
                'label' => 'SSL'
            ],
        ];
    }
}
