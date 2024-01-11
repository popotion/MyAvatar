<?php

namespace App\Controller\Front;

use App\Controller\Front\RouteCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/avatar/{id}',
    name: RouteCollection::AVATAR->value,
    methods: ['GET']
)]
class AvatarController extends AbstractController
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly Packages $packages,
    ) {
    }

    public function __invoke(
        string $id,
    ): ?BinaryFileResponse {
        $imageUrl = $this->getParameter('profilePictureDirectory') . '/' . $id;

        if ($this->filesystem->exists($imageUrlWithExtension = $imageUrl . '.jpeg')) {
            return new BinaryFileResponse(
                $imageUrlWithExtension
            );
        }

        if ($this->filesystem->exists($imageUrlWithExtension = $imageUrl . '.jpg')) {
            return new BinaryFileResponse(
                $imageUrlWithExtension
            );
        }

        if ($this->filesystem->exists($imageUrlWithExtension = $imageUrl . '.png')) {
            return new BinaryFileResponse(
                $imageUrlWithExtension
            );
        }

        return new BinaryFileResponse(
            $this->getParameter('publicDirectory') . '/build/static/img/default.jpg',
        );
    }
}