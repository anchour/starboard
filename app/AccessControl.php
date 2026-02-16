<?php

namespace Starboard;

defined('ABSPATH') || exit;

/**
 * Access Control - Manages access restrictions for Starboard settings.
 */
class AccessControl
{
    /**
     * Option name for storing access restriction setting.
     */
    const OPTION_NAME = 'starboard_restrict_access';

    /**
     * Get the allowed email domain.
     *
     * @return string
     */
    public static function getAllowedDomain(): string
    {
        return Config::getAccessDomain();
    }

    /**
     * Check if access restriction is enabled.
     *
     * @return bool
     */
    public static function isRestrictionEnabled(): bool
    {
        return (bool) get_option(self::OPTION_NAME, false);
    }

    /**
     * Enable or disable access restriction.
     *
     * @param bool $enabled
     * @return bool
     */
    public static function setRestriction(bool $enabled): bool
    {
        return update_option(self::OPTION_NAME, $enabled);
    }

    /**
     * Check if an email address is from the allowed domain.
     *
     * @param string $email
     * @return bool
     */
    public static function isAnchourEmail(string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        $domain = substr(strrchr($email, '@'), 1);
        return strtolower($domain) === strtolower(self::getAllowedDomain());
    }

    /**
     * Check if the current user has access to Starboard settings.
     *
     * @return bool
     */
    public static function currentUserHasAccess(): bool
    {
        // Must have manage_options capability
        if (!current_user_can('manage_options')) {
            return false;
        }

        // If restriction is not enabled, allow access
        if (!self::isRestrictionEnabled()) {
            return true;
        }

        // Check if current user has an allowed email
        $currentUser = wp_get_current_user();
        return self::isAnchourEmail($currentUser->user_email ?? '');
    }
}
