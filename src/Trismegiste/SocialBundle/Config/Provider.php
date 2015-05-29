<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Config;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Provider is a provider for dynamic config parameters coming from MongoDb
 */
class Provider implements CacheWarmerInterface, ProviderInterface
{

    const FILENAME = 'social_config.php';

    /** @var Trismegiste\Yuurei\Persistence\RepositoryInterface */
    protected $repo;
    protected $cacheDir;
    protected $defaultParam;
    // to prevent multiple loading :
    private $loadedConfig = null;

    /**
     * Ctor
     *
     * @param RepositoryInterface $repo
     * @param string $cache_dir
     * @param array $default
     */
    public function __construct(RepositoryInterface $repo, $cache_dir, array $default)
    {
        $this->repo = $repo;
        $this->cacheDir = $cache_dir;
        $this->defaultParam = $default;
    }

    /**
     * @inheritdoc
     */
    public function write(array $param)
    {
        $obj = $this->getUniqueInstance();
        $obj->data = $param;
        $this->repo->persist($obj);
        $this->dump($this->cacheDir, $param);
    }

    /**
     * @inheritdoc
     */
    public function read($forceReload = false)
    {
        if ($forceReload) {
            $cfg = $this->getUniqueInstance();
            $this->loadedConfig = $cfg->data;
        }

        if (is_null($this->loadedConfig)) {
            $this->loadedConfig = require_once $this->cacheDir . DIRECTORY_SEPARATOR . self::FILENAME;
        }

        return $this->loadedConfig;
    }

    /**
     * @inheritdoc
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function warmUp($cacheDir)
    {
        $c = $this->getUniqueInstance();
        $this->dump($cacheDir, $c->data);
    }

    protected function dump($cacheDir, array $obj)
    {
        file_put_contents($cacheDir . DIRECTORY_SEPARATOR . self::FILENAME
                , '<?php return ' . var_export($obj, true) . ';'
        );
    }

    /**
     * Get the unique entity in database (or create it)
     *
     * @return \Trismegiste\SocialBundle\Config\ParameterBag
     */
    protected function getUniqueInstance()
    {
        $singleton = $this->repo->findOne(['-class' => 'config']);

        if (is_null($singleton)) {
            $singleton = new ParameterBag($this->defaultParam);
        }

        return $singleton;
    }

}
