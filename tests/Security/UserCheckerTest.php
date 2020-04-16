<?php

declare(strict_types=1);

namespace Silverback\ApiComponentBundle\Tests\Security;

use Silverback\ApiComponentBundle\Entity\User\AbstractUser;
use Silverback\ApiComponentBundle\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Silverback\ApiComponentBundle\Tests\Functional\TestBundle\Entity\UnsupportedUser;
use Silverback\ApiComponentBundle\Tests\Functional\TestBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\DisabledException;

class UserCheckerTest extends TestCase
{
    public function test_check_post_auth_does_nothing_and_returns_nothing(): void
    {

        $userChecker = new UserChecker();
        $user = new User();
        $this->assertNull($userChecker->checkPostAuth($user));
    }

    public function test_pre_auth_does_nothing_if_object_is_not_user(): void
    {
        $userChecker = new UserChecker(true);
        $this->assertNull($userChecker->checkPreAuth(new UnsupportedUser()));
    }

    public function test_user_not_enabled_throws_exception(): void
    {
        $userChecker = new UserChecker(true);
        $user = new class extends AbstractUser{};

        $user->setEnabled(false);
        $this->expectException(DisabledException::class);
        $userChecker->checkPreAuth($user);

        $user->setEnabled(true);
        $this->expectException(DisabledException::class);
        $userChecker->checkPreAuth($user);
    }

    public function test_user_with_unverified_email_throws_exception(): void
    {
        $userChecker = new UserChecker(true);
        $user = new class extends AbstractUser{};

        $user->setEnabled(true);
        $user->setEmailAddressVerified(false);
        $this->expectException(DisabledException::class);
        $userChecker->checkPreAuth($user);
    }

    public function test_user_with_unverified_email_can_be_allowed_and_not_throw_exception(): void
    {
        $userChecker = new UserChecker(false);
        $user = new class extends AbstractUser{};

        $user->setEnabled(true);
        $user->setEmailAddressVerified(false);
        $this->assertNull($userChecker->checkPreAuth($user));
    }
}
