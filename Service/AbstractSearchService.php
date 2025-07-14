<?php

declare(strict_types=1);

namespace Sandip\SearchOverlay\Service;

abstract class AbstractSearchService
{
    /**
     * The fields to apply the "like" filter on.
     * Override in concrete class or pass in constructor.
     *
     * @var string[]
     */
    protected $searchFields = [];

    /**
     * Main search method called by controller.
     *
     * @param string $query
     *
     * @return array
     */
    public function search($query)
    {
        $collection = $this->getCollection();

        foreach ($this->searchFields as $field) {
            $collection->addFieldToFilter($field, ['like' => '%' . $query . '%']);
        }

        return $this->formatResults($collection);
    }

    /**
     * Child must implement to return collection instance
     * (e.g. product, category, cms page, etc).
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Data\Collection
     */
    abstract protected function getCollection();

    /**
     * Format results into array.
     *
     * @param \Traversable $collection
     *
     * @return array
     */
    protected function formatResults($collection)
    {
        $results = [];
        foreach ($collection as $item) {
            $results[] = [
                'id' => $item->getId(),
                'title' => $this->getTitle($item),
                'url' => $this->buildUrl($item),
            ];
        }

        return $results;
    }

    /**
     * Child can override to build custom URL.
     *
     * @param \Magento\Framework\Model\AbstractModel $item
     *
     * @return string
     */
    protected function buildUrl($item)
    {
        return '#';
    }

    /**
     * Child can override to extract title.
     *
     * @param \Magento\Framework\Model\AbstractModel $item
     *
     * @return string
     */
    protected function getTitle($item)
    {
        return $item->getData('title')
            ?: '';
    }
}
