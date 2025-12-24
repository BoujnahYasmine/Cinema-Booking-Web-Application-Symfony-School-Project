<?php

namespace App\Twig;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private AssetMapperInterface $assetMapper
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset_mapper_path', [$this, 'getAssetMapperPath']),
        ];
    }

    public function getAssetMapperPath(string $path): string
    {
        $asset = $this->assetMapper->getAsset($path);
        if ($asset) {
            return $asset->publicPath;
        }
        return '/assets/' . $path;
    }
}

