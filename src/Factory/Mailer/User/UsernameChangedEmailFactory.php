<?php

/*
 * This file is part of the Silverback API Component Bundle Project
 *
 * (c) Daniel West <daniel@silverback.is>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Silverback\ApiComponentBundle\Factory\Mailer\User;

use Silverback\ApiComponentBundle\Entity\User\AbstractUser;
use Symfony\Component\Mime\RawMessage;

/**
 * @author Daniel West <daniel@silverback.is>
 */
final class UsernameChangedEmailFactory extends AbstractUserEmailFactory
{
    public function create(AbstractUser $user, array $context = []): ?RawMessage
    {
        if (!$this->enabled) {
            return null;
        }
        $this->initUser($user);

        return $this->createEmailMessage($context);
    }

    protected function getTemplate(): string
    {
        return 'user_username_changed.html.twig';
    }
}
