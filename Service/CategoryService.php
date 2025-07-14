<?php
namespace Sandip\SearchOverlay\Service;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class CategoryService extends AbstractSearchService
{
    private $categoryCollectionFactory;
    protected $searchFields = ['name'];

    public function __construct(CollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    protected function getCollection()
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        return $collection;
    }

    protected function buildUrl($item)
    {
        return '/catalog/category/view/id/' . $item->getId();
    }

    protected function getTitle($item)
    {
        return $item->getName();
    }
}
