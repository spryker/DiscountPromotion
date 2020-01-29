<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DiscountPromotion\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\DiscountPromotionTransfer;
use Orm\Zed\DiscountPromotion\Persistence\SpyDiscountPromotion;

class DiscountPromotionMapper
{
    /**
     * @param \Orm\Zed\DiscountPromotion\Persistence\SpyDiscountPromotion $discountPromotionEntity
     * @param \Generated\Shared\Transfer\DiscountPromotionTransfer $discountPromotionTransfer
     *
     * @return \Generated\Shared\Transfer\DiscountPromotionTransfer
     */
    public function mapDiscountPromotionEntityToTransfer(
        SpyDiscountPromotion $discountPromotionEntity,
        DiscountPromotionTransfer $discountPromotionTransfer
    ): DiscountPromotionTransfer {
        return $discountPromotionTransfer->fromArray($discountPromotionEntity->toArray(), true);
    }

    /**
     * @param \Generated\Shared\Transfer\DiscountPromotionTransfer $discountPromotionTransfer
     * @param \Orm\Zed\DiscountPromotion\Persistence\SpyDiscountPromotion $discountPromotionEntity
     *
     * @return \Orm\Zed\DiscountPromotion\Persistence\SpyDiscountPromotion
     */
    public function mapDiscountPromotionTransferToEntity(
        DiscountPromotionTransfer $discountPromotionTransfer,
        SpyDiscountPromotion $discountPromotionEntity
    ): SpyDiscountPromotion {
        $discountPromotionEntity->fromArray($discountPromotionTransfer->toArray());

        return $discountPromotionEntity;
    }
}
