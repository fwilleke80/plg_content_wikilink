<?php

namespace Joomla\Plugin\Content\WikiLink;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

/**
 * Content plugin which replaces {wiki ...} placeholders with Wikipedia links.
 */
final class WikiLink extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Loads plugin language files automatically.
	 *
	 * @var boolean
	 */
	protected $autoloadLanguage = true;

	/**
	 * Returns the subscribed Joomla events.
	 *
	 * @return array<string, string>
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onContentPrepare' => 'OnContentPrepare',
		];
	}

	/**
	 * Returns whether generated links should open in a new tab.
	 *
	 * @return boolean
	 */
	private function GetOpenInNewTab(): bool
	{
		return (bool) $this->params->get('open_in_new_tab', 1);
	}

	/**
	 * Parses article text and replaces wiki placeholders.
	 *
	 * Supported forms:
	 * {wiki New_York_City}
	 * {wiki New_York_City "New York"}
	 * {wiki New_York_City "New York" de}
	 *
	 * @param Event $event The Joomla event.
	 *
	 * @return void
	 */
	public function OnContentPrepare(Event $event): void
	{
		if (!$this->getApplication()->isClient('site'))
		{
			return;
		}

		$args = array_values($event->getArguments());

		if (count($args) < 2)
		{
			return;
		}

		$article = $args[1] ?? null;

		if (!is_object($article) || !property_exists($article, 'text') || !is_string($article->text))
		{
			return;
		}

		$defaultLanguage = $this->GetDefaultLanguage();
		$article->text = $this->ReplaceWikiPlaceholders($article->text, $defaultLanguage);
	}

	/**
	 * Replaces all wiki placeholders in a string.
	 *
	 * @param string $text            The source text.
	 * @param string $defaultLanguage The default wiki language.
	 *
	 * @return string
	 */
	private function ReplaceWikiPlaceholders(string $text, string $defaultLanguage): string
	{
		$pattern = '/\{wiki\s+([^\s"}]+)(?:\s+"([^"]*)")?(?:\s+([A-Za-z-]+))?\s*\}/u';

		return (string) preg_replace_callback(
			$pattern,
			function (array $matches) use ($defaultLanguage): string
			{
				$page = $matches[1] ?? '';
				$label = $matches[2] ?? '';
				$language = $matches[3] ?? '';

				if ($page === '')
				{
					return $matches[0];
				}

				$language = $this->SanitizeLanguageCode($language !== '' ? $language : $defaultLanguage);

				if ($language === '')
				{
					$language = 'en';
				}

				if ($label === '')
				{
					$label = str_replace('_', ' ', $page);
				}

				$url = $this->BuildWikipediaUrl($page, $language);
				$title = Text::sprintf('PLG_CONTENT_WIKILINK_LINK_TITLE', $label);
				$openInNewTab = $this->GetOpenInNewTab();

				$attributes = [];
				$attributes[] = 'href="' . htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
				$attributes[] = 'title="' . htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
				$attributes[] = 'aria-label="' . htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';

				if ($openInNewTab)
				{
					$attributes[] = 'target="_blank"';
					$attributes[] = 'rel="noopener noreferrer"';
				}

				return '<a ' . implode(' ', $attributes) . '>'
					. htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
					. '</a>';
				},
			$text
		);
	}

	/**
	 * Builds the final Wikipedia URL.
	 *
	 * @param string $page     The wiki page title.
	 * @param string $language The language code.
	 *
	 * @return string
	 */
	private function BuildWikipediaUrl(string $page, string $language): string
	{
		$normalizedPage = str_replace(' ', '_', trim($page));
		$encodedPage = rawurlencode($normalizedPage);
		$encodedPage = str_replace('%2F', '/', $encodedPage);

		return 'https://' . $language . '.wikipedia.org/wiki/' . $encodedPage;
	}

	/**
	 * Returns the configured default language.
	 *
	 * @return string
	 */
	private function GetDefaultLanguage(): string
	{
		$language = (string) ($this->params->get('default_language', 'en'));

		$language = $this->SanitizeLanguageCode($language);

		return $language !== '' ? $language : 'en';
	}

	/**
	 * Sanitizes a language code like "en", "de", "pt-br".
	 *
	 * @param string $language The raw language code.
	 *
	 * @return string
	 */
	private function SanitizeLanguageCode(string $language): string
	{
		$language = trim($language);
		$language = strtolower($language);

		if (!preg_match('/^[a-z]{2,12}(?:-[a-z0-9]{2,12})*$/', $language))
		{
			return '';
		}

		return $language;
	}
}