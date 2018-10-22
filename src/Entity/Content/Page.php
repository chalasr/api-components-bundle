<?php

namespace Silverback\ApiComponentBundle\Entity\Content;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Silverback\ApiComponentBundle\Entity\Route\RouteAwareInterface;
use Silverback\ApiComponentBundle\Entity\Route\RouteAwareTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Page
 * @package Silverback\ApiComponentBundle\Entity\Content
 * @author Daniel West <daniel@silverback.is>
 * @ORM\Entity()
 */
class Page extends AbstractContent implements RouteAwareInterface
{
    use RouteAwareTrait;
    use PageTrait;

    /**
     * @ApiProperty()
     * @Groups({"content","route"})
     */
    public function isDynamic()
    {
        return false;
    }

    /**
     * @Groups({"default"})
     */
    protected $componentLocations;

    public function __construct()
    {
        parent::__construct();
        $this->routes = new ArrayCollection;
    }
}
