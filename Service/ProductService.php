<?php

namespace Sandip\SearchOverlay\Service;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class ProductService extends AbstractSearchService
{
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var string[]
     */
    protected $searchFields = ['name', 'sku'];

    /**
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        CollectionFactory $productCollectionFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @return \Magento\Framework\Data\Collection|\Magento\Framework\Data\Collection\AbstractDb
     */
    protected function getCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'sku', 'price', 'image']);
        $collection->setPageSize(10);

        return $collection;
    }

    protected function buildUrl($item)
    {
        return $item->getProductUrl();
    }

    protected function getTitle($item)
    {
        return $item->getName();
    }
}
