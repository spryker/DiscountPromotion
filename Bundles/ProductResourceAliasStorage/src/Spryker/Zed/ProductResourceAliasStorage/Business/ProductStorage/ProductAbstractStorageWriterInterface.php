<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductResourceAliasStorage\Business\ProductStorage;

interface ProductAbstractStorageWriterInterface
{
    /**
     * @param int[] $productAbstractIds
     *
     * @return void
     */
    public function updateProductAbstractStorageSkus(array $productAbstractIds): void;
}
