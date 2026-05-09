@extends('layouts.admin')

@section('title', __('Edit Customer') . ' - ' . $customer->name)

@section('page_title', __('Edit Customer'))
@section('page_subtitle', $customer->first_name . ' ' . $customer->last_name)

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="admin-card">
            <form action="{{ route('admin.customers.update', $customer) }}" method="post">
                @csrf
                @method('PUT')
                
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-slate-700">{{ __('First Name') }}</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $customer->first_name) }}" 
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-slate-700">{{ __('Last Name') }}</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $customer->last_name) }}" 
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700">{{ __('Email') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" 
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700">{{ __('Phone') }}</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" 
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700">{{ __('Status') }}</label>
                            <select id="status" name="status" 
                                    class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="active" {{ old('status', $customer->status) === 'active' ? 'selected' : '' }}>
                                    {{ __('Active') }}
                                </option>
                                <option value="inactive" {{ old('status', $customer->status) === 'inactive' ? 'selected' : '' }}>
                                    {{ __('Inactive') }}
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                        <a href="{{ route('admin.customers.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                            {{ __('Update Customer') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
