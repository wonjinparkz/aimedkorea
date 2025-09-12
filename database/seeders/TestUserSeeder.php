<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // 테스트 계정 생성 또는 업데이트
        $testUser = User::updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Test User',
                'username' => 'testuser',
                'password' => Hash::make('test'),
                'email_verified_at' => now(),
            ]
        );

        // 유저 역할 찾기 (가장 낮은 권한)
        $userRole = Role::where('slug', 'user')->first();
        
        if ($userRole) {
            // 기존 역할 제거 후 유저 역할 할당
            $testUser->roles()->sync([$userRole->id]);
            
            $this->command->info("테스트 계정 생성 완료:");
            $this->command->info("Email: test@test.com");
            $this->command->info("Password: test");
            $this->command->info("Role: {$userRole->display_name} (Level {$userRole->level})");
        } else {
            $this->command->error("유저 역할을 찾을 수 없습니다. 먼저 역할 시더를 실행해주세요.");
        }
    }
}