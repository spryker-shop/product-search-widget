<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\ProductSearchWidget\Mapper;

use Generated\Shared\Transfer\ProductConcretePageSearchTransfer;
use Generated\Shared\Transfer\ProductViewTransfer;

interface ProductConcreteMapperInterface
{
    public function mapProductConcretePageSearchTransferToProductViewTransfer(
        ProductConcretePageSearchTransfer $productConcretePageSearchTransfer,
        ProductViewTransfer $productViewTransfer
    ): ProductViewTransfer;
}
