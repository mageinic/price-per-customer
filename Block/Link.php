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

namespace MageINIC\PricePerCustomer\Block;

use Magento\Framework\Math\Random;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use MageINIC\PricePerCustomer\Helper\Data;
use Magento\Framework\View\Element\Html\Link as HtmlLink;

/**
 * Class For To Add Top Menu And Footer Link
 */
class Link extends HtmlLink
{
    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * Link Constructor
     *
     * @param Context $context
     * @param Data $helperData
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     * @param Random|null $random
     */
    public function __construct(
        Context             $context,
        Data                $helperData,
        array               $data = [],
        ?SecureHtmlRenderer $secureRenderer = null,
        ?Random             $random = null
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data, $secureRenderer, $random);
    }

    /**
     * Render Html
     *
     * @return string|void
     */
    protected function _toHtml()
    {
        if ($this->helperData->isEnable()) {
            $html = '<li>';
            $html .= '<a ' . $this->getLinkAttributes() . '>'
                . $this->helperData->getLinkLabel() . '</a>';
            $html .= '</li>';

            return $html;
        }
    }
}
