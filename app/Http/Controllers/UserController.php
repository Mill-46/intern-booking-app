<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        $this->logActivity($request, 'create_user', 'Created user #' . $user->id, null, [
            'managed_user_id' => $user->id,
            'role' => $user->role,
        ]);

        return redirect()->route('users.index')->with('status', 'User created');
    }

    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if (($validated['password'] ?? null) === null || $validated['password'] === '') {
            unset($validated['password']);
        }

        $user->update($validated);

        $this->logActivity($request, 'update_user', 'Updated user #' . $user->id, null, [
            'managed_user_id' => $user->id,
            'role' => $user->role,
        ]);

        return redirect()->route('users.index')->with('status', 'User updated');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $user->delete();

        $this->logActivity($request, 'delete_user', 'Deleted user #' . $user->id, null, [
            'managed_user_id' => $user->id,
        ]);

        return redirect()->route('users.index')->with('status', 'User deleted');
    }
}
