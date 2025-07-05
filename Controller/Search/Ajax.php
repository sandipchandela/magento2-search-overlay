<?php
namespace Hyva\QuickSearchOverlay\Controller\Search;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Search\Model\QueryFactory;

class Ajax extends Action
{
    protected $jsonFactory;
    protected $productCollectionFactory;
    protected $pageCollectionFactory;
    protected $categoryCollectionFactory;
    protected $queryFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        ProductCollectionFactory $productCollectionFactory,
        PageCollectionFactory $pageCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        QueryFactory $queryFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->queryFactory = $queryFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $query = $this->getRequest()->getParam('q');
        $result = $this->jsonFactory->create();

        if (!$query) {
            return $result->setData([]);
        }

        // Fetch matching products
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect(['name', 'sku', 'price', 'image']);
        $productCollection->addAttributeToFilter([
            ['attribute' => 'name', 'like' => '%' . $query . '%'],
            ['attribute' => 'sku', 'like' => '%' . $query . '%']
        ]);
        $productCollection->setPageSize(6);

        $products = [];
        foreach ($productCollection as $product) {
            $products[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'price' => $product->getPrice(),
                'image' => $product->getMediaConfig()->getMediaUrl($product->getImage())
            ];
        }

        // Fetch CMS pages
        $cmsPages = $this->pageCollectionFactory->create()
            ->addFieldToFilter('title', ['like' => '%' . $query . '%']);
        $pages = [];
        foreach ($cmsPages as $page) {
            $pages[] = [
                'id' => $page->getId(),
                'title' => $page->getTitle(),
                'url' => '/' . $page->getIdentifier()
            ];
        }

        // Fetch categories
        $categoriesCollection = $this->categoryCollectionFactory->create();
        $categoriesCollection->addAttributeToSelect('name');
        $categoriesCollection->addAttributeToFilter('name', ['like' => '%' . $query . '%']);
        $categories = [];
        foreach ($categoriesCollection as $category) {
            $categories[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'url' => '/catalog/category/view/id/' . $category->getId()
            ];
        }

        // Fetch suggestions from query table
        $searchQuery = $this->queryFactory->get();
        $searchQuery->setQueryText($query)->prepare();
        $suggestions = [$searchQuery->getQueryText()];

        return $result->setData([
            'products' => $products,
            'pages' => $pages,
            'categories' => $categories,
            'suggestions' => $suggestions
        ]);
    }
}
