<?php

declare(strict_types=1);

namespace Rixafy\Image\LocaleImage;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Rixafy\Image\ImageInterface;
use Rixafy\Translation\Annotation\Translatable;
use Doctrine\Common\Collections\ArrayCollection;
use Rixafy\DoctrineTraits\ActiveTrait;
use Rixafy\DoctrineTraits\DateTimeTrait;
use Rixafy\Image\ImageData;
use Rixafy\Image\ImagePropertiesTrait;
use Rixafy\Translation\EntityTranslator;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="locale_image")
 */
class LocaleImage extends EntityTranslator implements ImageInterface
{
    use ActiveTrait;
    use DateTimeTrait;
    use ImagePropertiesTrait;

	/**
	 * @var UuidInterface
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", unique=true)
	 */
	protected $id;

    /**
     * @Translatable
     * @var string
     */
    private $url_name;

    /**
     * @Translatable
     * @var string
     */
    private $description;

    /**
     * @Translatable
     * @var string
     */
    private $title;

    /**
     * @Translatable
     * @var string
     */
    private $alternative_text;

    /**
     * One LocaleImage has Many Translations
     *
     * @ORM\OneToMany(targetEntity="\Rixafy\Image\LocaleImage\LocaleImageTranslation", mappedBy="entity", cascade={"persist", "remove"})
     * @var LocaleImageTranslation[]
     */
    protected $translations;

    public function __construct(UuidInterface $id, ImageData $imageData)
    {
    	$this->id = $id;
        $this->image_group = $imageData->imageGroup;
        $this->translations = new ArrayCollection();

		[$this->width, $this->height] = getimagesize($imageData->file['tmp_name']);
		$this->file_extension = pathinfo($imageData->file['tmp_name'], PATHINFO_EXTENSION);

		$this->edit($imageData);
    }

    public function edit(ImageData $imageData): void
    {
        $this->editTranslation($imageData);
    }

    public function getId(): UuidInterface
	{
		return $this->id;
	}

    public function getData(): ImageData
    {
        $data = new ImageData();

        $data->description = $this->description;
        $data->title = $this->title;
        $data->alternativeText = $this->alternative_text;
        $data->language = $this->translationLanguage;

        return $data;
    }

    public function getUrlName(): string
    {
        return $this->url_name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAlternativeText(): string
    {
        return $this->alternative_text;
    }

    public function getTranslations()
    {
        return $this->translations;
    }
}
