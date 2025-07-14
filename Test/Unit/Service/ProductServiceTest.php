<?php
namespace Sandip\SearchOverlay\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use Sandip\SearchOverlay\Service\ProductService;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\Product;

class ProductServiceTest extends TestCase
{
    private $collectionFactoryMock;
    private $collectionMock;
    private $productService;

    protected function setUp(): void
    {
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->collectionMock = $this->createMock(Collection::class);

        $this->collectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);

        $this->productService = new ProductService($this->collectionFactoryMock);
    }

    public function testSearchAppliesFiltersAndReturnsFormattedResults()
    {
        $query = 'Test';

        $productMock = $this->createMock(Product::class);
        $productMock->method('getId')->willReturn(5);
        $productMock->method('getName')->willReturn('Test Product');
        $productMock->method('getProductUrl')->willReturn('/product/test');

        $this->collectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->with(['name', 'sku', 'price', 'image'])
            ->willReturnSelf();

        $this->collectionMock->expects($this->once())
            ->method('setPageSize')
            ->with(6)
            ->willReturnSelf();

        $this->collectionMock->expects($this->exactly(2))
            ->method('addFieldToFilter')
            ->withConsecutive(
                ['name', ['like' => '%Test%']],
                ['sku', ['like' => '%Test%']]
            )->willReturnSelf();

        $this->collectionMock->method('getIterator')
            ->willReturn(new \ArrayIterator([$productMock]));

        $result = $this->productService->search($query);

        $this->assertCount(1, $result);
        $this->assertSame([
            'id' => 5,
            'title' => 'Test Product',
            'url' => '/product/test'
        ], $result[0]);
    }
}
