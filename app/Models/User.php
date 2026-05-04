<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
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
        // role dung de phan quyen user trong he thong.
        // Gia tri hien tai dang dung: admin, editor, user.
        'role',
        // is_active la trang thai hoat dong cua tai khoan.
        // true  = dang hoat dong
        // false = tam khoa / ngung hoat dong
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
            // Ep kieu du lieu de code xu ly dung kieu gia tri.
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
        // Ham ho tro de kiem tra user hien tai co nam trong nhom role cho phep hay khong.
        // Vi du:
        // $user->hasRole('admin')
        // $user->hasRole('editor', 'admin')
        return in_array($this->role, $roles, true);
    }
}
