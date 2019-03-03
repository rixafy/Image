<?php

declare(strict_types=1);

namespace Rixafy\Image;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Rixafy\Doctrination\EntityTranslator;
use Rixafy\Doctrination\Language\Language;
use Rixafy\DoctrineTraits\ActiveTrait;
use Rixafy\DoctrineTraits\DateTimeTrait;
use Rixafy\DoctrineTraits\UniqueTrait;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="image")
 */
class Image extends EntityTranslator
{
    use UniqueTrait;
    use ActiveTrait;
    use DateTimeTrait;

    /**
     * @Translatable
     * @var string
     */
    protected $url_name;

    /**
     * @Translatable
     * @var string
     */
    protected $description;

    /**
     * @Translatable
     * @var string
     */
    protected $title;

    /**
     * @Translatable
     * @var string
     */
    protected $alternative_text;

    /**
     * One Image has Many Translations
     *
     * @ORM\OneToMany(targetEntity="\Rixafy\Image\ImageTranslation", mappedBy="entity", cascade={"persist", "remove"})
     * @var ImageTranslation[]
     */
    private $translations;

    public function __construct(ImageData $imageData)
    {
        $this->url_name = $imageData->urlName;
        $this->description = $imageData->description;
        $this->title = $imageData->title;
        $this->alternative_text = $imageData->alternativeText;

        $this->translations = new ArrayCollection();

        $this->addTranslation($this->url_name, $this->description, $this->title, $this->alternative_text, $imageData->language);

        $this->configureFallbackLanguage($imageData->language);
    }

    /**
     * @return string
     */
    public function getUrlName(): string
    {
        return $this->url_name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getAlternativeText(): string
    {
        return $this->alternative_text;
    }


    /**
     * @return ImageTranslation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param string $urlName
     * @param string $title
     * @param string $description
     * @param string $alternativeText
     * @param Language $language
     * @return ImageTranslation
     */
    public function addTranslation(string $urlName, string $description, string $title, string $alternativeText, Language $language): ImageTranslation
    {
        $translation = new ImageTranslation($urlName, $title, $description, $alternativeText, $language, $this);

        $this->translations->add($translation);

        return $translation;
    }
}