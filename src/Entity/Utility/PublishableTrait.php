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

namespace Silverback\ApiComponentBundle\Entity\Utility;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
trait PublishableTrait
{
    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    private ?\DateTimeInterface $publishedAt = null;

    private ?self $publishedResource = null;

    /** @return static */
    public function setPublishedAt(?\DateTimeInterface $publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function isPublished(): bool
    {
        return null !== $this->publishedAt;
    }

    /** @return static */
    public function setPublishedResource($publishedResource)
    {
        $this->publishedResource = $publishedResource;

        return $this;
    }

    public function getPublishedResource(): ?self
    {
        return $this->publishedResource;
    }
}
