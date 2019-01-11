<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Kernel;

use Spryker\Service\Container\Container as SprykerContainer;
use Spryker\Shared\Kernel\ContainerInterface;

class Container extends SprykerContainer implements ContainerInterface
{
    /**
     * @return \Generated\Yves\Ide\AutoCompletion|\Spryker\Shared\Kernel\LocatorLocatorInterface
     */
    public function getLocator()
    {
        return Locator::getInstance();
    }
}
