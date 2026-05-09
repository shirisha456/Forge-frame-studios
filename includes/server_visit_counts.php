<?php
declare(strict_types=1);

/**
 * Server-side visit tallies so marketplace JSON matches cookie-based "most visited" aggregates.
 */

function forge_aggregate_storage_path(): string
{
    return __DIR__ . '/../data/forge_slug_counts.json';
}

function forge_aggregate_increment_slug(string $slug): void
{
    $slug = trim($slug);
    if ($slug === '') {
        return;
    }
    $path = forge_aggregate_storage_path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $fp = fopen($path, 'c+');
    if ($fp === false) {
        return;
    }
    try {
        flock($fp, LOCK_EX);
        $counts = [];
        $stat = fstat($fp);
        if ($stat && $stat['size'] > 0) {
            rewind($fp);
            $raw = stream_get_contents($fp);
            $decoded = $raw !== false && $raw !== '' ? json_decode($raw, true) : [];
            if (is_array($decoded)) {
                $counts = array_map('intval', $decoded);
            }
        }
        $counts[$slug] = ($counts[$slug] ?? 0) + 1;
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($counts));
        fflush($fp);
    } finally {
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

/** @return array<string, int> */
function forge_aggregate_load_counts(): array
{
    $path = forge_aggregate_storage_path();
    if (!is_file($path)) {
        return [];
    }
    $raw = file_get_contents($path);
    if ($raw === false || $raw === '') {
        return [];
    }
    $data = json_decode($raw, true);

    return is_array($data) ? array_map('intval', $data) : [];
}

/** @return list<string> */
function forge_top_slugs(int $limit = 5): array
{
    $counts = forge_aggregate_load_counts();
    if ($counts === []) {
        return [];
    }
    arsort($counts);

    return array_slice(array_keys($counts), 0, max(1, $limit));
}
