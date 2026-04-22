<?php

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;

use Joomla\Plugin\Content\WikiLink\WikiLink;

return new class () implements ServiceProviderInterface
{
	/**
	 * Registers the plugin in the DI container.
	 *
	 * @param Container $container The DI container.
	 *
	 * @return void
	 */
	public function register(Container $container): void
	{
		$container->set(
			PluginInterface::class,
			function (Container $container): WikiLink
			{
				$config = (array) PluginHelper::getPlugin('content', 'wikilink');
				$subject = $container->get(DispatcherInterface::class);
				$app = Factory::getApplication();

				$plugin = new WikiLink($subject, $config);
				$plugin->setApplication($app);

				return $plugin;
			}
		);
	}
};