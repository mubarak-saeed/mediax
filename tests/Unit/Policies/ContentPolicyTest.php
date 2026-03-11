<?php

namespace Tests\Unit\Policies;

use App\Models\Content;
use App\Models\User;
use App\Policies\ContentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_any_content(): void
    {
        $policy = new ContentPolicy;

        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
        ]);

        $content = Content::factory()->create();

        $this->assertTrue($policy->update($superAdmin, $content));
    }

    public function test_admin_can_only_update_own_content(): void
    {
        $policy = new ContentPolicy;

        $ownerAdmin = User::factory()->admin()->create();
        $anotherAdmin = User::factory()->admin()->create();

        $ownedContent = Content::factory()->create([
            'created_by' => $ownerAdmin->id,
        ]);

        $notOwnedContent = Content::factory()->create([
            'created_by' => $ownerAdmin->id,
        ]);

        $this->assertTrue($policy->update($ownerAdmin, $ownedContent));
        $this->assertFalse($policy->update($anotherAdmin, $notOwnedContent));
    }
}
