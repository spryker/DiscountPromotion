<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentCartConnector\Business\StrategyResolver;

use Spryker\Zed\ShipmentCartConnector\Business\Cart\ShipmentCartExpanderInterface;

/**
 * @deprecated Remove strategy resolver after multiple shipment will be released.
 */
interface CartExpanderStrategyResolverInterface
{
    public const STRATEGY_KEY_WITHOUT_MULTI_SHIPMENT = 'STRATEGY_KEY_WITHOUT_MULTI_SHIPMENT';
    public const STRATEGY_KEY_WITH_MULTI_SHIPMENT = 'STRATEGY_KEY_WITH_MULTI_SHIPMENT';

    /**
     * @return \Spryker\Zed\ShipmentCartConnector\Business\Cart\ShipmentCartExpanderInterface
     */
    public function resolve(): ShipmentCartExpanderInterface;
}