<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\ProductSearchWidget\Reader;

use Generated\Shared\Transfer\ProductConcreteCriteriaFilterTransfer;
use Generated\Shared\Transfer\ProductViewTransfer;
use Spryker\Yves\Kernel\PermissionAwareTrait;
use SprykerShop\Yves\ProductSearchWidget\Dependency\Client\ProductSearchWidgetToCatalogClientInterface;
use SprykerShop\Yves\ProductSearchWidget\Mapper\ProductConcreteMapperInterface;

class ProductConcreteReader implements ProductConcreteReaderInterface
{
    use PermissionAwareTrait;

    /**
     * @uses \Spryker\Client\Catalog\Plugin\Elasticsearch\ResultFormatter\ProductConcreteCatalogSearchResultFormatterPlugin::NAME
     *
     * @var string
     */
    protected const RESULT_FORMATTER = 'ProductConcreteCatalogSearchResultFormatterPlugin';

    /**
     * @var \SprykerShop\Yves\ProductSearchWidget\Dependency\Client\ProductSearchWidgetToCatalogClientInterface
     */
    protected $catalogClient;

    /**
     * @var \SprykerShop\Yves\ProductSearchWidget\Mapper\ProductConcreteMapperInterface
     */
    protected $productConcreteMapper;

    /**
     * @param \SprykerShop\Yves\ProductSearchWidget\Dependency\Client\ProductSearchWidgetToCatalogClientInterface $catalogClient
     * @param \SprykerShop\Yves\ProductSearchWidget\Mapper\ProductConcreteMapperInterface $productConcreteMapper
     */
    public function __construct(
        ProductSearchWidgetToCatalogClientInterface $catalogClient,
        ProductConcreteMapperInterface $productConcreteMapper
    ) {
        $this->catalogClient = $catalogClient;
        $this->productConcreteMapper = $productConcreteMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteCriteriaFilterTransfer $productConcreteCriteriaFilterTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    public function searchProductConcretesByFullText(ProductConcreteCriteriaFilterTransfer $productConcreteCriteriaFilterTransfer): array
    {
        $searchResults = $this->catalogClient->searchProductConcretesByFullText($productConcreteCriteriaFilterTransfer);

        $productConcretePageSearchTransfers = $searchResults[static::RESULT_FORMATTER] ?? [];

        if (!$productConcretePageSearchTransfers) {
            return [];
        }

        if ($productConcreteCriteriaFilterTransfer->getExcludedProductIds()) {
            $productConcretePageSearchTransfers = $this->filterProductConcretePageSearchTransfersByProductIds(
                $productConcretePageSearchTransfers,
                $productConcreteCriteriaFilterTransfer->getExcludedProductIds(),
            );
        }

        return $this->getMappedProductViewTransfers($productConcretePageSearchTransfers);
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductConcretePageSearchTransfer> $productConcretePageSearchTransfers
     * @param array<int> $excludedProductIds
     *
     * @return array<\Generated\Shared\Transfer\ProductConcretePageSearchTransfer>
     */
    protected function filterProductConcretePageSearchTransfersByProductIds($productConcretePageSearchTransfers, array $excludedProductIds): array
    {
        $filteredProductConcretePageSearchTransfers = [];

        foreach ($productConcretePageSearchTransfers as $productConcretePageSearchTransfer) {
            if (!in_array($productConcretePageSearchTransfer->getFkProduct(), $excludedProductIds, true)) {
                $filteredProductConcretePageSearchTransfers[] = $productConcretePageSearchTransfer;
            }
        }

        return $filteredProductConcretePageSearchTransfers;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductConcretePageSearchTransfer> $productConcretePageSearchTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    protected function getMappedProductViewTransfers(array $productConcretePageSearchTransfers): array
    {
        $productViewTransfers = [];

        foreach ($productConcretePageSearchTransfers as $productConcretePageSearchTransfer) {
            $productViewTransfers[] = $this->productConcreteMapper->mapProductConcretePageSearchTransferToProductViewTransfer(
                $productConcretePageSearchTransfer,
                new ProductViewTransfer(),
            );
        }

        return $productViewTransfers;
    }
}
