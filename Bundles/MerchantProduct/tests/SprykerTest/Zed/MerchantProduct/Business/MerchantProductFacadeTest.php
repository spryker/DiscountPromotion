<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MerchantProduct\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Generated\Shared\Transfer\MerchantProductTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MerchantProduct
 * @group Business
 * @group Facade
 * @group MerchantProductFacadeTest
 *
 * Add your own group annotations below this line
 */
class MerchantProductFacadeTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\MerchantProduct\MerchantProductBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testFindMerchantReturnsMerchant(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();
        $expectedMerchantTransfer = $this->tester->haveMerchant();
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $expectedMerchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
        ]);

        // Act
        $merchantTransfer = $this->tester->getFacade()->findMerchant(
            (new MerchantProductCriteriaTransfer())->setIdProductAbstract($productAbstractTransfer->getIdProductAbstract())
        );

        // Assert
        $this->assertEquals($expectedMerchantTransfer->getIdMerchant(), $merchantTransfer->getIdMerchant());
        $this->assertEquals($expectedMerchantTransfer->getName(), $merchantTransfer->getName());
    }

    /**
     * @return void
     */
    public function testFindMerchantForNotExistingMerchantProductReturnsNull(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();

        // Act
        $merchantTransfer = $this->tester->getFacade()->findMerchant(
            (new MerchantProductCriteriaTransfer())->setIdProductAbstract(1)
        );

        // Assert
        $this->assertNull($merchantTransfer);
    }

    /**
     * @return void
     */
    public function testGetByIdProductAbstract(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();
        $merchantTransfer = $this->tester->haveMerchant();
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
        ]);
        $merchantProductCriteriaTransfer = (new MerchantProductCriteriaTransfer())
            ->setIdProductAbstract($productAbstractTransfer->getIdProductAbstract());

        // Act
        $merchantProductCollectionTransfer = $this->tester->getFacade()->get($merchantProductCriteriaTransfer);

        // Assert
        $this->assertCount(1, $merchantProductCollectionTransfer->getMerchantProducts());
    }

    /**
     * @return void
     */
    public function testGetByIdMerchantProductAbstracts(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();
        $merchantTransfer = $this->tester->haveMerchant();
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $productAbstractTransfer2 = $this->tester->haveProductAbstract();
        $merchantProductTransfer1 = $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
        ]);
        $merchantProductTransfer2 = $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer2->getIdProductAbstract(),
        ]);
        $merchantProductCriteriaTransfer = (new MerchantProductCriteriaTransfer())
            ->addMerchantProductAbstractId($merchantProductTransfer1->getIdMerchantProductAbstract())
            ->addMerchantProductAbstractId($merchantProductTransfer2->getIdMerchantProductAbstract());

        // Act
        $merchantProductCollectionTransfer = $this->tester->getFacade()->get($merchantProductCriteriaTransfer);

        // Assert
        $this->assertCount(2, $merchantProductCollectionTransfer->getMerchantProducts());
    }

    /**
     * @return void
     */
    public function testGetByIdMerchants(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();
        $merchantTransfer = $this->tester->haveMerchant();
        $merchantTransfer2 = $this->tester->haveMerchant();
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $productAbstractTransfer2 = $this->tester->haveProductAbstract();
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
        ]);
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer2->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer2->getIdProductAbstract(),
        ]);
        $merchantProductCriteriaTransfer = (new MerchantProductCriteriaTransfer())
            ->addIdMerchant($merchantTransfer->getIdMerchant())
            ->addIdMerchant($merchantTransfer2->getIdMerchant());

        // Act
        $merchantProductCollectionTransfer = $this->tester->getFacade()->get($merchantProductCriteriaTransfer);

        // Assert
        $this->assertCount(2, $merchantProductCollectionTransfer->getMerchantProducts());
    }
}
