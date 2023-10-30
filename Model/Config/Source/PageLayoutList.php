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

namespace MageINIC\PricePerCustomer\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class For Page Layout List Dropdown
 */
class PageLayoutList implements OptionSourceInterface
{
    /**
     * Page Layout List Array
     *
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => ' ', 'label' => __('Please Select')],
            ['value' => '1column', 'label' => __('1 Column')],
            ['value' => '2columns-left', 'label' => __('2 Column with Left Bar')],
            ['value' => '2columns-right', 'label' => __('2 Column with Right Bar')],
            ['value' => '3columns', 'label' => __('3 Column')],
            ['value' => 'category-full-width', 'label' => __('Category Full Width')],
        ];
    }
}
