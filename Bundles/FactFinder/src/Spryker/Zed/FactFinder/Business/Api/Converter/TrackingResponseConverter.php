<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\FactFinder\Business\Api\Converter;

use FACTFinder\Adapter\Tracking as FFTrackingAdapter;
use Generated\Shared\Transfer\FFTrackingResponseTransfer;

class TrackingResponseConverter extends BaseConverter
{

    /**
     * @var \FACTFinder\Adapter\Tracking
     */
    protected $trackingAdapter;

    /**
     * @param \FACTFinder\Adapter\Tracking $trackingAdapter
     */
    public function __construct(FFTrackingAdapter $trackingAdapter)
    {
        $this->trackingAdapter = $trackingAdapter;
    }

    /**
     * @return \Generated\Shared\Transfer\FFTrackingResponseTransfer
     */
    public function convert()
    {
        $responseTransfer = new FFTrackingResponseTransfer();
//        $responseTransfer->set();

        return $responseTransfer;
    }

}
