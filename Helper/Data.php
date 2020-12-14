<?php
namespace Sga\Configview\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Config\Model\ResourceModel\Config\Data\Collection;
use Magento\Eav\Model\Config as EavConfig;

class Data extends AbstractHelper
{
    protected $_collection;
    protected $_eavConfig;

    public function __construct(
        Context $context,
        Collection $collection,
        EavConfig $eavConfig
    ) {
        $this->_collection = $collection;
        $this->_eavConfig = $eavConfig;

        parent::__construct($context);
    }

    public function getConfigNbRewrite(array $keys)
    {
        $select = $this->_collection->getSelect();
        $rs = $select->reset()
            ->from(
                $this->_collection->getTable('core_config_data'),
                array('path', 'nb' => new \Zend_Db_Expr('COUNT(*)'))
            )
            ->where('path IN ("'.implode('","', $keys).'")')
            ->where('scope_id<>0')
            ->group(array('path'))
            ->query();

        $entries = $rs->fetchAll();
        $list = [];
        foreach ($entries as $entry) {
            $list[$entry['path']] = $entry['nb'];
        }
        return $list;
    }

    public function getConfigScopeDefaultValue($section, $group, $field)
    {
        $select = $this->_collection->getSelect();
        $rs = $select->reset()
            ->from($this->_collection->getTable('core_config_data'), 'value')
            ->where('path = "'.$section.'/'.$group.'/'.$field.'"')
            ->where('scope=?', 'default')
            ->query();

        $entries = $rs->fetchAll();
        return isset($entries[0]['value']) ? $entries[0]['value'] : null;
    }

    public function getConfigScopeWebsiteValue($section, $group, $field, $websiteId)
    {
        $select = $this->_collection->getSelect();
        $rs = $select->reset()
            ->from($this->_collection->getTable('core_config_data'), 'value')
            ->where('path = "'.$section.'/'.$group.'/'.$field.'"')
            ->where('scope=?', 'website')
            ->where('scope_id=?', $websiteId)
            ->query();

        $entries = $rs->fetchAll();
        return isset($entries[0]['value']) ? $entries[0]['value'] : null;
    }

    public function getConfigScopeStoreValue($section, $group, $field, $storeId)
    {
        $select = $this->_collection->getSelect();
        $rs = $select->reset()
            ->from($this->_collection->getTable('core_config_data'), 'value')
            ->where('path = "'.$section.'/'.$group.'/'.$field.'"')
            ->where('scope=?', 'stores')
            ->where('scope_id=?', $storeId)
            ->query();

        $entries = $rs->fetchAll();
        return isset($entries[0]['value']) ? $entries[0]['value'] : null;
    }

    public function getProductAttributeNbRewrite($productId, array $attributes)
    {
        $list = [];

        foreach ($attributes as $attributeCode) {
            // load attribute
            $attribute = $this->_eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);

            $list[$attributeCode] = [
                'nb' => 0,
                'static' => 1
            ];

            if ((int)$attribute->getId() > 0 && $attribute->getBackendType() !== 'static') {
                $list[$attributeCode]['static'] = $attribute->getBackendType() === 'static' ? 1 : 0;

                $select = $this->_collection->getSelect();
                $rs = $select->reset()
                    ->from(
                        $attribute->getBackendTable(),
                        array('nb' => new \Zend_Db_Expr('COUNT(*)'))
                    )
                    ->where('attribute_id=?', $attribute->getId())
                    ->where('entity_id=?', $productId)
                    ->where('store_id<>0')
                    ->group(array('attribute_id'))
                    ->query();

                $entries = $rs->fetchAll();
                foreach ($entries as $entry) {
                    $list[$attributeCode]['nb'] = $entry['nb'];
                }
            }
        }

        return $list;
    }

    public function getProductAttributeScopeValue($productId, $attributeCode, $storeId = 0)
    {
        $nb = null;
        $attribute = $this->_eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
        if ((int)$attribute->getId() > 0 && $attribute->getBackendType() !== 'static') {
            $select = $this->_collection->getSelect();
            $rs = $select->reset()
                ->from(
                    $attribute->getBackendTable(),
                    array('value')
                )
                ->where('attribute_id=?', $attribute->getId())
                ->where('entity_id=?', $productId)
                ->where('store_id=?', $storeId)
                ->query();

            $entries = $rs->fetchAll();
            $nb = isset($entries[0]['value']) ? $entries[0]['value'] : null;
        }
        return $nb;
    }
}