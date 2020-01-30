<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Oauth\Business;

use Codeception\Test\Unit;
use DateInterval;
use Generated\Shared\Transfer\CustomerIdentifierTransfer;
use Generated\Shared\Transfer\OauthAccessTokenValidationRequestTransfer;
use Generated\Shared\Transfer\OauthClientTransfer;
use Generated\Shared\Transfer\OauthGrantTypeConfigurationTransfer;
use Generated\Shared\Transfer\OauthRequestTransfer;
use Generated\Shared\Transfer\OauthScopeTransfer;
use Generated\Shared\Transfer\OauthUserTransfer;
use Generated\Shared\Transfer\RevokeRefreshTokenRequestTransfer;
use Orm\Zed\Oauth\Persistence\SpyOauthClientQuery;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Oauth\OauthConstants;
use Spryker\Zed\Oauth\Business\Model\League\Grant\PasswordGrantType;
use Spryker\Zed\Oauth\Business\OauthBusinessFactory;
use Spryker\Zed\Oauth\Business\OauthFacade;
use Spryker\Zed\Oauth\Business\OauthFacadeInterface;
use Spryker\Zed\Oauth\OauthConfig;
use Spryker\Zed\Oauth\OauthDependencyProvider;
use Spryker\Zed\OauthExtension\Dependency\Plugin\OauthGrantTypeConfigurationProviderPluginInterface;
use Spryker\Zed\OauthExtension\Dependency\Plugin\OauthUserProviderPluginInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Oauth
 * @group Business
 * @group Facade
 * @group OauthFacadeTest
 * Add your own group annotations below this line
 */
class OauthFacadeTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\Oauth\OauthBusinessTester
     */
    protected $tester;

    /**
     * @var \Generated\Shared\Transfer\CustomerTransfer
     */
    protected $customerTransfer;

    /**
     * @var \Spryker\Zed\Oauth\OauthConfig|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $oauthConfigMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->customerTransfer = $this->tester->haveCustomer();
        $this->setUpOauthConfigMock();
    }

    /**
     * @return void
     */
    public function testAccessTokenShouldReturnSuccessWhenValid(): void
    {
        $this->createTestClient();
        $this->setUserProviderPluginMock();
        $this->setGrantTypeConfigurationProviderPluginMock();

        $oauthRequestTransfer = $this->createOauthRequestTransfer();
        $oauthResponseTransfer = $this->getOauthFacade()->processAccessTokenRequest($oauthRequestTransfer);

        $this->assertTrue($oauthResponseTransfer->getIsValid());
    }

    /**
     * @return void
     */
    public function testAccessTokenShouldReturnFailureWhenClientCredentialsInValid(): void
    {
        $this->createTestClient();
        $this->setUserProviderPluginMock();

        $oauthRequestTransfer = new OauthRequestTransfer();
        $oauthRequestTransfer
            ->setGrantType('password')
            ->setClientId('frontend')
            ->setClientSecret('abc1232');

        $oauthResponseTransfer = $this->getOauthFacade()->processAccessTokenRequest($oauthRequestTransfer);

        $this->assertFalse($oauthResponseTransfer->getIsValid());
    }

    /**
     * @return void
     */
    public function testValidateAccessTokenShouldSuccessWithValidToken(): void
    {
        $this->createTestClient();
        $this->setUserProviderPluginMock();
        $this->setGrantTypeConfigurationProviderPluginMock();

        $oauthRequestTransfer = $this->createOauthRequestTransfer();
        $oauthResponseTransfer = $this->getOauthFacade()->processAccessTokenRequest($oauthRequestTransfer);

        $oauthAccessTokenValidationRequestTransfer = new OauthAccessTokenValidationRequestTransfer();
        $oauthAccessTokenValidationRequestTransfer
            ->setAccessToken($oauthResponseTransfer->getAccessToken())
            ->setType('Bearer');

        $oauthAccessTokenValidationResponseTransfer = $this->getOauthFacade()
            ->validateAccessToken($oauthAccessTokenValidationRequestTransfer);

        $this->assertTrue($oauthAccessTokenValidationResponseTransfer->getIsValid());
    }

    /**
     * @return void
     */
    public function testValidateAccessTokenShouldFailedWithInValidToken(): void
    {
        $oauthAccessTokenValidationRequestTransfer = new OauthAccessTokenValidationRequestTransfer();
        $oauthAccessTokenValidationRequestTransfer
            ->setAccessToken('wrong')
            ->setType('Bearer');

        $oauthAccessTokenValidationResponseTransfer = $this->getOauthFacade()
            ->validateAccessToken($oauthAccessTokenValidationRequestTransfer);

        $this->assertFalse($oauthAccessTokenValidationResponseTransfer->getIsValid());
    }

    /**
     * @return void
     */
    public function testSaveScopeShouldPersist(): void
    {
        $oauthScopeTransfer = (new OauthScopeTransfer())
            ->setIdentifier('identifier')
            ->setDescription('test scope');

        $oauthScopeTransfer = $this->getOauthFacade()->saveScope($oauthScopeTransfer);

        $this->assertNotEmpty($oauthScopeTransfer->getIdOauthScope());
    }

    /**
     * @return void
     */
    public function testSaveClientShouldPersist(): void
    {
        $oauthClientTransfer = (new OauthClientTransfer())
            ->setIdentifier('identifier')
            ->setName('client name')
            ->setSecret('secret')
            ->setIsConfidential(true)
            ->setRedirectUri('url');

        $oauthClientTransfer = $this->getOauthFacade()->saveClient($oauthClientTransfer);

        $this->assertNotEmpty($oauthClientTransfer->getIdOauthClient());
    }

    /**
     * @return void
     */
    public function testFindClientByIdentifier(): void
    {
        $oauthClientTransfer = (new OauthClientTransfer())
            ->setIdentifier('identifier')
            ->setName('client name')
            ->setSecret('secret')
            ->setIsConfidential(true)
            ->setRedirectUri('url');

        $this->getOauthFacade()->saveClient($oauthClientTransfer);

        $oauthClientTransfer = $this->getOauthFacade()->findClientByIdentifier($oauthClientTransfer);

        $this->assertNotNull($oauthClientTransfer);
    }

    /**
     * @return void
     */
    public function testFindScopeByIdentifier(): void
    {
        $oauthScopeTransfer = (new OauthScopeTransfer())
            ->setIdentifier('identifier')
            ->setDescription('test scope');

        $this->getOauthFacade()->saveScope($oauthScopeTransfer);

        $oauthScopeTransfer = $this->getOauthFacade()->findScopeByIdentifier($oauthScopeTransfer);

        $this->assertNotEmpty($oauthScopeTransfer->getIdOauthScope());
    }

    /**
     * @return void
     */
    public function testFindScopesByIdentifiers(): void
    {
        $identifiers = ['identifier', 'test_identifier'];

        $this->getOauthFacade()->saveScope(
            (new OauthScopeTransfer())
            ->setIdentifier($identifiers[0])
            ->setDescription('scope')
        );

        $this->getOauthFacade()->saveScope(
            (new OauthScopeTransfer())
                ->setIdentifier($identifiers[1])
                ->setDescription('test scope')
        );

        $oauthScopeTransfers = $this->getOauthFacade()->getScopesByIdentifiers($identifiers);

        foreach ($oauthScopeTransfers as $oauthScopeTransfer) {
            $this->assertNotEmpty($oauthScopeTransfer->getIdOauthScope());
        }
    }

    /**
     * @return void
     */
    public function testRevokeConcreteRefreshTokenShouldSuccessWithValidToken(): void
    {
        // Arrange
        $this->createTestClient();
        $this->setUserProviderPluginMock();
        $this->setGrantTypeConfigurationProviderPluginMock();
        $oauthRequestTransfer = $this->createOauthRequestTransfer();
        $oauthResponseTransfer = $this->getOauthFacade()->processAccessTokenRequest($oauthRequestTransfer);
        $customerTransfer = $this->tester->haveCustomer()
            ->setCustomerReference($oauthResponseTransfer->getCustomerReference());

        $revokeRefreshTokenRequestTransfer = (new RevokeRefreshTokenRequestTransfer())
            ->setCustomer($customerTransfer)
            ->setRefreshToken($oauthResponseTransfer->getRefreshToken());

        // Act
        $revokerRefreshTokenResponseTransfer = $this->getOauthFacade()->revokeConcreteRefreshToken($revokeRefreshTokenRequestTransfer);

        // Assert
        $this->assertTrue($revokerRefreshTokenResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testRevokeConcreteRefreshTokenShouldFailedWithInvalidToken(): void
    {
        // Arrange
        $oauthRequestTransfer = (new RevokeRefreshTokenRequestTransfer())
            ->setCustomer($this->customerTransfer)
            ->setRefreshToken('test');

        // Act
        $revokerRefreshTokenResponseTransfer = $this->getOauthFacade()->revokeConcreteRefreshToken($oauthRequestTransfer);

        // Assert
        $this->assertFalse($revokerRefreshTokenResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testRevokeRefreshTokensByCustomerShouldSuccessWithValidCustomer(): void
    {
        // Arrange
        $revokeRefreshTokenRequestTransfer = (new RevokeRefreshTokenRequestTransfer())
            ->setCustomer($this->customerTransfer);

        // Act
        $revokerRefreshTokenResponseTransfer = $this->getOauthFacade()->revokeRefreshTokensByCustomer($revokeRefreshTokenRequestTransfer);

        // Assert
        $this->assertTrue($revokerRefreshTokenResponseTransfer->getIsSuccessful());
    }

    /**
     * @return array
     */
    public function dependencyOfRefreshTokenRetentionIntervalToRefreshTokenCountDataProvider()
    {
        return [
            ['P2M', 0],
            ['PT0M', 1],
        ];
    }

    /**
     * @dataProvider dependencyOfRefreshTokenRetentionIntervalToRefreshTokenCountDataProvider
     *
     * @param string $interval
     * @param int $matches
     *
     * @return void
     */
    public function testCleanExpiredRefreshTokens(string $interval, int $matches): void
    {
        // Arrange
        $this->tester->deleteAllOauthRefreshTokens();
        $this->oauthConfigMock->method('getRefreshTokenRetentionInterval')->willReturn(new DateInterval($interval));
        $this->setUserProviderPluginMock();
        $oauthRequestTransfer = $this->createOauthRequestTransfer();
        $this->getOauthFacade()->processAccessTokenRequest($oauthRequestTransfer);

        // Act
        $this->getOauthFacade()->cleanExpiredRefreshTokens();

        // Assert
        $this->assertEquals($matches, $this->tester->getOauthRefreshTokensCount());
    }

    /**
     * @return void
     */
    protected function setUserProviderPluginMock(): void
    {
        $userProviderPluginMock = $this->getMockBuilder(OauthUserProviderPluginInterface::class)
            ->setMethods(['getUser', 'accept'])
            ->getMock();

        $userProviderPluginMock->method('getUser')->willReturnCallback(
            function (OauthUserTransfer $oauthUserTransfer) {
                $oauthUserTransfer->setIsSuccess(true)
                    ->setUserIdentifier(
                        json_encode(
                            (new CustomerIdentifierTransfer())
                                ->setCustomerReference('DE--test')
                                ->setIdCustomer(999)
                                ->toArray()
                        )
                    );

                return $oauthUserTransfer;
            }
        );

        $userProviderPluginMock
            ->method('accept')
            ->willReturn(true);

        $this->tester->setDependency(
            OauthDependencyProvider::PLUGIN_USER_PROVIDER,
            [
                $userProviderPluginMock,
            ]
        );
    }

    /**
     * @return void
     */
    protected function setGrantTypeConfigurationProviderPluginMock(): void
    {
        $grantTypeConfigurationProviderPluginMock = $this->getMockBuilder(OauthGrantTypeConfigurationProviderPluginInterface::class)
            ->setMethods(['getGrantTypeConfiguration'])
            ->getMock();

        $grantTypeConfigurationProviderPluginMock->method('getGrantTypeConfiguration')->willReturnCallback(
            function () {
                $oauthGrantTypeConfigurationTransfer = (new OauthGrantTypeConfigurationTransfer())
                    ->setIdentifier('password')
                    ->setFullyQualifiedClassName(PasswordGrantType::class);

                return $oauthGrantTypeConfigurationTransfer;
            }
        );

        $this->tester->setDependency(
            OauthDependencyProvider::PLUGINS_GRANT_TYPE_CONFIGURATION_PROVIDER,
            [
                $grantTypeConfigurationProviderPluginMock,
            ]
        );
    }

    /**
     * @return void
     */
    protected function createTestClient(): void
    {
        $oauthClientEntity = SpyOauthClientQuery::create()
            ->filterByIdentifier('frontend')
            ->findOneOrCreate();

        $oauthClientEntity
            ->setName('frontend api client')
            ->setSecret('$2y$10$gkKxj9iHzIAtza98kT4Ipe0/bxHV1XIEvLROcqaC6YdHJThUFrexS')
            ->setIsConfidential(true)
            ->save();
    }

    /**
     * @return void
     */
    protected function setUpOauthConfigMock(): void
    {
        $this->oauthConfigMock = $this->createMock(OauthConfig::class);
        $this->oauthConfigMock
            ->method('getPrivateKeyPath')->willReturn(Config::getInstance()->get(OauthConstants::PRIVATE_KEY_PATH));
        $this->oauthConfigMock
            ->method('getPublicKeyPath')->willReturn(Config::getInstance()->get(OauthConstants::PUBLIC_KEY_PATH));
        $this->oauthConfigMock
            ->method('getAccessTokenTTL')->willReturn('PT8H');
        $this->oauthConfigMock
            ->method('getRefreshTokenTTL')->willReturn('P1M');
    }

    /**
     * @return \Spryker\Zed\Oauth\Business\OauthFacadeInterface
     */
    protected function getOauthFacade(): OauthFacadeInterface
    {
        $businessFactory = (new OauthBusinessFactory())
            ->setConfig($this->oauthConfigMock);

        $facade = (new OauthFacade())
            ->setFactory($businessFactory);

        return $facade;
    }

    /**
     * @return \Generated\Shared\Transfer\OauthRequestTransfer
     */
    protected function createOauthRequestTransfer(): OauthRequestTransfer
    {
        $oauthRequestTransfer = new OauthRequestTransfer();
        $oauthRequestTransfer
            ->setGrantType('password')
            ->setClientId('frontend')
            ->setClientSecret('abc123')
            ->setUsername('spencor.hopkin@spryker.com')
            ->setPassword('change123');

        return $oauthRequestTransfer;
    }
}
