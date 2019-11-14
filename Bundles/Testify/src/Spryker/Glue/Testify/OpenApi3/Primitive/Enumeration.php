<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Glue\Testify\OpenApi3\Primitive;

use Spryker\Glue\Testify\OpenApi3\Collection\AbstractCollection;
use Spryker\Glue\Testify\OpenApi3\Property\PropertyDefinition;

class Enumeration extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    public function getElementDefinition(): PropertyDefinition
    {
        return new PropertyDefinition(Any::class);
    }
}