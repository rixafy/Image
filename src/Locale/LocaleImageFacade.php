<?php

declare(strict_types=1);

namespace Rixafy\Image\LocaleImage;

use Doctrine\ORM\EntityManagerInterface;
use Rixafy\Image\ImageData;
use Rixafy\Image\ImageRenderer;
use Rixafy\Image\ImageStorage;
use Rixafy\Image\LocaleImage\Exception\LocaleImageNotFoundException;

class LocaleImageFacade
{
    /** @var ImageStorage */
    private $imageStorage;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LocaleImageRepository */
    private $localeImageRepository;

    /** @var ImageRenderer */
    private $localeImageRenderer;

    /** @var LocaleImageFactory */
    private $localeImageFactory;

    /**
     * LocaleImageFacade constructor.
     * @param ImageStorage $imageStorage
     * @param EntityManagerInterface $entityManager
     * @param LocaleImageRepository $localeImageRepository
     * @param ImageRenderer $imageRenderer
     * @param LocaleImageFactory $localeImageFactory
     */
    public function __construct(
        ImageStorage $imageStorage,
        EntityManagerInterface $entityManager,
        LocaleImageRepository $localeImageRepository,
        ImageRenderer $imageRenderer,
        LocaleImageFactory $localeImageFactory
    ) {
        $this->imageStorage = $imageStorage;
        $this->entityManager = $entityManager;
        $this->localeImageRepository = $localeImageRepository;
        $this->localeImageRenderer = $imageRenderer;
        $this->localeImageFactory = $localeImageFactory;
    }

    /**
     * @param ImageData $imageData
     * @return LocaleImage
     * @throws \Rixafy\Image\Exception\ImageSaveException
     */
    public function create(ImageData $imageData): LocaleImage
    {
        $localeImage = $this->localeImageFactory->create($imageData);

        $this->imageStorage->save($imageData->file, (string) $localeImage->getId());

        $this->entityManager->persist($localeImage);
        $this->entityManager->flush();

        return $localeImage;
    }

    /**
     * @param string $id
     * @param ImageData $imageData
     * @return LocaleImage
     * @throws Exception\LocaleImageNotFoundException
     */
    public function edit(string $id, ImageData $imageData): LocaleImage
    {
        $localeImage = $this->localeImageRepository->get($id);
        $localeImage->edit($imageData);

        $this->entityManager->flush();

        return $localeImage;
    }

    /**
     * @param string $id
     * @return LocaleImage
     * @throws LocaleImageNotFoundException
     */
    public function get(string $id): LocaleImage
    {
        return $this->localeImageRepository->get($id);
    }

    /**
     * Permanent, removes localeImage from database and disk
     *
     * @param string $id
     * @throws LocaleImageNotFoundException
     * @throws \Rixafy\Image\Exception\ImageNotFoundException
     */
    public function remove(string $id): void
    {
        $entity = $this->get($id);

        $this->imageStorage->remove($entity->getRealPath());
        $this->entityManager->remove($entity);

        $this->entityManager->flush();
    }
}