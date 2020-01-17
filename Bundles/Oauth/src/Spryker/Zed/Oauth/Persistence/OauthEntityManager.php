<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Oauth\Persistence;

use Generated\Shared\Transfer\OauthRefreshTokenTransfer;
use Generated\Shared\Transfer\SpyOauthAccessTokenEntityTransfer;
use Generated\Shared\Transfer\SpyOauthClientEntityTransfer;
use Generated\Shared\Transfer\SpyOauthScopeEntityTransfer;
use Orm\Zed\Oauth\Persistence\SpyOauthRefreshToken;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Spryker\Zed\Oauth\Persistence\OauthPersistenceFactory getFactory()
 */
class OauthEntityManager extends AbstractEntityManager implements OauthEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\SpyOauthAccessTokenEntityTransfer $spyOauthAccessTokenEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyOauthAccessTokenEntityTransfer
     */
    public function saveAccessToken(SpyOauthAccessTokenEntityTransfer $spyOauthAccessTokenEntityTransfer): SpyOauthAccessTokenEntityTransfer
    {
        /** @var \Generated\Shared\Transfer\SpyOauthAccessTokenEntityTransfer $spyOauthAccessTokenEntityTransfer */
        $spyOauthAccessTokenEntityTransfer = $this->save($spyOauthAccessTokenEntityTransfer);

        return $spyOauthAccessTokenEntityTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OauthRefreshTokenTransfer $oauthRefreshTokenTransfer
     *
     * @return \Generated\Shared\Transfer\OauthRefreshTokenTransfer
     */
    public function saveRefreshToken(OauthRefreshTokenTransfer $oauthRefreshTokenTransfer): OauthRefreshTokenTransfer
    {
        $oauthRefreshTokenMapper = $this->getFactory()->createOauthRefreshTokenMapper();
        $oauthRefreshTokenEntity = $oauthRefreshTokenMapper->mapOauthRefreshTokenTransferToOauthRefreshTokenEntity($oauthRefreshTokenTransfer, new SpyOauthRefreshToken());

        $oauthRefreshTokenEntity->save();

        return $oauthRefreshTokenMapper->mapOauthRefreshTokenEntityToMapOauthRefreshTokenTransfer($oauthRefreshTokenEntity, $oauthRefreshTokenTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\SpyOauthClientEntityTransfer $spyOauthClientEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyOauthClientEntityTransfer
     */
    public function saveClient(SpyOauthClientEntityTransfer $spyOauthClientEntityTransfer): SpyOauthClientEntityTransfer
    {
        /** @var \Generated\Shared\Transfer\SpyOauthClientEntityTransfer $spyOauthClientEntityTransfer */
        $spyOauthClientEntityTransfer = $this->save($spyOauthClientEntityTransfer);

        return $spyOauthClientEntityTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\SpyOauthScopeEntityTransfer $spyOauthScopeEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyOauthScopeEntityTransfer
     */
    public function saveScope(SpyOauthScopeEntityTransfer $spyOauthScopeEntityTransfer): SpyOauthScopeEntityTransfer
    {
        /** @var \Generated\Shared\Transfer\SpyOauthScopeEntityTransfer $spyOauthScopeEntityTransfer */
        $spyOauthScopeEntityTransfer = $this->save($spyOauthScopeEntityTransfer);

        return $spyOauthScopeEntityTransfer;
    }

    /**
     * @param string $identifier
     *
     * @return void
     */
    public function deleteAccessTokenByIdentifier(string $identifier): void
    {
        /** @var \Orm\Zed\Oauth\Persistence\SpyOauthAccessToken|null $authAccessTokenEntity */
        $authAccessTokenEntity = $this->getFactory()
            ->createAccessTokenQuery()
            ->findOneByIdentifier($identifier);

        if ($authAccessTokenEntity) {
            $authAccessTokenEntity->delete();
        }
    }
}
