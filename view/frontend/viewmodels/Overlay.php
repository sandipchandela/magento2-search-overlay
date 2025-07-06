<?php
namespace Sandip\SearchOverlay\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Sandip\SearchOverlay\Helper\Data;

class Overlay implements ArgumentInterface
{
    protected $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    public function isEnabled(): bool
    {
        return $this->helper->isEnabled();
    }
}
