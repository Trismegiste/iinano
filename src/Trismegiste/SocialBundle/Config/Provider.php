<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Config;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Provider is a provider for config parameters
 */
class Provider implements CacheWarmerInterface, ProviderInterface
{

    const FILENAME = 'config.php';

    /** @var Trismegiste\Yuurei\Persistence\RepositoryInterface */
    protected $repo;
    protected $cacheDir;

    public function __construct(RepositoryInterface $repo, $cache_dir)
    {
        $this->repo = $repo;
        $this->cacheDir = $cache_dir;
    }

    public function write($obj)
    {
        $this->repo->persist($obj);
        $this->warmUp($this->cacheDir);
    }

    public function read()
    {
        return include $this->cacheDir . DIRECTORY_SEPARATOR . self::FILENAME;
    }

    public function isOptional()
    {
        return false;
    }

    public function warmUp($cacheDir)
    {
        $c = $this->repo->findOne(['-class' => 'config']);

        file_put_contents($cacheDir . DIRECTORY_SEPARATOR . self::FILENAME
                , '<?php return ' . var_export($c, true) . ';'
        );
    }

}
