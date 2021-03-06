<?php

/*
 * This file is part of the Silverback API Components Bundle Project
 *
 * (c) Daniel West <daniel@silverback.is>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Silverback\ApiComponentsBundle\Exception;

use Symfony\Component\Mailer\Exception\TransportException;

/**
 * @author Daniel West <daniel@silverback.is>
 */
class MailerTransportException extends TransportException
{
}
