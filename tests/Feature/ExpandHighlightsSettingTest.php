<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Tests for the "Expand highlights by default" collection setting (issue #352).
 *
 * User map (mirrors existing test suite):
 *   User 1  = admin (no collection permissions, but admin role)
 *   User 2  = maintainer of collection 1
 *
 * Cases covered:
 *   1. Non-maintainer cannot access the settings page (403).
 *   2. Maintainer can load the settings page (200) and the checkbox is present.
 *   3. Saving with the checkbox checked persists expand_highlights_by_default = 1.
 *   4. Saving without the checkbox persists expand_highlights_by_default as absent/falsy.
 *   5. Collection view page emits JS flag = 1 when setting is on.
 *   6. Collection view page emits JS flag = 0 when setting is off.
 */
class ExpandHighlightsSettingTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Access control
    // -------------------------------------------------------------------------

    public function testNonMaintainerCannotAccessSettings()
    {
        // User 1 is admin but NOT maintainer of collection 1
        $user = \App\User::find(1);
        $response = $this->actingAs($user)->get('/collection/1/settings');
        $response->assertStatus(403);
    }

    public function testMaintainerCanAccessSettings()
    {
        $user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/collection/1/settings');
        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // Checkbox presence in the settings form
    // -------------------------------------------------------------------------

    public function testSettingsPageContainsExpandHighlightsCheckbox()
    {
        $user = \App\User::find(2);
        $response = $this->actingAs($user)->get('/collection/1/settings');
        $response->assertStatus(200);
        $response->assertSee('expand_highlights_by_default');
        $response->assertSee('Expand highlights by default');
    }

    // -------------------------------------------------------------------------
    // Saving the setting ON
    // -------------------------------------------------------------------------

    public function testSavingExpandHighlightsOnPersistsInColumnConfig()
    {
        $user = \App\User::find(2);

        $collection = \App\Collection::find(1);
        $original_config = $collection->column_config;

        $response = $this->actingAs($user)->followingRedirects()->post('/collection/1/settings', [
            '_token'                       => csrf_token(),
            'collection_id'                => 1,
            'expand_highlights_by_default' => '1',
            'pdf_viewer'                   => 'viewerjs',
        ]);

        $response->assertStatus(200);

        $collection->refresh();
        $config = json_decode($collection->column_config);
        $this->assertEquals(1, (int) $config->expand_highlights_by_default,
            'expand_highlights_by_default should be 1 after saving with checkbox checked');

        // Restore original config so other tests are not affected
        $collection->column_config = $original_config;
        $collection->save();
    }

    // -------------------------------------------------------------------------
    // Saving the setting OFF (checkbox absent from POST = unchecked)
    // -------------------------------------------------------------------------

    public function testSavingExpandHighlightsOffRemovesOrFalsifiesFlag()
    {
        $user = \App\User::find(2);

        $collection = \App\Collection::find(1);
        $original_config = $collection->column_config;

        // First turn it on
        $this->actingAs($user)->post('/collection/1/settings', [
            '_token'                       => csrf_token(),
            'collection_id'                => 1,
            'expand_highlights_by_default' => '1',
            'pdf_viewer'                   => 'viewerjs',
        ]);

        // Now save without the checkbox (simulates unchecked)
        $this->actingAs($user)->post('/collection/1/settings', [
            '_token'       => csrf_token(),
            'collection_id' => 1,
            'pdf_viewer'   => 'viewerjs',
            // expand_highlights_by_default intentionally absent
        ]);

        $collection->refresh();
        $config = json_decode($collection->column_config);
        $this->assertFalse(
            !empty($config->expand_highlights_by_default),
            'expand_highlights_by_default should be absent or falsy after saving without checkbox'
        );

        // Restore
        $collection->column_config = $original_config;
        $collection->save();
    }

    // -------------------------------------------------------------------------
    // Collection view page JS output
    // -------------------------------------------------------------------------

    public function testCollectionViewEmitsJsFlagOneWhenSettingIsOn()
    {
        $user = \App\User::find(2);

        $collection = \App\Collection::find(1);
        $original_config = $collection->column_config;

        // Force the setting on directly so this test does not depend on the save test
        $config = json_decode($collection->column_config) ?? new \stdClass();
        $config->expand_highlights_by_default = 1;
        $collection->column_config = json_encode($config);
        $collection->save();

        $response = $this->actingAs($user)->get('/collection/1');
        $response->assertStatus(200);
        // The blade outputs either "if (1)" or "if (0)" into the JS
        $response->assertSee('if (1)');

        // Restore
        $collection->column_config = $original_config;
        $collection->save();
    }

    public function testCollectionViewEmitsJsFlagZeroWhenSettingIsOff()
    {
        $user = \App\User::find(2);

        $collection = \App\Collection::find(1);
        $original_config = $collection->column_config;

        // Force the setting off
        $config = json_decode($collection->column_config) ?? new \stdClass();
        unset($config->expand_highlights_by_default);
        $collection->column_config = json_encode($config);
        $collection->save();

        $response = $this->actingAs($user)->get('/collection/1');
        $response->assertStatus(200);
        $response->assertSee('if (0)');

        // Restore
        $collection->column_config = $original_config;
        $collection->save();
    }
}
