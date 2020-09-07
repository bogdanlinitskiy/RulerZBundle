<?php

declare(strict_types=1);

namespace KPhoen\RulerZBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Clear the cache.
 *
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class CacheClearCommand extends Command
{
    protected $container;

    public function __construct(string $name = null, ContainerBuilder $container)
    {
        $this->container = $container;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rulerz:cache:clear')
            ->setDescription("Clear RulerZ's cache")
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cacheDir = $this->container->getParameter('rulerz.cache_directory');
        $filesystem = $this->container->get('filesystem');

        if (!is_writable($cacheDir)) {
            throw new \RuntimeException(sprintf('Unable to write in the "%s" directory', $cacheDir));
        }

        if ($filesystem->exists($cacheDir)) {
            $filesystem->remove($cacheDir);
            $filesystem->mkdir($cacheDir);
        }
    }
}
