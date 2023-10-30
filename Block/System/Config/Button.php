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

namespace MageINIC\PricePerCustomer\Block\System\Config;

use Magento\Backend\Block\Widget\Button as ButtonN;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class For System Configuration Button
 */
class Button extends Field
{
    /**
     * Export Price Per-Customer Data Export Url
     */
    public const EXPORT_URL_PATH = 'customerprice/index/export';

    /**
     * @var string
     */
    protected $_template = 'MageINIC_PricePerCustomer::system/config/button.phtml';

    /**
     * Get Customer Url
     *
     * @return string
     */
    public function getCustomUrl(): string
    {
        return $this->getUrl(self::EXPORT_URL_PATH);
    }

    /**
     * Button Html
     *
     * @return string
     * @throws LocalizedException
     */
    public function getButtonHtml(): string
    {
        $button = $this->getLayout()->createBlock(ButtonN::class)->setData(
            [
                'id' => 'buttonId',
                'label' => __('Export CSV')
            ]
        );
        return $button->toHtml();
    }

    /**
     * @inheritdoc
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }
}
