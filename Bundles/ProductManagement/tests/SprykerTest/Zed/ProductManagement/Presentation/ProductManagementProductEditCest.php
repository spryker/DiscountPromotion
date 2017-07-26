<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductManagement\Presentation;

use SprykerTest\Zed\ProductManagement\PageObject\ProductManagementProductListPage;
use SprykerTest\Zed\ProductManagement\PresentationTester;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group ProductManagement
 * @group Presentation
 * @group ProductManagementProductEditCest
 * Add your own group annotations below this line
 */
class ProductManagementProductEditCest
{

    /**
     * @param \SprykerTest\Zed\ProductManagement\PresentationTester $i
     *
     * @return void
     */
    public function breadcrumbIsVisible(PresentationTester $i)
    {
        $i->amOnPage(ProductManagementProductListPage::URL);
        $i->wait(2);
        $i->click('(//a[contains(., "Edit")])[1]');

        $i->seeBreadcrumbNavigation('Dashboard / Products / Products / Edit Product');
    }

}
