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
        $container = $this->compileContainer();
        $cacheDir = $container->getParameter('rulerz.cache_directory');
        $filesystem = $container->get('filesystem');

        if (!is_writable($cacheDir)) {
            throw new \RuntimeException(sprintf('Unable to write in the "%s" directory', $cacheDir));
        }

        if ($filesystem->exists($cacheDir)) {
            $filesystem->remove($cacheDir);
            $filesystem->mkdir($cacheDir);
        }
    }

    private function compileContainer(): ContainerBuilder
    {
        $kernel = clone $this->getApplication()->getKernel();
        $kernel->boot();

        $method = new \ReflectionMethod($kernel, 'buildContainer');
        $method->setAccessible(true);
        $container = $method->invoke($kernel);
        $container->getCompiler()->compile($container);

        return $container;
    }
}
