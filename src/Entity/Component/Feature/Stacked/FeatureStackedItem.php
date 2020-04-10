<?php

declare(strict_types=1);

namespace Silverback\ApiComponentBundle\Entity\Component\Feature\Stacked;

use Doctrine\ORM\Mapping as ORM;
use Silverback\ApiComponentBundle\Entity\Component\Feature\AbstractFeatureItem;
use Silverback\ApiComponentBundle\Entity\Component\FileInterface;
use Silverback\ApiComponentBundle\Entity\Component\FileTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Silverback\ApiComponentBundle\Dto\File\FileData;

/**
 * @ORM\Entity()
 */
class FeatureStackedItem extends AbstractFeatureItem implements FileInterface
{
    use FileTrait;

    /**
     * @ORM\Column()
     * @Groups({"component", "content"})
     * @var null|string
     */
    protected $buttonText;

    /**
     * @ORM\Column()
     * @Groups({"component", "content"})
     * @var null|string
     */
    protected $buttonClass;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint(
            'description',
            new Assert\NotBlank()
        );
        $metadata->addPropertyConstraint(
            'filePath',
            new Assert\Image()
        );
    }

    /**
     * @return null|string
     */
    public function getButtonText(): ?string
    {
        return $this->buttonText;
    }

    public function setButtonText(?string $buttonText): self
    {
        $this->buttonText = $buttonText;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getButtonClass(): ?string
    {
        return $this->buttonClass;
    }

    public function setButtonClass(?string $buttonClass): self
    {
        $this->buttonClass = $buttonClass;
        return $this;
    }
}
