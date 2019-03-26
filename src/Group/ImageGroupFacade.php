<?php

declare(strict_types=1);

namespace Rixafy\Image\ImageGroup;

use Doctrine\ORM\EntityManagerInterface;

class ImageGroupFacade
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ImageGroupRepository */
    private $imageGroupRepository;

    /** @var ImageGroupFactory */
    private $imageGroupFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        ImageGroupRepository $imageGroupRepository,
        ImageGroupFactory $imageGroupFactory
    ) {
        $this->entityManager = $entityManager;
        $this->imageGroupRepository = $imageGroupRepository;
        $this->imageGroupFactory = $imageGroupFactory;
    }

    /**
     * @param ImageGroupData $imageGroupData
     * @return ImageGroup
     */
    public function create(ImageGroupData $imageGroupData): ImageGroup
    {
        $imageGroup = $this->imageGroupFactory->create($imageGroupData);
        
        $this->entityManager->persist($imageGroup);
        $this->entityManager->flush();

        return $imageGroup;
    }

    /**
     * @param string $id
     * @param ImageGroupData $imageGroupData
     * @return ImageGroup
     * @throws Exception\ImageGroupNotFoundException
     */
    public function edit(string $id, ImageGroupData $imageGroupData): ImageGroup
    {
        $imageGroup = $this->imageGroupRepository->get($id);
        $imageGroup->edit($imageGroupData);

        $this->entityManager->flush();

        return $imageGroup;
    }

    /**
     * @param string $id
     * @return ImageGroup
     * @throws Exception\ImageGroupNotFoundException
     */
    public function get(string $id): ImageGroup
    {
        return $this->imageGroupRepository->get($id);
    }

    /**
     * Permanent removal
     *
     * @param string $id
     * @throws Exception\ImageGroupNotFoundException
     */
    public function remove(string $id): void
    {
        $entity = $this->get($id);

        $this->entityManager->remove($entity);

        $this->entityManager->flush();
    }

}