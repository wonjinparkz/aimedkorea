<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    // 역할과의 관계
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    // 설문 응답과의 관계
    public function survey_responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    // 설문 타임라인과의 관계
    public function surveyTimelines(): HasMany
    {
        return $this->hasMany(SurveyTimeline::class);
    }

    // 특정 역할이 있는지 확인
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    // 여러 역할 중 하나라도 있는지 확인
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    // 특정 권한이 있는지 확인
    public function hasPermission(string $permission): bool
    {
        // 관리자는 모든 권한을 가짐
        if ($this->hasRole('admin')) {
            return true;
        }

        // 역할을 통한 권한 확인
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    // 특정 모듈에 대한 권한 확인
    public function hasModulePermission(string $module, string $action = null): bool
    {
        // 관리자는 모든 권한을 가짐
        if ($this->hasRole('admin')) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->hasModulePermission($module, $action)) {
                return true;
            }
        }

        return false;
    }

    // 최고 레벨 역할 반환
    public function getHighestRole(): ?Role
    {
        return $this->roles()->orderBy('level', 'desc')->first();
    }

    // 사용자가 관리자인지 확인
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // 모든 사용자가 관리자 패널에 접근 가능하도록 설정
        // 실제 운영 환경에서는 이메일 확인이나 역할 기반 접근 제어를 추가하세요
        return true;
    }
}
