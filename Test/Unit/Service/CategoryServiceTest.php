<?php
namespace Sandip\SearchOverlay\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use Sandip\SearchOverlay\Service\CategoryService;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\Category;

class CategoryServiceTest extends TestCase
{
    private $collectionFactoryMock;
    private $collectionMock;
    private $categoryService;

    protected function setUp(): void
    {
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->collectionMock = $this->createMock(Collection::class);

        $this->collectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);

        $this->categoryService = new CategoryService($this->collectionFactoryMock);
    }

    public function testSearchAppliesFiltersAndReturnsFormattedResults()
    {
        $query = 'Test';

        // Prepare mock Category
        $categoryMock = $this->createMock(Category::class);
        $categoryMock->method('getId')->willReturn(10);
        $categoryMock->method('getName')->willReturn('Test Category');

        // Mock methods on collection
        $this->collectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->with('name')
            ->willReturnSelf();

        $this->collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with('name', ['like' => '%Test%'])
            ->willReturnSelf();

        // Make it iterable
        $this->collectionMock->method('getIterator')
            ->willReturn(new \ArrayIterator([$categoryMock]));

        // Act
        $result = $this->categoryService->search($query);

        // Assert
        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 10,
            'title' => 'Test Category',
            'url' => '/catalog/category/view/id/10'
        ], $result[0]);
    }
}
