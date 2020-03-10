<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Comment;

use Spryker\Client\Kernel\AbstractBundleConfig;

/**
 * @method \Spryker\Shared\Comment\CommentConfig getSharedConfig()
 */
class CommentConfig extends AbstractBundleConfig
{
    /**
     * @api
     *
     * @return string[]
     */
    public function getAvailableCommentTags(): array
    {
        return $this->getSharedConfig()->getAvailableCommentTags();
    }
}
