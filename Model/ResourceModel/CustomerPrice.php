<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_PricePerCustomer
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\PricePerCustomer\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class For Price Per Customer Resource Model
 */
class CustomerPrice extends AbstractDb
{
    /**#@+
     * Constants For Module Table
     */
    public const TABLE_NAME = 'mageinic_price_per_customer';
    public const PRIMARY_KEY = 'entity_id';
    /**#@-*/

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY);
    }

    /**
     * Save CustomPrice Relation
     *
     * @param array $customPriceIds
     * @param int $productId
     * @return CustomerPrice
     */
    public function saveCustomPriceRelation(array $customPriceIds, int $productId): CustomerPrice
    {
        $oldProducts = $this->lookupCustomPriceIds($productId);
        $newProducts = $customPriceIds;
        if (isset($newProducts)) {
            $table = $this->getTable('mageinic_price_per_customer');
            $insert = array_diff((array)$newProducts, $oldProducts);
            $delete = array_diff($oldProducts, $newProducts);
            if ($delete) {
                $where = [
                    'product_id = ?' => (int)$productId,
                    'customer_id IN (?)' => $delete
                ];
                $this->getConnection()->delete($table, $where);
            }
            if ($insert) {
                $data = [];
                foreach ($insert as $productId) {
                    $data[] = [
                        'product_id' => (int)$productId,
                        'customer_id' => (int)$customPriceIds
                    ];
                }
                $this->getConnection()->insertMultiple($table, $data);
            }
        }
        return $this;
    }

    /**
     * Custom Price Of Before Save
     *
     * @param AbstractModel $object
     * @return CustomerPrice
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object): CustomerPrice
    {
        $getEntityId = $object->getEntityId();
        $productId = $object->getProductId();
        $customerId = $object->getCustomerId();
        $adapter = $this->getConnection();
        $select = $adapter->select()->from($this->getTable('mageinic_price_per_customer'), '*')
            ->where('product_id = ?', (int)$productId)
            ->where('customer_id = ?', (int)$customerId);
        if ($object->getEntityId()) {
            $select->where('entity_id = ?', (int)$object->getEntityId());
        }
        if ($adapter->fetchRow($select)) {
            if ($adapter->fetchRow($select)['product_id'] == $productId
                && $adapter->fetchRow($select)['customer_id'] == $customerId
                && $adapter->fetchRow($select)['entity_id'] != $getEntityId
            ) {
                throw new LocalizedException(
                    __('This custom product price is all ready added to this customer.')
                );
            }
        }
        return $this;
    }

    /**
     * Custom Price Ids
     *
     * @param int $productId
     * @return array
     */
    public function lookupCustomPriceIds(int $productId): array
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from($this->getTable(
            'mageinic_price_per_customer'
        ), 'customer_id')->where('product_id = ?', (int)$productId);
        return $adapter->fetchCol($select);
    }
}
