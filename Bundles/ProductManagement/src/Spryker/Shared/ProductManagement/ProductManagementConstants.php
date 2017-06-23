<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\ProductManagement;

interface ProductManagementConstants
{

    const PRODUCT_MANAGEMENT_ATTRIBUTE_GLOSSARY_PREFIX = 'product.attribute.';

    const PRODUCT_MANAGEMENT_DEFAULT_LOCALE = 'default';

    /** @deprecated Please use ProductManagementConstants::BASE_URL_YVES instead */
    const HOST_YVES = 'HOST_YVES';

    /**
     * Base URL for Yves including scheme and port (e.g. http://www.de.demoshop.local:8080)
     *
     * @api
     */
    const BASE_URL_YVES = 'PRODUCT_MANAGEMENT:BASE_URL_YVES';

}
