@extends('dashboard.layouts.app')

@push('css')
<style>
    .permission-card {
        flex: 1 1 200px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px;
        min-width: 200px;
    }
    .permission-title {
        font-size: 14px; font-weight: 700;
        text-transform: uppercase; margin-bottom: 8px;
    }
    .permission-item {
        display: flex; align-items: center;
        gap: 6px; font-size: 13px; margin-bottom: 4px;
    }
</style>
@endpush

@php
    $isEdit  = isset($user);
    $title   = $isEdit ? 'Edit User' : 'Create User';
    $action  = $isEdit ? route('user.update', $user->id) : route('user.store');
    $icon    = $isEdit ? 'fa-pencil-square-o' : 'fa-user-plus';
    $btnText = $isEdit ? 'Update User' : 'Create User';
@endphp

@section('content')
<div class="app-container container-xxl">
    <div class="card shadow-sm">

        <div class="card-header border-1 pt-4 pb-2">
            <div class="card-title">
                <h4 class="mb-0">
                    <i class="fa {{ $icon }} text-primary me-2"></i>{{ $title }}
                </h4>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('user.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ $action }}" method="POST" novalidate>
                @csrf
                @if($isEdit) @method('PUT') @endif

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Name</label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email ?? '') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Password --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold">
                            Password
                            @if($isEdit)
                                <small class="text-muted fw-normal">(leave blank to keep current)</small>
                            @endif
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                autocomplete="new-password"
                                placeholder="{{ $isEdit ? 'Leave blank to keep current' : 'Enter password' }}"
                                {{ $isEdit ? '' : 'required' }}>
                            <span class="input-group-text toggle-pw" data-target="#password" style="cursor:pointer;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                        @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold">
                            Confirm Password
                        </label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" autocomplete="new-password"
                                placeholder="Confirm password">
                            <span class="input-group-text toggle-pw" data-target="#password_confirmation" style="cursor:pointer;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Role --}}
                <div class="mb-3">
                    <label for="roleSelect" class="form-label fw-semibold">Role</label>
                    <select name="role" id="roleSelect"
                        class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ old('role', $isEdit ? $user->roles->pluck('name')->first() : '') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Permissions (dynamic) --}}
                <div class="mb-4" id="permissionsBox" style="display:none;">
                    <label class="form-label fw-bold">Permissions assigned to this role</label>
                    <div id="permissionsList" class="d-flex flex-wrap gap-3"></div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fa fa-floppy-o me-1"></i> {{ $btnText }}
                    </button>
                    <a href="{{ route('user.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
// ── Password visibility toggle ─────────────────────────
document.querySelectorAll('.toggle-pw').forEach(el => {
    el.addEventListener('click', () => {
        const input = document.querySelector(el.dataset.target);
        const icon  = el.querySelector('i');
        if (input.type === 'password') {
            input.type    = 'text';
            icon.className = 'fa fa-eye-slash';
        } else {
            input.type    = 'password';
            icon.className = 'fa fa-eye';
        }
    });
});

// ── Permission loader ──────────────────────────────────
const roleSelect      = document.getElementById('roleSelect');
const permissionsBox  = document.getElementById('permissionsBox');
const permissionsList = document.getElementById('permissionsList');

function loadPermissions(role) {
    if (!role) {
        permissionsBox.style.display = 'none';
        permissionsList.innerHTML    = '';
        return;
    }

    fetch("{{ route('user.role.permissions') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ role })
    })
    .then(r => r.json())
    .then(data => {
        permissionsList.innerHTML = '';

        if (!Object.keys(data).length) {
            permissionsBox.style.display = 'none';
            return;
        }

        Object.keys(data).forEach(group => {
            const card = document.createElement('div');
            card.classList.add('permission-card');

            const heading = document.createElement('div');
            heading.classList.add('permission-title');
            heading.textContent = group;
            card.appendChild(heading);

            data[group].forEach(permission => {
                const actionLabel = permission.split('.').slice(1).join('.');
                const label = document.createElement('label');
                label.classList.add('permission-item', 'form-check');
                label.innerHTML = `
                    <input type="checkbox" name="permissions[]"
                        value="${permission}"
                        class="form-check-input" checked disabled>
                    <span class="form-check-label">${actionLabel}</span>
                `;
                card.appendChild(label);
            });

            permissionsList.appendChild(card);
        });

        permissionsBox.style.display = 'block';
    })
    .catch(() => {
        permissionsBox.style.display = 'none';
        permissionsList.innerHTML    = '';
    });
}

roleSelect.addEventListener('change', function () {
    loadPermissions(this.value);
});

window.addEventListener('DOMContentLoaded', () => {
    if (roleSelect.value) loadPermissions(roleSelect.value);
});
</script>
@endpush