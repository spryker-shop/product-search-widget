<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\ProductSearchWidget\Expander;

use Generated\Shared\Transfer\CurrentProductPriceTransfer;
use Generated\Shared\Transfer\PriceProductFilterTransfer;
use Generated\Shared\Transfer\ProductViewTransfer;
use Spryker\Yves\Kernel\PermissionAwareTrait;
use SprykerShop\Yves\ProductSearchWidget\Dependency\Client\ProductSearchWidgetToPriceProductStorageClientInterface;

class ProductConcretePriceExpander implements ProductConcretePriceExpanderInterface
{
    use PermissionAwareTrait;

    /**
     * @var \SprykerShop\Yves\ProductSearchWidget\Dependency\Client\ProductSearchWidgetToPriceProductStorageClientInterface
     */
    protected $priceProductStorageClient;

    /**
     * @param \SprykerShop\Yves\ProductSearchWidget\Dependency\Client\ProductSearchWidgetToPriceProductStorageClientInterface $priceProductStorageClient
     */
    public function __construct(ProductSearchWidgetToPriceProductStorageClientInterface $priceProductStorageClient)
    {
        $this->priceProductStorageClient = $priceProductStorageClient;
    }

    /**
     * @uses \Spryker\Yves\Kernel\PermissionAwareTrait::can()
     *
     * @param \Generated\Shared\Transfer\ProductViewTransfer $productViewTransfer
     *
     * @return \Generated\Shared\Transfer\ProductViewTransfer
     */
    public function expandProductViewTransferWithPrice(ProductViewTransfer $productViewTransfer): ProductViewTransfer
    {
        if (!$this->can('SeePricePermissionPlugin')) {
            return $productViewTransfer;
        }

        return $productViewTransfer->setPrice(
            $this->getCurrentProductPriceTransfer($productViewTransfer)->getSumPrice()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ProductViewTransfer $productViewTransfer
     *
     * @return \Generated\Shared\Transfer\CurrentProductPriceTransfer
     */
    protected function getCurrentProductPriceTransfer(ProductViewTransfer $productViewTransfer): CurrentProductPriceTransfer
    {
        $priceProductFilterTransfer = (new PriceProductFilterTransfer())
            ->setQuantity($productViewTransfer->getQuantity() ?: 1)
            ->setIdProduct($productViewTransfer->getIdProductConcrete())
            ->setIdProductAbstract($productViewTransfer->getIdProductAbstract());

        return $this->priceProductStorageClient->getResolvedCurrentProductPriceTransfer($priceProductFilterTransfer);
    }
}
