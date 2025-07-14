<?php
namespace Sandip\SearchOverlay\Service;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;

class CmsPageService extends AbstractSearchService
{
    private $pageCollectionFactory;
    protected $searchFields = ['title', 'content_heading', 'content'];

    public function __construct(CollectionFactory $pageCollectionFactory)
    {
        $this->pageCollectionFactory = $pageCollectionFactory;
    }

    protected function getCollection()
    {
        return $this->pageCollectionFactory->create();
    }

    protected function buildUrl($item)
    {
        return '/' . ltrim($item->getIdentifier(), '/');
    }

    protected function getTitle($item)
    {
        return $item->getTitle();
    }
}
