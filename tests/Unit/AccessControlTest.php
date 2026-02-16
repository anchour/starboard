<?php

namespace Starboard\Tests\Unit;

use Starboard\AccessControl;
use WP_UnitTestCase;

/**
 * Tests for the AccessControl class.
 */
class AccessControlTest extends WP_UnitTestCase
{
	/**
	 * Clean up after each test.
	 */
	public function tearDown(): void
	{
		parent::tearDown();
		delete_option('starboard_restrict_access');
	}

	/**
	 * Test isAnchourEmail with valid anchour.com emails.
	 */
	public function test_is_anchour_email_valid()
	{
		$this->assertTrue(AccessControl::isAnchourEmail('user@anchour.com'));
		$this->assertTrue(AccessControl::isAnchourEmail('USER@ANCHOUR.COM'));
		$this->assertTrue(AccessControl::isAnchourEmail('user@Anchour.Com'));
		$this->assertTrue(AccessControl::isAnchourEmail('user.name@anchour.com'));
	}

	/**
	 * Test isAnchourEmail with non-anchour emails.
	 */
	public function test_is_anchour_email_invalid()
	{
		$this->assertFalse(AccessControl::isAnchourEmail('user@example.com'));
		$this->assertFalse(AccessControl::isAnchourEmail('user@anchour.org'));
		$this->assertFalse(AccessControl::isAnchourEmail('user@notanchour.com'));
		$this->assertFalse(AccessControl::isAnchourEmail(''));
		$this->assertFalse(AccessControl::isAnchourEmail('not-an-email'));
	}

	/**
	 * Test restriction is disabled by default.
	 */
	public function test_restriction_disabled_by_default()
	{
		delete_option('starboard_restrict_access');

		$this->assertFalse(AccessControl::isRestrictionEnabled());
	}

	/**
	 * Test enabling restriction.
	 */
	public function test_enable_restriction()
	{
		AccessControl::setRestriction(true);

		$this->assertTrue(AccessControl::isRestrictionEnabled());
	}

	/**
	 * Test disabling restriction.
	 */
	public function test_disable_restriction()
	{
		AccessControl::setRestriction(true);
		$this->assertTrue(AccessControl::isRestrictionEnabled());

		AccessControl::setRestriction(false);
		$this->assertFalse(AccessControl::isRestrictionEnabled());
	}

	/**
	 * Test currentUserHasAccess when restriction is disabled.
	 */
	public function test_current_user_has_access_when_unrestricted()
	{
		AccessControl::setRestriction(false);

		// Anyone should have access when restriction is disabled
		$this->assertTrue(AccessControl::currentUserHasAccess());
	}

	/**
	 * Test currentUserHasAccess with anchour user when restricted.
	 */
	public function test_anchour_user_has_access_when_restricted()
	{
		AccessControl::setRestriction(true);

		// Create a user with anchour.com email
		$user_id = $this->factory->user->create([
			'user_email' => 'test@anchour.com',
			'role' => 'administrator',
		]);
		wp_set_current_user($user_id);

		$this->assertTrue(AccessControl::currentUserHasAccess());
	}

	/**
	 * Test currentUserHasAccess with non-anchour user when restricted.
	 */
	public function test_non_anchour_user_denied_when_restricted()
	{
		AccessControl::setRestriction(true);

		// Create a user with non-anchour email
		$user_id = $this->factory->user->create([
			'user_email' => 'test@example.com',
			'role' => 'administrator',
		]);
		wp_set_current_user($user_id);

		$this->assertFalse(AccessControl::currentUserHasAccess());
	}
}
