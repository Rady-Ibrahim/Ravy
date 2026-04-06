@extends('layouts.admin')

@section('title', __('Add user'))

@section('page_title', __('Add user'))
@section('page_subtitle', __('Create a new user and assign admin roles if needed.'))

@section('content')
    <div class="mx-auto max-w-2xl">
        <form action="{{ route('admin.users.store') }}" method="post" class="admin-card border-0 shadow-md shadow-slate-200/50 space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">{{ __('Email') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">{{ __('Password') }}</label>
                <input type="password" name="password" id="password" required autocomplete="new-password" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">{{ __('Confirm password') }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700">{{ __('Phone') }}</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="type" class="block text-sm font-medium text-slate-700">{{ __('Type') }}</label>
                    <select name="type" id="type" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="admin" @selected(old('type') === 'admin')>{{ __('Admin') }}</option>
                        <option value="customer" @selected(old('type', 'customer') === 'customer')>{{ __('Customer') }}</option>
                    </select>
                    @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700">{{ __('Status') }}</label>
                    <select name="status" id="status" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="active" @selected(old('status', 'active') === 'active')>{{ __('Active') }}</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>{{ __('Inactive') }}</option>
                        <option value="banned" @selected(old('status') === 'banned')>{{ __('Banned') }}</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div id="roles-field" class="{{ old('type', 'admin') === 'customer' ? 'hidden' : '' }}">
                <span class="block text-sm font-medium text-slate-700">{{ __('Roles') }} <span class="font-normal text-slate-500">({{ __('admin only') }})</span></span>
                <div class="mt-2 max-h-48 space-y-2 overflow-y-auto rounded-xl border border-slate-200 p-3">
                    @foreach ($roles as $role)
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" @checked(in_array($role->name, old('roles', []), true)) class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            {{ $role->name }}
                        </label>
                    @endforeach
                </div>
                @error('roles')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('roles.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 border-t border-slate-100 pt-5">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Save') }}</button>
                <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('type')?.addEventListener('change', function () {
            document.getElementById('roles-field').classList.toggle('hidden', this.value !== 'admin');
        });
    </script>
@endsection
