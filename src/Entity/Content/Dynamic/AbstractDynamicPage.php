<?php

namespace Silverback\ApiComponentBundle\Entity\Content\Dynamic;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Silverback\ApiComponentBundle\Entity\Content\AbstractContent;
use Silverback\ApiComponentBundle\Entity\Content\PageTrait;
use Silverback\ApiComponentBundle\Entity\Route\Route;
use Silverback\ApiComponentBundle\Entity\Route\RouteAwareInterface;
use Silverback\ApiComponentBundle\Entity\Route\RouteAwareTrait;
use Symfony\Component\Serializer\Annotation\Groups;

abstract class AbstractDynamicPage extends AbstractContent implements RouteAwareInterface
{
    use RouteAwareTrait;
    use PageTrait {
        getParentRoute as getParentParentRoute;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Silverback\ApiComponentBundle\Entity\Route\Route")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="route")
     * @var Route|null
     */
    protected $parentRoute;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     * @var boolean
     */
    protected $nested = false;

    public function __construct()
    {
        parent::__construct();
        $this->routes = new ArrayCollection;
    }

    /** @Groups({"dynamic_content", "route"}) */
    public function getComponentLocations(): Collection
    {
        return new ArrayCollection;
    }

    /**
     * @param null|Route $parentRoute
     */
    public function setParentRoute(?Route $parentRoute): void
    {
        $this->parentRoute = $parentRoute;
    }

    /**
     * @inheritdoc
     */
    public function getParentRoute(): ?Route
    {
        if ($this->parentRoute) {
            return $this->parentRoute;
        }
        if ($this->nested) {
            return $this->getParentParentRoute();
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isNested(): bool
    {
        return $this->nested;
    }

    /**
     * @param bool $nested
     */
    public function setNested(bool $nested): void
    {
        $this->nested = $nested;
    }
}
