<?php
namespace Sandip\SearchOverlay\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use Sandip\SearchOverlay\Service\AbstractSearchService;
use Magento\Framework\DataObject;

class AbstractSearchServiceTest extends TestCase
{
    public function testSearchAppliesFiltersAndFormatsResults()
    {
        $collectionMock = $this->getMockBuilder(\Magento\Framework\Data\Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Should addFieldToFilter 2 times
        $collectionMock->expects($this->exactly(2))
            ->method('addFieldToFilter')
            ->withConsecutive(
                ['title', ['like' => '%Query%']],
                ['description', ['like' => '%Query%']]
            )->willReturnSelf();

        $item = new DataObject([
            'id' => 99,
            'title' => 'Demo Title'
        ]);

        $collectionMock->method('getIterator')
            ->willReturn(new \ArrayIterator([$item]));

        // Anonymous stub class extending AbstractSearchService
        $service = new class($collectionMock) extends AbstractSearchService {
            protected $searchFields = ['title', 'description'];

            private $collection;

            public function __construct($collection)
            {
                $this->collection = $collection;
            }

            protected function getCollection()
            {
                return $this->collection;
            }

            protected function buildUrl($item)
            {
                return '/dummy/' . $item->getId();
            }
        };

        $result = $service->search('Query');

        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 99,
            'title' => 'Demo Title',
            'url' => '/dummy/99'
        ], $result[0]);
    }
}
