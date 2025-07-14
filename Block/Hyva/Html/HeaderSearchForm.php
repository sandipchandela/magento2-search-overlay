<?php

declare(strict_types=1);

namespace Sandip\SearchOverlay\Block\Hyva\Html;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Sandip\SearchOverlay\Helper\Data;

class HeaderSearchForm extends Template
{
    /**
     * @param Context $context
     * @param Data $configHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $configHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        if (null === $this->_template) {
            $this->_template = $this->configHelper->isEnabled()
                ? 'Sandip_SearchOverlay::overlay.phtml'
                : 'Magento_Theme::html/header/search-form.phtml';
        }

        return parent::getTemplate();
    }
}
