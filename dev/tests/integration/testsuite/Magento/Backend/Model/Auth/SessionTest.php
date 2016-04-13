<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Model\Auth;

/**
 * @magentoAppArea adminhtml
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Auth
     */
    private $auth;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    protected function setUp()
    {
        parent::setUp();
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->objectManager->get(\Magento\Framework\Config\ScopeInterface::class)
            ->setCurrentScope(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $this->auth = $this->objectManager->create(\Magento\Backend\Model\Auth::class);
        $this->authSession = $this->objectManager->create(\Magento\Backend\Model\Auth\Session::class);
        $this->auth->setAuthStorage($this->authSession);
    }

    protected function tearDown()
    {
        $this->objectManager->get(\Magento\Framework\Config\ScopeInterface::class)->setCurrentScope(null);
        $this->auth->logout();
    }

    /**
     * @dataProvider loginDataProvider
     * Disabled form security in order to prevent exit from the app
     * @magentoAdminConfigFixture admin/security/session_lifetime 100
     */
    public function testIsLoggedIn($loggedIn)
    {
        if ($loggedIn) {
            $this->auth->login(
                \Magento\TestFramework\Bootstrap::ADMIN_NAME,
                \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
            );
        }
        $this->assertEquals($loggedIn, $this->authSession->isLoggedIn());
    }

    public function loginDataProvider()
    {
        return [[true], [false]];
    }

    /**
     * Disabled form security in order to prevent exit from the app
     * @magentoConfigFixture current_store admin/security/session_lifetime 59
     */
    public function testIsLoggedInWithIgnoredLifetime()
    {
        $this->_auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
        $this->assertTrue($this->_model->isLoggedIn());

        $this->_model->setUpdatedAt(time() - 101);
        $this->assertTrue($this->_model->isLoggedIn());
    }
}
