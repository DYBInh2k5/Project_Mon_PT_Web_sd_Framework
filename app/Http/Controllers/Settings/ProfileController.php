<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $this->ensureProfileExists($user->id, $user->name);

        $profile = DB::table('profiles')->where('user_id', $user->id)->first();

        return view('pages.profile', [
            'profile' => $profile,
            'user' => $user,
        ]);
    }

    public function edit(Request $request): View
    {
        $user = $request->user();

        $this->ensureProfileExists($user->id, $user->name);

        $profile = DB::table('profiles')->where('user_id', $user->id)->first();

        return view('pages.auth.settings.profile', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $this->ensureProfileExists($user->id, $user->name);

        $profile = DB::table('profiles')->where('user_id', $user->id)->first();

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
            'avatar' => ['nullable', 'string', 'max:2048'],
            'birthday' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $fullName = $validated['full_name'] ?? $validated['name'] ?? $profile->full_name;

        DB::table('profiles')
            ->where('id', $profile->id)
            ->update([
                'full_name' => $fullName,
                'address' => $validated['address'] ?? null,
                'avatar' => $validated['avatar'] ?? null,
                'birthday' => $validated['birthday'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'updated_at' => now(),
            ]);

        if (array_key_exists('name', $validated) || array_key_exists('email', $validated)) {
            $userUpdate = [];

            if (! empty($validated['name'])) {
                $userUpdate['name'] = $validated['name'];
            }

            if (! empty($validated['email'])) {
                $userUpdate['email'] = $validated['email'];

                if ($validated['email'] !== $user->email) {
                    $userUpdate['email_verified_at'] = null;
                }
            }

            if ($userUpdate !== []) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update($userUpdate);
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

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }

    private function ensureProfileExists(int $userId, string $fallbackName): void
    {
        $exists = DB::table('profiles')->where('user_id', $userId)->exists();

        if ($exists) {
            return;
        }

        DB::table('profiles')->insert([
            'user_id' => $userId,
            'full_name' => $fallbackName,
            'address' => 'Cap nhat dia chi',
            'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($fallbackName).'&background=465fff&color=ffffff&size=160',
            'birthday' => now()->subYears(20)->toDateString(),
            'gender' => 'Khac',
            'phone' => 'Chua cap nhat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
