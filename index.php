<?php
declare(strict_types=1);

/**
 * DEVDES Playground — responsive directory browser
 * Put this file in the directory you want to browse.
 */

$config = [
    'title' => 'INDEXIO INDEXER',
    'browseDirectories' => true,
    'showHiddenFiles' => false,
    'dateFormat' => 'd.m.Y · H:i',
    'sizeDecimals' => 1,
    'robots' => 'noindex, nofollow',
];

$languages = ['de', 'en', 'fr', 'es', 'tlh'];
$lang = isset($_GET['lang']) && is_string($_GET['lang']) && in_array($_GET['lang'], $languages, true)
    ? $_GET['lang']
    : 'de';

$translations = [
    'de' => [
        'language'=>'Sprache','online'=>'Verzeichnis online','explorer'=>'Datei-Explorer',
        'localTime'=>'Lokale Systemzeit','unavailable'=>'Dieser Ordner ist nicht verfügbar.',
        'unreadable'=>'Der Ordner konnte nicht gelesen werden.','total'=>'Elemente insgesamt',
        'foldersFiles'=>'Ordner / Dateien','totalSize'=>'Dateigröße gesamt*',
        'search'=>'Dateien und Ordner durchsuchen…','sort'=>'Sortierung',
        'name'=>'Name','size'=>'Größe','date'=>'Datum','modified'=>'Zuletzt geändert',
        'parent'=>'Übergeordneter Ordner','empty'=>'Keine passenden Dateien gefunden.',
        'tagline'=>'Minimal. Schnell. Aufgeräumt.','count'=>'Elemente',
        'sumNote'=>'*Summe der Dateigrößen im aktuellen Ordner','grid'=>'▦ Raster','list'=>'☰ Liste',
    ],
    'en' => [
        'language'=>'Language','online'=>'Directory online','explorer'=>'File explorer',
        'localTime'=>'Local system time','unavailable'=>'This folder is not available.',
        'unreadable'=>'The folder could not be read.','total'=>'Total items',
        'foldersFiles'=>'Folders / files','totalSize'=>'Total file size*',
        'search'=>'Search files and folders…','sort'=>'Sort by',
        'name'=>'Name','size'=>'Size','date'=>'Date','modified'=>'Last modified',
        'parent'=>'Parent folder','empty'=>'No matching files found.',
        'tagline'=>'Minimal. Fast. Organized.','count'=>'items',
        'sumNote'=>'*Combined file size in this folder','grid'=>'▦ Grid','list'=>'☰ List',
    ],
    'fr' => [
        'language'=>'Langue','online'=>'Répertoire en ligne','explorer'=>'Explorateur de fichiers',
        'localTime'=>'Heure locale','unavailable'=>'Ce dossier est indisponible.',
        'unreadable'=>'Impossible de lire ce dossier.','total'=>'Éléments au total',
        'foldersFiles'=>'Dossiers / fichiers','totalSize'=>'Taille totale*',
        'search'=>'Rechercher des fichiers et dossiers…','sort'=>'Trier par',
        'name'=>'Nom','size'=>'Taille','date'=>'Date','modified'=>'Dernière modification',
        'parent'=>'Dossier parent','empty'=>'Aucun fichier correspondant.',
        'tagline'=>'Minimal. Rapide. Organisé.','count'=>'éléments',
        'sumNote'=>'*Taille totale des fichiers de ce dossier','grid'=>'▦ Grille','list'=>'☰ Liste',
    ],
    'es' => [
        'language'=>'Idioma','online'=>'Directorio en línea','explorer'=>'Explorador de archivos',
        'localTime'=>'Hora local','unavailable'=>'Esta carpeta no está disponible.',
        'unreadable'=>'No se pudo leer la carpeta.','total'=>'Elementos totales',
        'foldersFiles'=>'Carpetas / archivos','totalSize'=>'Tamaño total*',
        'search'=>'Buscar archivos y carpetas…','sort'=>'Ordenar por',
        'name'=>'Nombre','size'=>'Tamaño','date'=>'Fecha','modified'=>'Última modificación',
        'parent'=>'Carpeta superior','empty'=>'No se encontraron archivos.',
        'tagline'=>'Minimalista. Rápido. Organizado.','count'=>'elementos',
        'sumNote'=>'*Tamaño combinado de los archivos de esta carpeta','grid'=>'▦ Cuadrícula','list'=>'☰ Lista',
    ],
    'tlh' => [
        'language'=>'Hol','online'=>'De’wI’ QaptaH','explorer'=>'De’ QulwI’',
        'localTime'=>'DaHjaj poH','unavailable'=>'Daqvam tu’lu’be’.',
        'unreadable'=>'Daqvam laDlaHbe’.','total'=>'Dochmey Hoch',
        'foldersFiles'=>'polmeH Daqmey / De’mey','totalSize'=>'De’ ngI’ Hoch*',
        'search'=>'De’mey yInej…','sort'=>'chenmoH',
        'name'=>'pong','size'=>'ngI’','date'=>' jaj','modified'=>'choHpu’',
        'parent'=>'Daq wa’DIch','empty'=>'De’mey tu’lu’be’.',
        'tagline'=>'mach. nom. qay’be’.','count'=>'Dochmey',
        'sumNote'=>'*DaqvamDaq De’mey ngI’ Hoch','grid'=>'▦ ghantoH','list'=>'☰ tetlh',
    ],
];
$t = $translations[$lang];

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function formatBytes(int $bytes, int $decimals = 1): string {
    if ($bytes < 1024) {
        return $bytes . ' B';
    }
    $units = ['KB', 'MB', 'GB', 'TB', 'PB'];
    $value = (float)$bytes;
    foreach ($units as $unit) {
        $value /= 1024;
        if ($value < 1024 || $unit === 'PB') {
            return number_format($value, $decimals, ',', '.') . ' ' . $unit;
        }
    }
    return number_format($value, $decimals, ',', '.') . ' PB';
}

function queryUrl(array $changes = []): string {
    $params = $_GET;
    foreach ($changes as $key => $value) {
        if ($value === null || $value === '') {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }
    return '?' . http_build_query($params);
}

$root = realpath(__DIR__);
$self = basename(__FILE__);
$requested = isset($_GET['b']) && is_string($_GET['b'])
    ? trim(str_replace('\\', '/', $_GET['b']), '/')
    : '';
$segments = $requested === '' ? [] : explode('/', $requested);
$valid = $root !== false;

foreach ($segments as $segment) {
    if ($segment === '' || $segment === '.' || $segment === '..' || strpos($segment, "\0") !== false) {
        $valid = false;
        break;
    }
}

$currentPath = $root ?: __DIR__;
if ($valid && $config['browseDirectories'] && $segments) {
    $candidate = realpath($currentPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments));
    if (
        $candidate === false ||
        !is_dir($candidate) ||
        ($candidate !== $currentPath && strpos($candidate, $currentPath . DIRECTORY_SEPARATOR) !== 0)
    ) {
        $valid = false;
    } else {
        $currentPath = $candidate;
    }
} elseif (!$config['browseDirectories'] && $segments) {
    $valid = false;
}

$pathError = '';
if (!$valid) {
    http_response_code(404);
    $pathError = $t['unavailable'];
    $currentPath = $root ?: __DIR__;
    $segments = [];
    $requested = '';
}

$relativePath = implode('/', $segments);
$items = [];
$totalSize = 0;

if ($pathError === '' && is_readable($currentPath)) {
    try {
        foreach (new DirectoryIterator($currentPath) as $entry) {
            $name = $entry->getFilename();

            if ($entry->isDot() || $name === $self) {
                continue;
            }
            if (!$config['showHiddenFiles'] && substr($name, 0, 1) === '.') {
                continue;
            }

            $isDirectory = $entry->isDir();
            if ($isDirectory && !$config['browseDirectories']) {
                continue;
            }

            $size = $isDirectory ? 0 : (int)@filesize($entry->getPathname());
            $modified = (int)@filemtime($entry->getPathname());
            $items[] = [
                'name' => $name,
                'isdir' => $isDirectory,
                'size' => max(0, $size),
                'time' => max(0, $modified),
            ];
            $totalSize += max(0, $size);
        }
    } catch (UnexpectedValueException $exception) {
        $pathError = $t['unreadable'];
    }
}

$sort = isset($_GET['s']) && in_array($_GET['s'], ['name', 'size', 'time'], true)
    ? $_GET['s']
    : 'name';
$reverse = isset($_GET['r']) && $_GET['r'] === '1';

usort($items, static function (array $a, array $b) use ($sort, $reverse): int {
    if ($a['isdir'] !== $b['isdir']) {
        $result = $a['isdir'] ? -1 : 1;
    } elseif ($sort === 'size') {
        $result = $a['size'] <=> $b['size'];
    } elseif ($sort === 'time') {
        $result = $a['time'] <=> $b['time'];
    } else {
        $result = strnatcasecmp($a['name'], $b['name']);
    }
    return $reverse ? -$result : $result;
});

$crumbs = [];
$walk = [];
foreach ($segments as $segment) {
    $walk[] = $segment;
    $crumbs[] = [
        'name' => $segment,
        'url' => queryUrl(['b' => implode('/', $walk), 's' => null, 'r' => null]),
    ];
}

$objectCount = count($items);
$folderCount = count(array_filter($items, static fn(array $item): bool => $item['isdir']));
$fileCount = $objectCount - $folderCount;
$locale = ['de'=>'de-DE', 'en'=>'en-GB', 'fr'=>'fr-FR', 'es'=>'es-ES', 'tlh'=>'en-GB'][$lang];
?>
<!doctype html>
<html lang="<?= h($lang === 'tlh' ? 'tlh' : $lang) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="<?= h($config['robots']) ?>">
<meta name="theme-color" content="#171923">
<title><?= h($config['title']) ?><?= $relativePath !== '' ? ' / ' . h($relativePath) : '' ?></title>
<style>
:root{color-scheme:dark;--bg:#171923;--panel:#202332;--line:#34394e;--text:#f1f3f9;--muted:#a3abc2;--purple:#bd93f9;--cyan:#8be9fd;--green:#50fa7b;--yellow:#f1fa8c;--radius:17px}
*{box-sizing:border-box}html{min-height:100%;scroll-behavior:smooth}body{margin:0;min-height:100vh;background:radial-gradient(ellipse at 15% 0%,#bd93f922,transparent 35%),radial-gradient(ellipse at 90% 15%,#8be9fd12,transparent 28%),var(--bg);color:var(--text);font:15px/1.5 Inter,system-ui,-apple-system,"Segoe UI",sans-serif}a{color:inherit;text-decoration:none}.shell{width:min(1180px,calc(100% - 32px));margin:auto;padding:28px 0}
.topbar{display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:34px}.brand{display:flex;align-items:center;gap:11px;font-weight:800;letter-spacing:.04em}.brand-mark{display:grid;place-items:center;width:42px;height:42px;border-radius:13px;background:linear-gradient(135deg,var(--purple),var(--cyan));color:#171923;font-weight:900;box-shadow:0 8px 30px #bd93f933}.lang-switch{display:flex;align-items:center;gap:8px;margin-left:auto}.lang-switch label,.status{font-size:12px;color:var(--muted)}.lang-select{border:1px solid var(--line);border-radius:10px;background:var(--panel);color:var(--text);padding:9px;outline:none}.lang-select:focus{border-color:var(--purple)}.status{display:flex;align-items:center;gap:8px}.pulse{width:8px;height:8px;border-radius:50%;background:var(--green);animation:pulse 2s infinite}
.pathline{color:var(--muted);overflow-wrap:anywhere;margin:0 0 24px;animation:rise .45s both}.pathline a{color:var(--cyan)}.pathline a:hover{color:var(--green)}
.stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:22px;animation:rise .55s both}.stat{padding:17px 19px;background:linear-gradient(145deg,#24283a,#1d202e);border:1px solid var(--line);border-radius:var(--radius)}.stat-label{color:var(--muted);font-size:12px}.stat-value{font-size:24px;font-weight:800;letter-spacing:-.03em;margin-top:3px}.purple{color:var(--purple)}.cyan{color:var(--cyan)}.green{color:var(--green)}
.toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin:24px 0 13px}.search-wrap{position:relative;flex:1;min-width:220px;max-width:460px}.search-wrap svg{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted)}.search{width:100%;border:1px solid var(--line);border-radius:12px;background:#1e2130;color:var(--text);padding:12px 14px 12px 42px;outline:none}.search:focus{border-color:var(--purple);box-shadow:0 0 0 3px #bd93f922}.sorts{display:flex;gap:8px;flex-wrap:wrap}.sort-link,.view-btn{padding:9px 12px;border:1px solid var(--line);border-radius:10px;color:var(--muted);font-size:12px;background:var(--panel);cursor:pointer;transition:.2s}.sort-link:hover,.sort-link.active,.view-btn:hover{color:var(--text);border-color:#7662a6;background:#302943}
.list-head{display:grid;grid-template-columns:minmax(0,1fr) 135px 170px;gap:16px;padding:12px 18px;color:var(--muted);font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.file-list{display:grid;gap:9px}.file-row{display:grid;grid-template-columns:minmax(0,1fr) 135px 170px;gap:16px;align-items:center;padding:14px 18px;background:linear-gradient(110deg,#252a3bec,#1f2331e8);border:1px solid var(--line);border-radius:14px;transition:transform .2s,border-color .2s,background .2s;animation:rowIn .4s both;animation-delay:calc(var(--i,0)*22ms)}.file-row:hover{transform:translateY(-2px);border-color:#7662a6;background:linear-gradient(110deg,#2b2d43,#22283a)}.file-name{display:flex;align-items:center;gap:13px;min-width:0;font-weight:650;overflow-wrap:anywhere}.file-name:hover .name-text{color:var(--cyan)}.file-icon{width:39px;height:39px;display:grid;place-items:center;flex:0 0 39px;border-radius:12px;background:#343049;color:var(--purple)}.file-icon.folder{background:#153640;color:var(--cyan)}.file-icon.parent{background:#3a3422;color:var(--yellow)}.file-icon svg{width:20px;height:20px}.file-meta{color:var(--muted);font-size:12px;font-variant-numeric:tabular-nums}.file-meta.size{text-align:right}.empty{display:none;text-align:center;padding:50px 20px;color:var(--muted);border:1px dashed var(--line);border-radius:16px}.error{padding:16px 18px;margin-bottom:18px;border:1px solid #ff79c655;background:#ff79c611;border-radius:12px;color:#ff9acb}.footer{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:22px 3px 0;margin-top:24px;border-top:1px solid var(--line);color:var(--muted);font-size:12px}.footer strong{color:var(--purple)}
body.grid-mode .file-list{grid-template-columns:repeat(auto-fit,minmax(245px,1fr))}body.grid-mode .file-row{grid-template-columns:1fr;gap:12px;align-content:start;min-height:145px}body.grid-mode .file-meta.size{text-align:left}body.grid-mode .list-head{display:none}body.grid-mode .file-name{align-items:flex-start}body.grid-mode .file-icon{width:44px;height:44px;flex-basis:44px}
@keyframes rise{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}@keyframes rowIn{from{opacity:0;transform:translateY(7px)}to{opacity:1;transform:translateY(0)}}@keyframes pulse{0%{box-shadow:0 0 0 0 #50fa7b77}70%{box-shadow:0 0 0 7px #50fa7b00}100%{box-shadow:0 0 0 0 #50fa7b00}}
@media(max-width:700px){.shell{width:calc(100% - 24px);padding-top:20px}.topbar{margin-bottom:24px}.status{width:100%}.stats{gap:8px}.stat{padding:13px}.stat-value{font-size:19px}.list-head{display:none}.file-row{grid-template-columns:minmax(0,1fr) auto;gap:8px 12px;padding:13px}.file-name{grid-column:1/-1}.file-meta.date{grid-column:1}.file-meta.size{grid-column:2;grid-row:2}.sorts{width:100%}.sort-link,.view-btn{flex:1;text-align:center}}
@media(max-width:420px){.stats{grid-template-columns:1fr}.stat{display:flex;justify-content:space-between;align-items:center}.stat-value{margin:0}.brand{font-size:13px}.lang-switch{margin-left:0}}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;scroll-behavior:auto!important;transition-duration:.01ms!important}}
</style>
</head>
<body>
<div class="shell">
<header class="topbar">
  <a class="brand" href="<?= h(queryUrl(['b'=>null,'s'=>null,'r'=>null])) ?>"><span class="brand-mark">ID</span><span>INDEXIO <span style="color:var(--purple)">RESPONSIVE INDEXER</span></span></a>
  <form class="lang-switch" method="get" aria-label="<?= h($t['language']) ?>">
    <?php if ($relativePath !== ''): ?><input type="hidden" name="b" value="<?= h($relativePath) ?>"><?php endif; ?>
    <?php if ($sort !== 'name'): ?><input type="hidden" name="s" value="<?= h($sort) ?>"><?php endif; ?>
    <?php if ($reverse): ?><input type="hidden" name="r" value="1"><?php endif; ?>
    <label for="language"><?= h($t['language']) ?></label>
    <select class="lang-select" id="language" name="lang" onchange="this.form.submit()">
      <option value="de" <?= $lang==='de'?'selected':'' ?>>🇩🇪 Deutsch</option>
      <option value="en" <?= $lang==='en'?'selected':'' ?>>🇬🇧 English</option>
      <option value="fr" <?= $lang==='fr'?'selected':'' ?>>🇫🇷 Français</option>
      <option value="es" <?= $lang==='es'?'selected':'' ?>>🇪🇸 Español</option>
      <option value="tlh" <?= $lang==='tlh'?'selected':'' ?>>🖖 tlhIngan Hol (Klingonisch)</option>
    </select>
  </form>
  <div class="status"><span class="pulse"></span><?= h($t['online']) ?> <span>·</span> PHP Index</div>
</header>

<nav class="pathline" aria-label="Breadcrumb">
  <a href="<?= h(queryUrl(['b'=>null,'s'=>null,'r'=>null])) ?>">/ root</a>
  <?php foreach ($crumbs as $crumb): ?><span aria-hidden="true"> / </span><a href="<?= h($crumb['url']) ?>"><?= h($crumb['name']) ?></a><?php endforeach; ?>
</nav>

<?php if ($pathError !== ''): ?><div class="error"><?= h($pathError) ?></div><?php endif; ?>

<section class="stats" aria-label="<?= h($t['total']) ?>">
  <div class="stat"><div class="stat-label"><?= h($t['total']) ?></div><div class="stat-value purple"><?= $objectCount ?></div></div>
  <div class="stat"><div class="stat-label"><?= h($t['foldersFiles']) ?></div><div class="stat-value cyan"><?= $folderCount ?> <span style="color:var(--muted);font-size:15px">/</span> <?= $fileCount ?></div></div>
  <div class="stat"><div class="stat-label"><?= h($t['totalSize']) ?></div><div class="stat-value green"><?= h(formatBytes($totalSize, $config['sizeDecimals'])) ?></div></div>
</section>

<div class="toolbar">
  <label class="search-wrap">
    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
    <input class="search" id="search" type="search" placeholder="<?= h($t['search']) ?>" autocomplete="off" aria-label="<?= h($t['search']) ?>">
  </label>
  <div class="sorts" aria-label="<?= h($t['sort']) ?>">
    <a class="sort-link <?= $sort==='name'?'active':'' ?>" href="<?= h(queryUrl(['s'=>'name','r'=>($sort==='name'&&!$reverse)?'1':null])) ?>"><?= h($t['name']) ?> <?= $sort==='name'?($reverse?'↓':'↑'):'' ?></a>
    <a class="sort-link <?= $sort==='size'?'active':'' ?>" href="<?= h(queryUrl(['s'=>'size','r'=>($sort==='size'&&!$reverse)?'1':null])) ?>"><?= h($t['size']) ?> <?= $sort==='size'?($reverse?'↓':'↑'):'' ?></a>
    <a class="sort-link <?= $sort==='time'?'active':'' ?>" href="<?= h(queryUrl(['s'=>'time','r'=>($sort==='time'&&!$reverse)?'1':null])) ?>"><?= h($t['date']) ?> <?= $sort==='time'?($reverse?'↓':'↑'):'' ?></a>
    <button class="view-btn" id="viewToggle" type="button" aria-pressed="false"><?= h($t['grid']) ?></button>
  </div>
</div>

<div class="list-head"><span><?= h($t['name']) ?></span><span style="text-align:right"><?= h($t['size']) ?></span><span style="text-align:right"><?= h($t['modified']) ?></span></div>
<main class="file-list" id="fileList">
<?php if ($relativePath !== ''): ?>
  <a class="file-row" style="--i:0" data-name=".." href="<?= h(queryUrl(['b'=>count($segments)>1?implode('/',array_slice($segments,0,-1)):null,'s'=>null,'r'=>null])) ?>">
    <span class="file-name"><span class="file-icon parent"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 19V5m-6 6 6-6 6 6"/></svg></span><span class="name-text">.. <span style="color:var(--muted);font-weight:400"><?= h($t['parent']) ?></span></span></span>
    <span class="file-meta size">—</span><span class="file-meta date">—</span>
  </a>
<?php endif; ?>
<?php foreach ($items as $index => $item):
    $name = $item['name'];
    $isDirectory = $item['isdir'];
    if ($isDirectory) {
        $next = trim(($relativePath !== '' ? $relativePath . '/' : '') . $name, '/');
        $url = queryUrl(['b'=>$next,'s'=>null,'r'=>null]);
    } else {
        $url = ($relativePath !== '' ? implode('/', array_map('rawurlencode', $segments)) . '/' : '') . rawurlencode($name);
    }
?>
  <a class="file-row" style="--i:<?= min($index,18) ?>" data-name="<?= h(function_exists('mb_strtolower') ? mb_strtolower($name,'UTF-8') : strtolower($name)) ?>" href="<?= h($url) ?>">
    <span class="file-name">
      <span class="file-icon <?= $isDirectory?'folder':'' ?>">
      <?php if ($isDirectory): ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7.5A2.5 2.5 0 0 1 5.5 5H10l2 2h6.5A2.5 2.5 0 0 1 21 9.5v8a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 17.5z"/></svg>
      <?php else: ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3h8l5 5v13H6z"/><path d="M14 3v6h5M9 14h6M9 17h6"/></svg><?php endif; ?>
      </span>
      <span class="name-text"><?= h($name) ?><?= $isDirectory?' <span style="color:var(--muted)">/</span>':'' ?></span>
    </span>
    <span class="file-meta size"><?= $isDirectory?'—':h(formatBytes($item['size'],$config['sizeDecimals'])) ?></span>
    <span class="file-meta date"><?= $item['time']?h(date($config['dateFormat'],$item['time'])):'—' ?></span>
  </a>
<?php endforeach; ?>
</main>
<div class="empty" id="emptyState"><?= h($t['empty']) ?></div>

<footer class="footer">
  <span><strong>DEVDES</strong> · <?= h($t['tagline']) ?></span>
  <span><?= $objectCount ?> <?= h($t['count']) ?> · <?= h($t['sumNote']) ?></span>
</footer>
</div>
<script>
(() => {
  const search = document.getElementById('search');
  const rows = [...document.querySelectorAll('.file-row[data-name]')];
  const empty = document.getElementById('emptyState');
  const toggle = document.getElementById('viewToggle');
  const locale = <?= json_encode($locale) ?>;
  const gridLabel = <?= json_encode($t['grid'], JSON_UNESCAPED_UNICODE) ?>;
  const listLabel = <?= json_encode($t['list'], JSON_UNESCAPED_UNICODE) ?>;

  search.addEventListener('input', () => {
    const query = search.value.trim().toLocaleLowerCase(locale);
    let visible = 0;
    rows.forEach(row => {
      const matches = row.dataset.name.toLocaleLowerCase(locale).includes(query);
      row.hidden = !matches;
      if (matches) visible++;
    });
    empty.style.display = visible === 0 ? 'block' : 'none';
  });

  toggle.addEventListener('click', () => {
    const grid = document.body.classList.toggle('grid-mode');
    toggle.setAttribute('aria-pressed', String(grid));
    toggle.textContent = grid ? listLabel : gridLabel;
  });
})();
</script>
</body>
</html>
