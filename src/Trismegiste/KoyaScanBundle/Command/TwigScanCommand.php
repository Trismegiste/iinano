<?php

/*
 * iinano
 */

namespace Trismegiste\KoyaScanBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * TwigScanCommand is a CLI which scans all twigs and referencing css and js
 */
class TwigScanCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('koya:scan')
                ->addArgument('bundle')
                ->addOption('invert', 'i', InputOption::VALUE_NONE, 'Bijection');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $bundle Symfony\Component\HttpKernel\Bundle\BundleInterface */
        $bundle = $this->getBundle($input->getArgument('bundle'));

        $finder = new Finder();
        $finder->files()->name("*.html.twig")->in($bundle->getPath() . '/Resources/views');

        $assetFile = [];
        $inheritanceMap = [];
        foreach ($finder as $file) {
            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            $match = [];
            if (preg_match_all('#asset\([\'|"]([^\'|^"]+)[\'|"]\)#', $file->getContents(), $match)) {
                foreach ($match[1] as $item) {
                    $assetFile[$file->getRelativePathname()][] = $item;
                }
            }
            if (preg_match('#extends\s+[\'|"]' . $bundle->getName() . ':([^\'|^"]+)[\'|"]#', $file->getContents(), $match)) {
                $path = str_replace(':', '/', $match[1]);
                if ($path[0] === '/') {
                    $path = substr($path, 1);
                }
                $inheritanceMap[$file->getRelativePathname()] = $path;
            }
        }

        $compiled = [];
        foreach ($assetFile as $name => $listing) {
            $compiled[$name] = $this->getInheritedAssets($name, $inheritanceMap, $assetFile);
        }

        if ($input->getOption('invert')) {
            $inverted = [];
            foreach ($compiled as $file => $listing) {
                foreach ($listing as $asset) {
                    $inverted[$asset][] = $file;
                }
            }
            $compiled = $inverted;
        }

        // print
        foreach ($compiled as $asset => $listing) {
            $output->writeln("<info>$asset</info>");
            foreach ($listing as $file) {
                $output->writeln("    $file");
            }
        }
    }

    private function getInheritedAssets($name, $inheritanceMap, $assetFile)
    {
        $base = (array_key_exists($name, $assetFile)) ? $assetFile[$name] : [];

        if (!array_key_exists($name, $inheritanceMap)) {
            return $base;
        } else {
            $parent = $inheritanceMap[$name];
            return array_merge($base, $this->getInheritedAssets($parent, $inheritanceMap, $assetFile));
        }
    }

    private function getBundle($name)
    {
        $bundle = $this->getContainer()->get('kernel')->getBundles();
        foreach ($bundle as $item) {
            if ($name === $item->getName()) {
                $found = $item;
            }
        }

        if (!isset($found)) {
            throw new \InvalidArgumentException("$name is not a referenced bundle");
        }

        return $found;
    }

}
