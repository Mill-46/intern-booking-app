@php($roles = [
    \App\Models\User::ROLE_ADMIN => 'Admin',
    \App\Models\User::ROLE_APPROVER_L1 => 'Approver L1',
    \App\Models\User::ROLE_APPROVER_L2 => 'Approver L2',
])

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">Nama</label>
        <input name="name" value="{{ old('name', $user->name ?? '') }}" required class="field">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required class="field">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Role</label>
        <select name="role" required class="field">
            @foreach($roles as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Password {{ isset($user) ? '(opsional)' : '' }}</label>
        <input type="password" name="password" {{ isset($user) ? '' : 'required' }} class="field">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" {{ isset($user) ? '' : 'required' }} class="field">
    </div>
</div>
