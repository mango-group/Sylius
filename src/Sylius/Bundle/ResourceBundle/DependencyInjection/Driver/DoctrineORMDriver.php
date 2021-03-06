<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class DoctrineORMDriver extends AbstractDatabaseDriver
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDriver()
    {
        return SyliusResourceBundle::DRIVER_DOCTRINE_ORM;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepositoryDefinition(array $classes)
    {
        $repositoryKey = $this->getContainerKey('repository', '.class');
        $repositoryClass = 'Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository';

        if ($this->container->hasParameter($repositoryKey)) {
            $repositoryClass = $this->container->getParameter($repositoryKey);
        }

        if (isset($classes['repository'])) {
            $repositoryClass = $classes['repository'];
        }

        $definition = new Definition($repositoryClass);
        $definition->setArguments(array(
            new Reference($this->getContainerKey('manager')),
            $this->getClassMetadataDefinition($classes['model'])
        ));

        if (isset($classes['translatable']['translatable_fields'])) {
            // TODO add only to if instance of translatable repository?
            $definition->addMethodCall('setTranslatableFields', array($classes['translatable']['translatable_fields']));
            $definition->addMethodCall('setLocaleContext', array(new Reference('sylius.context.locale')));
        }

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getManagerServiceKey()
    {
        return sprintf('doctrine.orm.%s_entity_manager', $this->managerName);
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataClassname()
    {
        return 'Doctrine\\ORM\\Mapping\\ClassMetadata';
    }
}
