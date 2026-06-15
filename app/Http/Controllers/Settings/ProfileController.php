<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        // Lấy profile bằng Eloquent qua quan hệ relationship để không dùng Query Builder thô nữa.
        $profile = $this->ensureProfileExists($user);

        return view('pages.profile', [
            'profile' => $profile,
            'user' => $user,
        ]);
    }

    public function edit(Request $request): View
    {
        $user = $request->user();

        // Sử dụng cùng một luồng Eloquent cho trang chỉnh sửa profile cá nhân.
        $profile = $this->ensureProfileExists($user);

        return view('pages.auth.settings.profile', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $profile = $this->ensureProfileExists($user);

        $validated = $request->validate([
            'full_name' => ['nullable', 'string', 'max:100', 'required_without:name'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'address' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'birthday' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $fullName = $validated['full_name'] ?? $validated['name'] ?? $profile->full_name;
        $avatarPath = $profile->avatar;

        if ($request->hasFile('avatar')) {
            if ($avatarPath && ! str_starts_with($avatarPath, 'http')) {
                Storage::disk('public')->delete($avatarPath);
            }

            $avatarPath = $request->file('avatar')->store('profiles', 'public');
        }

        $profile->fill([
            'full_name' => $fullName,
            'address' => $validated['address'] ?? null,
            'avatar' => $avatarPath,
            'birthday' => $validated['birthday'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        $profile->save();

        if (array_key_exists('name', $validated) || array_key_exists('email', $validated)) {
            $userUpdate = [];
            $emailChanged = false;

            if (! empty($validated['name'])) {
                $userUpdate['name'] = $validated['name'];
            }

            if (! empty($validated['email'])) {
                $userUpdate['email'] = $validated['email'];

                if ($validated['email'] !== $user->email) {
                    $emailChanged = true;
                }
            }

            if ($userUpdate !== []) {
                $user->fill($userUpdate);

                // Cột email_verified_at không nằm trong danh sách fillable nên phải gán trực tiếp
                // nhằm đảm bảo Laravel reset trạng thái xác minh khi người dùng thay đổi địa chỉ email.
                if ($emailChanged) {
                    $user->email_verified_at = null;
                }

                $user->save();
            }
        }

        return to_route('settings.profile.edit')->with('status', 'Profile updated successfully');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $profile = $this->ensureProfileExists($user);

        Auth::logout();

        $profile->delete();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }

    private function ensureProfileExists(User $user): Profile
    {
        // Sử dụng phương thức firstOrCreate qua relationship hasOne để đảm bảo logic
        // tự động tạo profile nếu chưa có sẵn trên tài khoản của người dùng.
        return $user->profile()->firstOrCreate(
            [],
            [
                'full_name' => $user->name,
                'address' => 'Cập nhật địa chỉ',
                'avatar' => null,
                'birthday' => now()->subYears(20)->toDateString(),
                'gender' => 'Khác',
                'phone' => 'Chưa cập nhật',
            ]
        );
    }
}
