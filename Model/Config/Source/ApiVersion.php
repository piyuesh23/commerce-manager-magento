<?php

namespace Acquia\CommerceManager\Model\Config\Source;

/**
 * Source model for choosing the Commerce Connector Service API version.
 */
class ApiVersion implements \Magento\Framework\Option\ArrayInterface
{

    const COMMERCE_CONNECTOR_SERVICE_V1_PATH = "v1";
    const COMMERCE_CONNECTOR_SERVICE_V2_PATH = "v2";

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::COMMERCE_CONNECTOR_SERVICE_V1_PATH, 'label' => __('Version one')],
            ['value' => self::COMMERCE_CONNECTOR_SERVICE_V2_PATH, 'label' => __('Version two')],
        ];
    }
}
