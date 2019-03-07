<?php

declare(strict_types=1);

namespace Rixafy\Image;

use Doctrine\Common\Collections\Criteria;
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
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $real_path;

    /**
     * @ORM\Column(type="smallint")
     * @var int
     */
    private $width;

    /**
     * @ORM\Column(type="smallint")
     * @var int
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=5)
     * @var string
     */
    private $file_extension;

    /**
     * One Image has Many Translations
     *
     * @ORM\OneToMany(targetEntity="\Rixafy\Image\ImageTranslation", mappedBy="entity", cascade={"persist", "remove"})
     * @var ImageTranslation[]
     */
    private $translations;

    public function __construct(ImageData $imageData)
    {
        $this->real_path = $imageData->realPath;
        $this->width = $imageData->width;
        $this->height = $imageData->height;
        $this->file_extension = $imageData->fileExtension;
        $this->translations = new ArrayCollection();

        $this->edit($imageData);
    }

    public function edit(ImageData $imageData)
    {
        if ($imageData->language !== null) {
            if ($this->fallback_language === null) {
                $this->addTranslation($imageData, $imageData->language);

            } else {
                $criteria = Criteria::create()
                    ->where(Criteria::expr()->eq('language', $imageData->language))
                    ->setMaxResults(1);

                /** @var ImageTranslation $translation */
                $translation = $this->translations->matching($criteria);

                if ($translation !== null) {
                    $translation->edit($imageData);

                } else {
                    $this->addTranslation($imageData, $imageData->language);
                }
            }
        }

        $this->url_name = $imageData->urlName;
        $this->description = $imageData->description;
        $this->title = $imageData->title;
        $this->alternative_text = $imageData->alternativeText;
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
     * @return string
     */
    public function getRealPath(): string
    {
        return $this->real_path;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return $this->file_extension;
    }

    /**
     * @return ImageTranslation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param ImageData $imageData
     * @param Language $language
     * @return ImageTranslation
     */
    public function addTranslation(ImageData $imageData, Language $language): ImageTranslation
    {
        $translation = new ImageTranslation($imageData, $language, $this);

        $this->translations->add($translation);

        if ($this->fallback_language === null) {
            $this->configureFallbackLanguage($language);
        }

        return $translation;
    }
}