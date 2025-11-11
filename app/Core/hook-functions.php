<?php
/**
 * Global helper functions for hooks
 * These must be in the global namespace (no namespace declaration)
 */

if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10) {
        \App\Core\Hooks::addAction($hook, $callback, $priority);
    }
}

if (!function_exists('do_action')) {
    function do_action($hook, ...$args) {
        \App\Core\Hooks::doAction($hook, ...$args);
    }
}

if (!function_exists('add_filter')) {
    function add_filter($hook, $callback, $priority = 10) {
        \App\Core\Hooks::addFilter($hook, $callback, $priority);
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters($hook, $value, ...$args) {
        return \App\Core\Hooks::applyFilters($hook, $value, ...$args);
    }
}

if (!function_exists('has_action')) {
    function has_action($hook) {
        return \App\Core\Hooks::hasAction($hook);
    }
}

if (!function_exists('remove_action')) {
    function remove_action($hook, $callback, $priority = 10) {
        return \App\Core\Hooks::removeAction($hook, $callback, $priority);
    }
}

