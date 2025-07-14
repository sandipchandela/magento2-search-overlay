<?php

namespace Sandip\SearchOverlay\Controller\Search;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Search\Model\QueryFactory;
use Sandip\SearchOverlay\Model\SearchServicePool;

class Ajax extends Action
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;
    /**
     * @var QueryFactory
     */
    private $queryFactory;
    /**
     * @var SearchServicePool
     */
    private $servicePool;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param QueryFactory $queryFactory
     * @param SearchServicePool $servicePool
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        QueryFactory $queryFactory,
        SearchServicePool $servicePool
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->queryFactory = $queryFactory;
        $this->servicePool = $servicePool;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $query = trim($this->getRequest()->getParam('q'));
        $searchQuery = $this->queryFactory->get();
        $searchQuery->setQueryText($query);
        $minQueryLength = $searchQuery->getMinQueryLength();

        $result = $this->jsonFactory->create();
        if (!$query || strlen($query) < $minQueryLength) {
            return $result->setData([]);
        }

        $results = [];
        foreach ($this->servicePool->getServices() as $key => $service) {
            $results[$key] = $service->search($query);
        }

        $results['suggestions'] = [$searchQuery->getQueryText()];

        return $result->setData($results);
    }
}
