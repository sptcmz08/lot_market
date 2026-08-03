<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_forms_use_username_without_email(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Username สำหรับล็อกอิน')
            ->assertDontSee('name="email"', false)
            ->assertDontSee('อีเมล');

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'พนักงานไม่มีอีเมล',
            'username' => 'staff-no-email',
            'phone' => '0811111111',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'staff',
            'is_active' => 1,
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'staff-no-email',
            'email' => null,
        ]);

        $this->actingAs($admin)->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('staff-no-email')
            ->assertDontSee('<th>อีเมล</th>', false);
    }

    public function test_staff_login_uses_username_only(): void
    {
        $user = User::create([
            'name' => 'เจ้าหน้าที่ทดสอบ',
            'username' => 'staff-login-only',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->post(route('login'), ['login' => $user->username, 'password' => 'password'])
            ->assertRedirect(route('staff.bookings.index'));
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'แอดมินทดสอบ',
            'username' => 'admin-user-test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
