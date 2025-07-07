<?php
namespace Sandip\SearchOverlay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'sandipsearchoverlay/settings/enabled';

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
			self::XML_PATH_ENABLED, 
			ScopeInterface::SCOPE_STORE
		);
    }
}
