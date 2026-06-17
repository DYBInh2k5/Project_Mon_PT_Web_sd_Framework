<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // role dùng để phân quyền người dùng trong hệ thống.
        // Giá trị hiện tại đang dùng: admin, editor, user.
        'role',
        // is_active là trạng thái hoạt động của tài khoản.
        // true  = đang hoạt động
        // false = tạm khóa / ngừng hoạt động
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            // Ép kiểu dữ liệu để code xử lý đúng kiểu giá trị.
            'role' => 'string',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function hasRole(string ...$roles): bool
    {
        // Hàm hỗ trợ để kiểm tra user hiện tại có nằm trong nhóm role cho phép hay không.
        // Ví dụ:
        // $user->hasRole('admin')
        // $user->hasRole('editor', 'admin')
        return in_array($this->role, $roles, true);
    }

    public function profile(): HasOne
    {
        // Quan hệ 1-1:
        // Một user chỉ có duy nhất 1 profile.
        // Laravel sẽ nối user.id với profiles.user_id.
        return $this->hasOne(Profile::class);
    }

    public function articles(): HasMany
    {
        // Quan hệ 1-n:
        // Một user có thể viết nhiều article (bài viết).
        return $this->hasMany(Article::class);
    }
}
