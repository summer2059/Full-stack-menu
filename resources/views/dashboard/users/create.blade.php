@extends('dashboard.layouts.app')

@section('content')
    <div class="app-container container-xxl">
        <div class="card">
            <!-- Card Header -->
            <div class="card-header border-1 pt-6">
                <h4>Create User</h4>
            </div>

            <!-- Card Body -->
            <div class="card-body pt-0 mt-4">
                <form action="{{ route('user.store') }}" method="POST" novalidate>
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password and Confirm Password in same row -->
                    <div class="row mb-3">
                        <!-- Password -->
                        <div class="col-md-6 position-relative">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror" required>
                                <span class="input-group-text password-toggle" data-target="#password" aria-label="Toggle password visibility" style="cursor: pointer;">
                                    <!-- Eye icon SVG -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z"/>
                                        <path d="M8 5.5A2.5 2.5 0 1 1 5.5 8 2.5 2.5 0 0 1 8 5.5z"/>
                                    </svg>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6 position-relative">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control" required>
                                <span class="input-group-text password-toggle" data-target="#password_confirmation" aria-label="Toggle password visibility" style="cursor: pointer;">
                                    <!-- Eye icon SVG -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z"/>
                                        <path d="M8 5.5A2.5 2.5 0 1 1 5.5 8 2.5 2.5 0 0 1 8 5.5z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="roleSelect">Role</label>
                        <select name="role" id="roleSelect" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Permissions -->
                    <div class="mb-3" id="permissionsBox" style="display:none;">
                        <label class="form-label fw-bold">Permissions</label>
                        <div id="permissionsList" class="d-flex flex-wrap gap-3"></div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Create</button>
                        <a href="{{ route('user.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

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
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .permission-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            margin-bottom: 4px;
        }
    </style>
@endpush

@push('js')
<script>
    // Toggle password visibility with eye icon
    document.querySelectorAll('.password-toggle').forEach(el => {
        el.addEventListener('click', () => {
            const input = document.querySelector(el.dataset.target);
            if (input.type === 'password') {
                input.type = 'text';
                el.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                      <path d="M13.359 11.238a6.473 6.473 0 0 0 1.303-3.238 6.5 6.5 0 0 0-12.844 0 6.473 6.473 0 0 0 1.303 3.238L1.146 13.707a.5.5 0 1 0 .708.708l12-12a.5.5 0 0 0-.708-.708l-2.8 2.8A6.5 6.5 0 0 0 13.359 11.238z"/>
                      <path d="M6.06 8.06a2 2 0 0 0 2.829 2.83l-2.83-2.83z"/>
                    </svg>
                `;
            } else {
                input.type = 'password';
                el.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                      <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z"/>
                      <path d="M8 5.5A2.5 2.5 0 1 1 5.5 8 2.5 2.5 0 0 1 8 5.5z"/>
                    </svg>
                `;
            }
        });
    });

    // Load permissions dynamically
    const roleSelect = document.getElementById('roleSelect');
    const permissionsBox = document.getElementById('permissionsBox');
    const permissionsList = document.getElementById('permissionsList');

    roleSelect.addEventListener('change', function () {
        const role = this.value;

        if (role) {
            fetch("{{ route('user.role.permissions') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ role: role })
            })
            .then(response => response.json())
            .then(data => {
                permissionsList.innerHTML = '';

                Object.keys(data).forEach(group => {
                    const card = document.createElement('div');
                    card.classList.add('permission-card');

                    const title = document.createElement('div');
                    title.classList.add('permission-title');
                    title.textContent = group;
                    card.appendChild(title);

                    data[group].forEach(permission => {
                        const parts = permission.split('.');
                        const permissionName = parts.length > 1 ? parts[1] : permission;

                        const item = document.createElement('label');
                        item.classList.add('permission-item', 'form-check');

                        item.innerHTML = `
                            <input type="checkbox" name="permissions[]" value="${permission}" class="form-check-input" id="perm_${permission.replace('.', '_')}" checked disabled>
                            <span class="form-check-label">${permissionName}</span>
                        `;

                        card.appendChild(item);
                    });

                    permissionsList.appendChild(card);
                });

                permissionsBox.style.display = 'block';
            })
            .catch(() => {
                permissionsBox.style.display = 'none';
                permissionsList.innerHTML = '';
            });
        } else {
            permissionsBox.style.display = 'none';
            permissionsList.innerHTML = '';
        }
    });

    // If old role selected on reload, trigger change to load permissions
    window.addEventListener('DOMContentLoaded', () => {
        if (roleSelect.value) {
            roleSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
