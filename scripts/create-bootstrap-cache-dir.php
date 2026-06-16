<?php
/**
 * Script to ensure bootstrap/cache directory exists.
 * Called by composer post-autoload-dump hook.
 */

$bootstrapCacheDir = __DIR__ . '/../bootstrap/cache';

if (!is_dir($bootstrapCacheDir)) {
    @mkdir($bootstrapCacheDir, 0755, true);
}

// Ensure it's writable
if (is_dir($bootstrapCacheDir) && !is_writable($bootstrapCacheDir)) {
    @chmod($bootstrapCacheDir, 0755);
}
