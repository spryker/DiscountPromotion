<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesReturn\Business\Writer;

use ArrayObject;
use Generated\Shared\Transfer\CreateReturnRequestTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\OrderItemFilterTransfer;
use Generated\Shared\Transfer\ReturnFilterTransfer;
use Generated\Shared\Transfer\ReturnResponseTransfer;
use Generated\Shared\Transfer\ReturnTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\SalesReturn\Business\Generator\ReturnReferenceGeneratorInterface;
use Spryker\Zed\SalesReturn\Business\Reader\ReturnReaderInterface;
use Spryker\Zed\SalesReturn\Business\Validator\ReturnValidatorInterface;
use Spryker\Zed\SalesReturn\Dependency\Facade\SalesReturnToSalesFacadeInterface;
use Spryker\Zed\SalesReturn\Persistence\SalesReturnEntityManagerInterface;

class ReturnWriter implements ReturnWriterInterface
{
    use TransactionTrait;

    protected const GLOSSARY_KEY_CREATE_RETURN_ITEM_REQUIRED_FIELDS_ERROR = 'return.create_return.validation.required_item_fields_error';

    /**
     * @var \Spryker\Zed\SalesReturn\Persistence\SalesReturnEntityManagerInterface
     */
    protected $salesReturnEntityManager;

    /**
     * @var \Spryker\Zed\SalesReturn\Business\Validator\ReturnValidatorInterface
     */
    protected $returnValidator;

    /**
     * @var \Spryker\Zed\SalesReturn\Business\Reader\ReturnReaderInterface
     */
    protected $returnReader;

    /**
     * @var \Spryker\Zed\SalesReturn\Business\Generator\ReturnReferenceGeneratorInterface
     */
    protected $returnReferenceGenerator;

    /**
     * @var \Spryker\Zed\SalesReturn\Dependency\Facade\SalesReturnToSalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @param \Spryker\Zed\SalesReturn\Persistence\SalesReturnEntityManagerInterface $salesReturnEntityManager
     * @param \Spryker\Zed\SalesReturn\Business\Validator\ReturnValidatorInterface $returnValidator
     * @param \Spryker\Zed\SalesReturn\Business\Reader\ReturnReaderInterface $returnReader
     * @param \Spryker\Zed\SalesReturn\Business\Generator\ReturnReferenceGeneratorInterface $returnReferenceGenerator
     * @param \Spryker\Zed\SalesReturn\Dependency\Facade\SalesReturnToSalesFacadeInterface $salesFacade
     */
    public function __construct(
        SalesReturnEntityManagerInterface $salesReturnEntityManager,
        ReturnValidatorInterface $returnValidator,
        ReturnReaderInterface $returnReader,
        ReturnReferenceGeneratorInterface $returnReferenceGenerator,
        SalesReturnToSalesFacadeInterface $salesFacade
    ) {
        $this->salesReturnEntityManager = $salesReturnEntityManager;
        $this->returnValidator = $returnValidator;
        $this->returnReader = $returnReader;
        $this->returnReferenceGenerator = $returnReferenceGenerator;
        $this->salesFacade = $salesFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\CreateReturnRequestTransfer $createReturnRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ReturnResponseTransfer
     */
    public function createReturn(CreateReturnRequestTransfer $createReturnRequestTransfer): ReturnResponseTransfer
    {
        $this->assertReturnRequirements($createReturnRequestTransfer);

        return $this->getTransactionHandler()->handleTransaction(function () use ($createReturnRequestTransfer) {
            return $this->executeCreateReturnTransaction($createReturnRequestTransfer);
        });
    }

    /**
     * @param \Generated\Shared\Transfer\CreateReturnRequestTransfer $createReturnRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ReturnResponseTransfer
     */
    protected function executeCreateReturnTransaction(CreateReturnRequestTransfer $createReturnRequestTransfer): ReturnResponseTransfer
    {
        if (!$this->checkReturnItemRequirements($createReturnRequestTransfer)) {
            return $this->createErrorReturnResponse(static::GLOSSARY_KEY_CREATE_RETURN_ITEM_REQUIRED_FIELDS_ERROR);
        }

        $orderItems = $this->getOrderItemsFromReturnRequest($createReturnRequestTransfer);
        $returnResponseTransfer = $this->returnValidator->validateReturnRequest($createReturnRequestTransfer, $orderItems);

        if (!$returnResponseTransfer->getIsSuccessful()) {
            return $returnResponseTransfer;
        }

        $returnTransfer = $this->createReturnTransfer($createReturnRequestTransfer);
        $returnTransfer = $this->createReturnItemTransfers($returnTransfer, $orderItems);

        // TODO: trigger event for return items, recalculate???

        return $this->returnReader->getReturn(
            (new ReturnFilterTransfer())->setReturnReference($returnTransfer->getReturnReference())
        );
    }

    /**
     * @param \Generated\Shared\Transfer\CreateReturnRequestTransfer $createReturnRequestTransfer
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[]
     */
    protected function getOrderItemsFromReturnRequest(CreateReturnRequestTransfer $createReturnRequestTransfer): ArrayObject
    {
        $orderItemFilterTransfer = $this->mapCreateReturnRequestTransferToOrderItemFilterTransfer(
            $createReturnRequestTransfer,
            new OrderItemFilterTransfer()
        );

        return $this->salesFacade
            ->getOrderItems($orderItemFilterTransfer)
            ->getItems();
    }

    /**
     * @param \Generated\Shared\Transfer\CreateReturnRequestTransfer $createReturnRequestTransfer
     * @param \Generated\Shared\Transfer\OrderItemFilterTransfer $orderItemFilterTransfer
     *
     * @return \Generated\Shared\Transfer\OrderItemFilterTransfer
     */
    protected function mapCreateReturnRequestTransferToOrderItemFilterTransfer(
        CreateReturnRequestTransfer $createReturnRequestTransfer,
        OrderItemFilterTransfer $orderItemFilterTransfer
    ): OrderItemFilterTransfer {
        $orderItemFilterTransfer->setCustomerReference(
            $createReturnRequestTransfer->getCustomer()->getCustomerReference()
        );

        foreach ($createReturnRequestTransfer->getReturnItems() as $returnItemTransfer) {
            $itemTransfer = $returnItemTransfer->getOrderItem();

            if ($itemTransfer->getUuid()) {
                $orderItemFilterTransfer->addSalesOrderItemUuid($itemTransfer->getUuid());

                continue;
            }

            $orderItemFilterTransfer->addSalesOrderItemId($itemTransfer->getIdSalesOrderItem());
        }

        return $orderItemFilterTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CreateReturnRequestTransfer $createReturnRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ReturnTransfer
     */
    protected function createReturnTransfer(CreateReturnRequestTransfer $createReturnRequestTransfer): ReturnTransfer
    {
        $returnTransfer = (new ReturnTransfer())
            ->setStore($createReturnRequestTransfer->getStore())
            ->setCustomerReference($createReturnRequestTransfer->getCustomer()->getCustomerReference())
            ->setReturnItems($createReturnRequestTransfer->getReturnItems());

        $returnReference = $this->returnReferenceGenerator->generateReturnReference($returnTransfer);

        $returnTransfer->setReturnReference($returnReference);

        return $this->salesReturnEntityManager->createReturn($returnTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ReturnTransfer $returnTransfer
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return \Generated\Shared\Transfer\ReturnTransfer
     */
    protected function createReturnItemTransfers(ReturnTransfer $returnTransfer, ArrayObject $itemTransfers): ReturnTransfer
    {
        $returnTransfer = $this->expandReturnItemsBeforeCreate($returnTransfer, $itemTransfers);

        foreach ($returnTransfer->getReturnItems() as $returnItemTransfer) {
            $this->salesReturnEntityManager->createReturnItem($returnItemTransfer);
        }

        return $returnTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CreateReturnRequestTransfer $createReturnRequestTransfer
     *
     * @return void
     */
    protected function assertReturnRequirements(CreateReturnRequestTransfer $createReturnRequestTransfer): void
    {
        $createReturnRequestTransfer
            ->requireReturnItems()
            ->requireStore()
            ->requireCustomer()
            ->getCustomer()
                ->requireCustomerReference();
    }

    /**
     * @param \Generated\Shared\Transfer\CreateReturnRequestTransfer $createReturnRequestTransfer
     *
     * @return bool
     */
    protected function checkReturnItemRequirements(CreateReturnRequestTransfer $createReturnRequestTransfer): bool
    {
        foreach ($createReturnRequestTransfer->getReturnItems() as $returnItemTransfer) {
            $returnItemTransfer->requireOrderItem();
            $itemTransfer = $returnItemTransfer->getOrderItem();

            if (!$itemTransfer->getIdSalesOrderItem() && !$itemTransfer->getUuid()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $message
     *
     * @return \Generated\Shared\Transfer\ReturnResponseTransfer
     */
    protected function createErrorReturnResponse(string $message): ReturnResponseTransfer
    {
        $messageTransfer = (new MessageTransfer())
            ->setValue($message);

        return (new ReturnResponseTransfer())
            ->setIsSuccessful(false)
            ->addMessage($messageTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ReturnTransfer $returnTransfer
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return \Generated\Shared\Transfer\ReturnTransfer
     */
    public function expandReturnItemsBeforeCreate(ReturnTransfer $returnTransfer, ArrayObject $itemTransfers): ReturnTransfer
    {
        $returnTransfer->requireIdSalesReturn();

        $indexedItemsById = $this->indexOrderItemsById($itemTransfers);
        $indexedItemsByUuid = $this->indexOrderItemsByUuid($itemTransfers);

        foreach ($returnTransfer->getReturnItems() as $returnItemTransfer) {
            $itemTransfer = $returnItemTransfer->getOrderItem();

            $returnItemTransfer->setIdSalesReturn($returnTransfer->getIdSalesReturn());
            $returnItemTransfer->setOrderItem(
                $indexedItemsById[$itemTransfer->getIdSalesOrderItem()] ?? $indexedItemsByUuid[$itemTransfer->getUuid()]
            );
        }

        return $returnTransfer;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    protected function indexOrderItemsByUuid(ArrayObject $itemTransfers): array
    {
        $indexedOrderItems = [];

        foreach ($itemTransfers as $itemTransfer) {
            $indexedOrderItems[$itemTransfer->getUuid()] = $itemTransfer;
        }

        return $indexedOrderItems;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    protected function indexOrderItemsById(ArrayObject $itemTransfers): array
    {
        $indexedOrderItems = [];

        foreach ($itemTransfers as $itemTransfer) {
            $indexedOrderItems[$itemTransfer->getIdSalesOrderItem()] = $itemTransfer;
        }

        return $indexedOrderItems;
    }
}
