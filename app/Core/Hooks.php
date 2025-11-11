<?php

namespace App\Core;

/**
 * Simple Hook System for Plugins
 */
class Hooks
{
    private static $actions = [];
    private static $filters = [];

    /**
     * Register an action hook
     */
    public static function addAction($hook, $callback, $priority = 10)
    {
        if (!isset(self::$actions[$hook])) {
            self::$actions[$hook] = [];
        }

        if (!isset(self::$actions[$hook][$priority])) {
            self::$actions[$hook][$priority] = [];
        }

        self::$actions[$hook][$priority][] = $callback;
    }

    /**
     * Execute an action hook
     */
    public static function doAction($hook, ...$args)
    {
        if (!isset(self::$actions[$hook])) {
            return;
        }

        // Sort by priority
        ksort(self::$actions[$hook]);

        foreach (self::$actions[$hook] as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }

    /**
     * Register a filter hook
     */
    public static function addFilter($hook, $callback, $priority = 10)
    {
        if (!isset(self::$filters[$hook])) {
            self::$filters[$hook] = [];
        }

        if (!isset(self::$filters[$hook][$priority])) {
            self::$filters[$hook][$priority] = [];
        }

        self::$filters[$hook][$priority][] = $callback;
    }

    /**
     * Apply a filter hook
     */
    public static function applyFilters($hook, $value, ...$args)
    {
        if (!isset(self::$filters[$hook])) {
            return $value;
        }

        // Sort by priority
        ksort(self::$filters[$hook]);

        foreach (self::$filters[$hook] as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                $value = call_user_func_array($callback, array_merge([$value], $args));
            }
        }

        return $value;
    }

    /**
     * Check if hook has any callbacks
     */
    public static function hasAction($hook)
    {
        return isset(self::$actions[$hook]) && !empty(self::$actions[$hook]);
    }

    /**
     * Remove an action
     */
    public static function removeAction($hook, $callback, $priority = 10)
    {
        if (!isset(self::$actions[$hook][$priority])) {
            return false;
        }

        $key = array_search($callback, self::$actions[$hook][$priority], true);
        if ($key !== false) {
            unset(self::$actions[$hook][$priority][$key]);
            return true;
        }

        return false;
    }
}

// Now close the namespace and define global functions in a separate file
// This file should be loaded after Hooks.php
