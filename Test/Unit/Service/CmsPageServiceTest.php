<?php
namespace Sandip\SearchOverlay\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use Sandip\SearchOverlay\Service\CmsPageService;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Cms\Model\ResourceModel\Page\Collection;
use Magento\Cms\Model\Page;

class CmsPageServiceTest extends TestCase
{
    private $collectionFactoryMock;
    private $collectionMock;
    private $cmsPageService;

    protected function setUp(): void
    {
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->collectionMock = $this->createMock(Collection::class);

        $this->collectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);

        $this->cmsPageService = new CmsPageService($this->collectionFactoryMock);
    }

    public function testSearchAppliesFiltersAndReturnsFormattedResults()
    {
        $query = 'Test';

        $pageMock = $this->createMock(Page::class);
        $pageMock->method('getId')->willReturn(15);
        $pageMock->method('getTitle')->willReturn('Test Page');
        $pageMock->method('getIdentifier')->willReturn('test-page');

        $this->collectionMock->expects($this->exactly(3))
            ->method('addFieldToFilter')
            ->withConsecutive(
                ['title', ['like' => '%Test%']],
                ['content_heading', ['like' => '%Test%']],
                ['content', ['like' => '%Test%']]
            )->willReturnSelf();

        $this->collectionMock->method('getIterator')
            ->willReturn(new \ArrayIterator([$pageMock]));

        $result = $this->cmsPageService->search($query);

        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 15,
            'title' => 'Test Page',
            'url' => '/test-page'
        ], $result[0]);
    }
}
