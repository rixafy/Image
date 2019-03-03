<?php

declare(strict_types=1);

namespace Rixafy\Image;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\Uuid;
use Rixafy\Image\Exception\ImageNotFoundException;

class ImageRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository()
    {
        return $this->entityManager->getRepository(Image::class);
    }

    /**
     * @param string $urlName
     * @return Image|object
     * @throws ImageNotFoundException
     */
    public function getByUrlName(string $urlName): Image
    {
        $image = $this->getRepository()->findOneBy([
            'url_name' => $urlName
        ]);

        if ($image === null) {
            throw new ImageNotFoundException('Image with id ' . $urlName . ' not found.');
        }

        return $image;
    }

    /**
     * @param string $id
     * @return Image
     * @throws ImageNotFoundException
     */
    public function get(string $id): Image
    {
        $image = $this->find($id);

        if ($image === null) {
            throw new ImageNotFoundException('Image with id ' . $id . ' not found.');
        }

        return $image;
    }

    public function find(string $id): ?Image
    {
        return $this->getQueryBuilderForAll()
            ->andWhere('i.id = :id')->setParameter('id', Uuid::fromString($id))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getQueryBuilderForAll(): QueryBuilder
    {
        return $this->getRepository()->createQueryBuilder('i')
            ->where('i.is_active = :active')->setParameter('active', true)
            ->orderBy('i.created_at');
    }
}