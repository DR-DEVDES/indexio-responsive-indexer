# INDEXIO — Responsive PHP Directory Indexer

A lightweight, responsive directory browser built with PHP, HTML, CSS, and vanilla JavaScript.
It features a Dracula-inspired dark interface, multilingual UI, directory navigation, sorting,
live search, grid/list views, file metadata, and subtle animations.

> **Important:** This is a directory browser, not a file-management application. It does not upload,
> edit, or delete files.

## Features

- Responsive layout for desktop, tablet, and mobile
- Languages: German, English, French, Spanish, and Klingon (`tlhIngan Hol`)
- Browse folders beneath the installation directory
- Sort by name, size, or last-modified time
- Reverse sort order by selecting the active sort again
- Instant client-side search
- List and grid layouts
- File size and modification date
- Hidden files excluded by default
- Path traversal protection for directory navigation
- Accessible labels, focus states, and reduced-motion support
- No JavaScript frameworks or build step

## Requirements

- PHP **7.4+** (PHP 8.1+ recommended)
- A web server such as Apache or Nginx
- Read permission for the directories and files you want to list

## Quick start

### Option A — PHP development server

1. Clone or download this repository.
2. Open a terminal in the project directory.
3. Run:

   ```bash
   php -S 127.0.0.1:8000
   ```

4. Open <http://127.0.0.1:8000>.

The built-in PHP server is for local development only. Do not expose it publicly.

### Option B — Apache / shared hosting

1. Upload `index.php` to the directory you want to browse.
2. Ensure PHP is enabled on the server.
3. Visit that directory in your browser.

The script lists the directory where `index.php` is installed and its descendants.

## Configuration

Edit the `$config` array near the top of `index.php`:

```php
$config = [
    'title' => 'INDEXIO INDEXER',
    'browseDirectories' => true,
    'showHiddenFiles' => false,
    'dateFormat' => 'd.m.Y · H:i',
    'sizeDecimals' => 1,
    'robots' => 'noindex, nofollow',
];
```

| Option | Description |
|---|---|
| `title` | Browser title |
| `browseDirectories` | Allow navigation into child directories |
| `showHiddenFiles` | Include dotfiles and dot-directories when `true` |
| `dateFormat` | PHP date format for modification timestamps |
| `sizeDecimals` | Decimal places shown for file sizes |
| `robots` | Robots meta directive |

## Languages

The language selector is controlled by the `$languages` array and `$translations` map.
The Klingon text is a best-effort UI translation; terminology may vary by source.

To add a language:
1. Add its language code to `$languages`.
2. Add a translation map under `$translations`.
3. Add an option to the language selector.
4. Add the matching locale in `$locale`.

## Security notes

This application exposes names, sizes, and timestamps for files it can read. Deploy it only
inside a directory whose contents you intend to reveal.

- **Do not place it in a sensitive directory.**
- Use web-server authentication or network restrictions for private content.
- Keep `showHiddenFiles` disabled unless you have a clear reason to enable it.
- Do not assume `noindex, nofollow` prevents access; it is only a crawler hint.
- The application does not authenticate visitors.
- Review server permissions before deployment.

The directory navigation checks canonical paths to prevent traversing above the install root.
This does not replace appropriate server permissions and access controls.

## Project structure

```text
indexio/
├── index.php
├── README.md
├── LICENSE
├── CONTRIBUTING.md
├── SECURITY.md
├── CHANGELOG.md
├── .gitignore
└── .github/
    ├── ISSUE_TEMPLATE/
    │   ├── bug_report.md
    │   └── feature_request.md
    └── PULL_REQUEST_TEMPLATE.md
```

## Roadmap ideas

- Optional password protection
- Configurable file-type icons
- Pagination for very large directories
- Unit and integration tests
- Dedicated CSS and translation files

## License

Released under the MIT License. See [`LICENSE`](LICENSE).
