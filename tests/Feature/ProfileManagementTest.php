<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/profile');
        
        $response->assertStatus(200);
        $response->assertViewIs('profile.show');
        $response->assertViewHas('user', $user);
    }

    public function test_user_can_update_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put('/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_user_cannot_update_profile_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put('/profile', [
            'name' => '',  // Invalid: empty name
            'email' => 'invalid-email',  // Invalid: not a valid email
        ]);
        
        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_user_can_change_password()
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);
        $this->actingAs($user);

        $response = $this->put('/profile/password', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);
        
        $response->assertRedirect();
        
        // Verify the password was updated by attempting to login
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_user_cannot_change_password_with_wrong_current_password()
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);
        $this->actingAs($user);

        $response = $this->put('/profile/password', [
            'current_password' => 'wrong-password',  // Wrong password
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);
        
        $response->assertSessionHasErrors(['current_password']);
        // Verify the password was NOT updated
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_profile_requires_authentication()
    {
        $response = $this->get('/profile');
        
        $response->assertRedirect('/login');
    }
}