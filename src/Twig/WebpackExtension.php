<?php

namespace Maba\Bundle\WebpackBundle\Twig;

use Twig_Extension as Extension;
use Twig_SimpleFunction as SimpleFunction;
use Maba\Bundle\WebpackBundle\Service\AssetManager;

class WebpackExtension extends Extension
{
    const FUNCTION_NAME = 'webpack_asset';
    const NAMED_ASSET_FUNCTION_NAME = 'webpack_named_asset';
    const TAG_NAME_STYLESHEETS = 'webpack_stylesheets';
    const TAG_NAME_JAVASCRIPTS = 'webpack_javascripts';
    const TAG_NAME_ASSETS = 'webpack_assets';

    protected $assetManager;
    protected $functionName;

    public function __construct(AssetManager $assetManager, $functionName = self::FUNCTION_NAME)
    {
        $this->assetManager = $assetManager;
        $this->functionName = $functionName;
    }

    public function getFunctions()
    {
        return array(
            new SimpleFunction($this->functionName, array($this, 'getAssetUrl')),
            new SimpleFunction(self::NAMED_ASSET_FUNCTION_NAME, array($this, 'getNamedAssetUrl')),
        );
    }

    public function getTokenParsers()
    {
        return array(
            new WebpackConcreteTokenParser(self::TAG_NAME_STYLESHEETS, $this->functionName, 'css'),
            new WebpackConcreteTokenParser(self::TAG_NAME_JAVASCRIPTS, $this->functionName, 'js'),
            new WebpackConcreteTokenParser(self::TAG_NAME_ASSETS, $this->functionName, null),
            new WebpackTokenParser($this->functionName, self::NAMED_ASSET_FUNCTION_NAME),
        );
    }

    /**
     * @param string $resource Path to resource. Can be begin with alias and be prefixed with loaders
     * @param string|null $type Type of asset. If null, type is guessed by extension
     * @param string|null $group Not used here - only used when parsing twig templates to group assets
     *
     * @return null|string
     */
    public function getAssetUrl($resource, $type = null, $group = null)
    {
        return $this->assetManager->getAssetUrl($resource, $type);
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return null|string
     */
    public function getNamedAssetUrl($name, $type = null)
    {
        return $this->assetManager->getNamedAssetUrl($name, $type);
    }

    public function getName()
    {
        return 'maba_webpack';
    }
}
