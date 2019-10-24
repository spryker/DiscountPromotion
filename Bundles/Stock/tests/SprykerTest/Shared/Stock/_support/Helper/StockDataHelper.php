<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\Stock\Helper;

use Codeception\Module;
use Generated\Shared\DataBuilder\StockProductBuilder;
use Generated\Shared\DataBuilder\TypeBuilder;
use Generated\Shared\Transfer\TypeTransfer;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;

class StockDataHelper extends Module
{
    use DataCleanupHelperTrait;
    use LocatorHelperTrait;

    /**
     * @param array $override
     *
     * @return void
     */
    public function haveProductInStock(array $override = [])
    {
        $stockFacade = $this->getStockFacade();
        $stockTypeTransfer = (new TypeBuilder([TypeTransfer::NAME => 'Warehouse1']))->build();
        $stockFacade->createStockType($stockTypeTransfer);
        $stockFacade->createStockProduct((new StockProductBuilder($override))->build()->setStockType($stockTypeTransfer->getName()));
    }

    /**
     * @param array $seed
     *
     * @return \Generated\Shared\Transfer\TypeTransfer
     */
    public function haveStock(array $seed = []): TypeTransfer
    {
        $typeTransfer = (new TypeBuilder($seed))->build();
        $typeTransfer->setIdStock($this->getStockFacade()->createStockType($typeTransfer));

        return $typeTransfer;
    }

    /**
     * @return \Spryker\Zed\Stock\Business\StockFacadeInterface
     */
    private function getStockFacade()
    {
        return $this->getLocator()->stock()->facade();
    }
}
