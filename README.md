# Content Plugin: Wiki Link

![Plugin Type](https://img.shields.io/badge/type-content%20plugin-6A1B9A) ![License](https://img.shields.io/badge/license-GPL%20v2%2B-orange)

A simple Joomla! 6 content plugin that replaces `{wiki ...}` placeholders with links to Wikipedia.

## Features

- Converts inline placeholders into Wikipedia links
- Optional custom link text
- Optional language override per link
- Configurable default language
- Safe HTML output (escaped, no injection risk)

---

## Usage

Insert placeholders directly into your Joomla article content.

### Basic

```text
{wiki New_York_City}
````

→

```html
<a href="https://en.wikipedia.org/wiki/New_York_City" target="_blank" title="Open New York City on Wikipedia" rel="noopener noreferrer">
  New York City
</a>
````

### Custom link text

```text
{wiki New_York_City "New York"}
```

→

```html
<a href="https://en.wikipedia.org/wiki/New_York_City" target="_blank" rel="noopener noreferrer">
  New York City
</a>
````

### Custom Language

```text
{wiki New_York_City "New York"}
```

→

```html
<a href="https://de.wikipedia.org/wiki/New_York_City" target="_blank" rel="noopener noreferrer">
  New York
</a>
```

### Syntax

```text
{wiki PAGE ["LABEL"] [LANG]}
```

| Parameter | Required | Description                             |
| --------- | -------- | --------------------------------------- |
| `PAGE`    | yes      | Wikipedia page name (use `_` or spaces) |
| `LABEL`   | no       | Custom link text                        |
| `LANG`    | no       | Language code (`en`, `de`, `fr`, etc.)  |

## Plugin Settings

### Default Wikipedia Language

Used when no language is specified in the placeholder.

Examples:

- `en` → `https://en.wikipedia.org/...`
- `de` → `https://de.wikipedia.org/...`

### Open links in new tab

If set to "Yes", Wikipedia links created with this plugin will open in a new tab (with `noopener` and `noreferrer`). Otherwise, they are opened in the same tab.

## Installation

1. Zip the plugin folder (plg_content_wikilink)
2. Go to Joomla Administrator → System → Install
3. Upload the ZIP file
4. Enable the plugin:  
  Content - Wiki Link

## Notes

- Only works in article content (frontend)
- Does not modify admin content
- Links always open in a new tab
- Language codes are validated before use

## Limitations

- No support for nested placeholders
- Only double quotes (") are supported for labels
- No automatic detection of existing Wikipedia links

## Example

```text
Berlin is the capital of {wiki Germany "Deutschland" de}.
```

## License

GNU Public License 2

## Author

Frank Willeke

