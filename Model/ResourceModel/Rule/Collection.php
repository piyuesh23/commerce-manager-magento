<?php

namespace Acquia\CommerceManager\Model\ResourceModel\Rule;


class Collection extends \Magento\CatalogRule\Model\ResourceModel\Rule\Collection
{

    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     * Copied from \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection::getAllIds
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Create all ids retrieving select with limitation
     * Backward compatibility with EAV collection
     * Copied from \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection::_getAllIdsSelect
     *
     * @param int $limit
     * @param int $offset
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->limit($limit, $offset);
        return $idsSelect;
    }
}


