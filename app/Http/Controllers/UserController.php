<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng.
     */
    public function index(Request $request): View
    {
        // Truy vấn gốc dùng để tính toán các số liệu thống kê tổng hợp ở đầu trang quản lý người dùng.
        $baseQuery = User::query();

        // Truy vấn chính để lấy danh sách người dùng hiển thị trong bảng.
        // Dữ liệu hỗ trợ tìm kiếm, lọc theo vai trò, trạng thái hoạt động và phân trang.
        $users = User::with('profile')
            // Tìm kiếm theo tên hoặc địa chỉ email nếu có tham số 'search'.
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            // Lọc theo vai trò (role) nếu được chọn.
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->string('role')->toString());
            })
            // Lọc theo trạng thái Hoạt động (Active) / Ngưng hoạt động (Inactive) nếu được chọn.
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->string('status')->toString() === 'active');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Trả dữ liệu sang view users.index.
        return view('users.index', [
            'title' => 'Người dùng',
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status']),
            'summary' => [
                'total' => (clone $baseQuery)->count(),
                'admins' => (clone $baseQuery)->where('role', 'admin')->count(),
                'editors' => (clone $baseQuery)->where('role', 'editor')->count(),
                'users' => (clone $baseQuery)->where('role', 'user')->count(),
                'active' => (clone $baseQuery)->where('is_active', true)->count(),
                'verified' => (clone $baseQuery)->whereNotNull('email_verified_at')->count(),
            ],
        ]);
    }

    /**
     * Hiển thị form tạo mới người dùng.
     */
    public function create(): View
    {
        return view('users.create', [
            'title' => 'Tạo người dùng',
        ]);
    }

    /**
     * Lưu thông tin người dùng mới tạo vào database.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        // Validation đã được xử lý tự động trong StoreUserRequest.
        // Nếu dữ liệu hợp lệ, tiến hành tạo tài khoản mới trong database.
        $user = User::create($request->validated());

        // Tạo profile mặc định đi kèm cho tài khoản mới (Quan hệ 1-1).
        Profile::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Tạo tài khoản người dùng thành công.');
    }

    /**
     * Hiển thị thông tin chi tiết của người dùng.
     */
    public function show(User $user): View
    {
        // Eager load quan hệ profile để tránh truy vấn thừa N+1.
        $user->load('profile');

        return view('users.show', [
            'title' => 'Chi tiết người dùng',
            'user' => $user,
        ]);
    }

    /**
     * Hiển thị form chỉnh sửa thông tin người dùng.
     */
    public function edit(User $user): View
    {
        // Route Model Binding: {user} trên URL tự động map thành model User $user.
        // Nạp thêm thông tin profile đi kèm để truyền sang view chỉnh sửa.
        $user->load('profile');

        return view('users.edit', [
            'title' => 'Chỉnh sửa người dùng',
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }

    /**
     * Cập nhật thông tin người dùng trong database.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        // Cập nhật thông tin tài khoản chính trong bảng users.
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $validated['is_active'],
        ]);

        // Lấy profile hiện tại hoặc tạo mới nếu chưa tồn tại.
        $profile = $user->profile ?: new Profile(['user_id' => $user->id]);
        $avatarPath = $profile->avatar;

        // Xử lý upload ảnh đại diện (avatar) nếu có file mới tải lên.
        if ($request->hasFile('avatar')) {
            // Xóa file avatar cũ trên ổ đĩa nếu có và không phải là ảnh mẫu trực tuyến.
            if ($avatarPath && ! str_starts_with($avatarPath, 'http')) {
                Storage::disk('public')->delete($avatarPath);
            }

            $avatarPath = $request->file('avatar')->store('profiles', 'public');
        }

        // Cập nhật thông tin chi tiết cá nhân trong bảng profiles.
        $profile->fill([
            'full_name' => ($validated['full_name'] ?? null) ?: $validated['name'],
            'address' => $validated['address'] ?? null,
            'avatar' => $avatarPath,
            'birthday' => $validated['birthday'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        $profile->save();

        return redirect()->route('users.index')->with('success', 'Cập nhật tài khoản người dùng thành công.');
    }

    /**
     * Chuyển đổi trạng thái hoạt động (is_active) của người dùng.
     */
    public function toggleStatus(User $user, Request $request): RedirectResponse
    {
        // Ngăn chặn admin tự khóa/vô hiệu hóa chính tài khoản của mình.
        // Tránh trường hợp hệ thống mất quyền quản trị cao nhất.
        if ($request->user()?->is($user)) {
            return redirect()
                ->route('users.index')
                ->with('success', 'Bạn không thể vô hiệu hóa tài khoản đang đăng nhập.');
        }

        // Đảo ngược trạng thái hoạt động hiện tại (true -> false hoặc false -> true).
        // Chức năng này được gọi trực tiếp khi admin click nhanh vào nhãn trạng thái.
        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Cập nhật trạng thái người dùng thành công.');
    }

    /**
     * Xóa tài khoản người dùng khỏi cơ sở dữ liệu.
     */
    public function destroy(User $user, Request $request): RedirectResponse
    {
        // Không cho phép tự xóa tài khoản của chính mình khi đang đăng nhập.
        if ($request->user()?->is($user)) {
            return redirect()
                ->route('users.index')
                ->with('success', 'Bạn không thể xóa tài khoản đang đăng nhập.');
        }

        // Thực hiện xóa mềm/xóa cứng tài khoản người dùng.
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Xóa tài khoản người dùng thành công.');
    }
}
