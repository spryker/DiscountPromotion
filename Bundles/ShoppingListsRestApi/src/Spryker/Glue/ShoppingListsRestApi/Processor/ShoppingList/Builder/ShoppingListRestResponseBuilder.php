<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ShoppingListsRestApi\Processor\ShoppingList\Builder;

use Generated\Shared\Transfer\RestShoppingListAttributesTransfer;
use Generated\Shared\Transfer\RestShoppingListCollectionResponseTransfer;
use Generated\Shared\Transfer\ShoppingListTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\ShoppingListsRestApi\Processor\Builder\RestResponseBuilder;
use Spryker\Glue\ShoppingListsRestApi\Processor\ShoppingList\Mapper\ShoppingListMapperInterface;
use Spryker\Glue\ShoppingListsRestApi\Processor\ShoppingListItem\Builder\ShoppingListItemRestResponseBuilderInterface;
use Spryker\Glue\ShoppingListsRestApi\ShoppingListsRestApiConfig;

class ShoppingListRestResponseBuilder extends RestResponseBuilder implements ShoppingListRestResponseBuilderInterface
{
    /**
     * @var \Spryker\Glue\ShoppingListsRestApi\Processor\Builder\RestResponseBuilderInterface
     */
    protected $restResponseBuilder;

    /**
     * @var \Spryker\Glue\ShoppingListsRestApi\Processor\ShoppingList\Mapper\ShoppingListMapperInterface
     */
    protected $shoppingListsResourceMapper;

    /**
     * @var \Spryker\Glue\ShoppingListsRestApi\Processor\ShoppingListItem\Builder\ShoppingListItemRestResponseBuilderInterface
     */
    protected $shoppingListItemRestResponseBuilder;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \Spryker\Glue\ShoppingListsRestApi\Processor\ShoppingList\Mapper\ShoppingListMapperInterface $shoppingListsResourceMapper
     * @param \Spryker\Glue\ShoppingListsRestApi\Processor\ShoppingListItem\Builder\ShoppingListItemRestResponseBuilderInterface $shoppingListItemRestResponseBuilder
     */
    public function __construct(
        RestResourceBuilderInterface $restResourceBuilder,
        ShoppingListMapperInterface $shoppingListsResourceMapper,
        ShoppingListItemRestResponseBuilderInterface $shoppingListItemRestResponseBuilder
    ) {
        parent::__construct($restResourceBuilder);

        $this->shoppingListsResourceMapper = $shoppingListsResourceMapper;
        $this->shoppingListItemRestResponseBuilder = $shoppingListItemRestResponseBuilder;
    }

    /**
     * @param \Generated\Shared\Transfer\ShoppingListTransfer $shoppingListTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function buildShoppingListRestResponse(
        ShoppingListTransfer $shoppingListTransfer
    ): RestResponseInterface {
        return $this->createRestResponse()->addResource($this->createShoppingListRestResource($shoppingListTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\RestShoppingListCollectionResponseTransfer $restShoppingListCollectionResponseTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function buildShoppingListCollectionRestResponse(
        RestShoppingListCollectionResponseTransfer $restShoppingListCollectionResponseTransfer
    ): RestResponseInterface {
        $restResponse = $this->createRestResponse();

        foreach ($restShoppingListCollectionResponseTransfer->getShoppingLists() as $shoppingListTransfer) {
            $restResponse->addResource($this->createShoppingListRestResource($shoppingListTransfer));
        }

        return $restResponse;
    }

    /**
     * @param \Generated\Shared\Transfer\ShoppingListTransfer $shoppingListTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface
     */
    protected function createShoppingListRestResource(
        ShoppingListTransfer $shoppingListTransfer
    ): RestResourceInterface {
        $restShoppingListsAttributesTransfer = $this->shoppingListsResourceMapper->mapShoppingListTransferToRestShoppingListsAttributesTransfer(
            $shoppingListTransfer,
            new RestShoppingListAttributesTransfer()
        );

        $shoppingListResource = $this->restResourceBuilder->createRestResource(
            ShoppingListsRestApiConfig::RESOURCE_SHOPPING_LISTS,
            $shoppingListTransfer->getUuid(),
            $restShoppingListsAttributesTransfer
        );

        $shoppingListItemResources = $this->shoppingListItemRestResponseBuilder->createShoppingListItemRestResourcesFromShoppingListTransfer(
            $shoppingListTransfer
        );

        foreach ($shoppingListItemResources as $relation) {
            $shoppingListResource->addRelationship($relation);
        }

        return $shoppingListResource;
    }
}
