```php
{{--
    Admin User Management Blade Template (e.g., resources/views/admin/users.blade.php)

    This template provides a structure for viewing and editing users.
    It requires the Admin/UserController to fetch the $users collection (paginated)
    and handle updates.
    It uses Tailwind CSS classes for styling (assuming Tailwind is set up in your project).
--}}

{{-- Assuming a main layout file exists, like layouts.app or layouts.admin --}}
{{-- @extends('layouts.admin') --}}

{{-- @section('content') --}}
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold mb-6">User Management</h1>

    {{-- Display Success/Error Flash Messages --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    {{-- Display validation errors or general errors flashed by the controller --}}
     @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Search Form - Submits back to the index method (admin.users.index) --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6">
        <div class="flex items-center">
            <input
                type="text"
                name="search_email"
                placeholder="Search by Email..."
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2"
                value="{{ request('search_email') }}" {{-- Repopulate search term --}}
            >
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Search
            </button>
             <a href="{{ route('admin.users.index') }}" class="ml-2 text-sm text-gray-600 hover:text-gray-800">Clear</a>
        </div>
    </form>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    {{-- Displaying common/useful fields. Add more <th> if needed. --}}
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Username</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                    {{-- Add other relevant user table columns you want to display/edit here --}}
                    {{-- <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email Verified At</th> --}}
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created At</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Permissions (Example)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Loop through the $users collection passed from the controller --}}
                @forelse ($users as $user)
                    {{-- Each user row is wrapped in its own form for individual updates --}}
                    {{-- The form submits to the admin.users.update route --}}
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" id="user-form-{{ $user->id }}">
                        @csrf {{-- CSRF Protection --}}
                        @method('PUT') {{-- Use PUT method for updates --}}

                        <tr class="hover:bg-gray-50">
                            {{-- Display user data from the model --}}
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $user->id }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{-- Editable username field --}}
                                <input
                                    type="text"
                                    name="name" {{-- This name must match controller validation/update logic --}}
                                    value="{{ old('name', $user->name) }}" {{-- Use old() helper for repopulation on validation error --}}
                                    class="shadow-sm appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name', 'user_'.$user->id) border-red-500 @enderror" {{-- Basic error styling --}}
                                >
                                {{-- Display validation error for this specific user's name field if needed --}}
                                {{-- @error('name', 'user_'.$user->id) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror --}}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{-- Editable email field --}}
                                <input
                                    type="email"
                                    name="email" {{-- This name must match controller validation/update logic --}}
                                    value="{{ old('email', $user->email) }}"
                                    class="shadow-sm appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email', 'user_'.$user->id) border-red-500 @enderror"
                                >
                                {{-- @error('email', 'user_'.$user->id) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror --}}
                            </td>
                            {{-- Add other relevant user table columns here --}}
                            {{-- <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $user->email_verified_at ? $user->email_verified_at->format('Y-m-d') : 'No' }}</td> --}}
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : 'N/A' }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{--
                                    Placeholder Permissions - Replace with actual permission logic for Kanka.
                                    You would typically loop through $allPermissions or $allRoles fetched in the controller.
                                    Check if the current $user has the permission/role using methods provided
                                    by Kanka's permission system (e.g., $user->hasPermissionTo($permission->name) or $user->hasRole($role->name)).
                                    The 'name' attribute for checkboxes should be an array like 'permissions[]' or 'roles[]'
                                    so they are submitted correctly to the controller.
                                --}}
                                <div class="flex flex-col space-y-1">
                                    <span class="text-xs text-gray-500 italic">(Implement Kanka Permission Logic Here)</span>
                                    {{-- Example Loop (using Spatie Permissions - adapt as needed): --}}
                                    {{-- @isset($allPermissions)
                                        @foreach ($allPermissions as $permission)
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="checkbox"
                                                    name="permissions[]" {{-- Submit as an array --}}
                                                    value="{{ $permission->name }}" {{-- Or $permission->id --}}
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                    {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }} {{-- Check if user has it --}}
                                                >
                                                <span class="ml-2 text-xs">{{ $permission->name }}</span>
                                            </label>
                                        @endforeach
                                    @endisset --}}
                                     {{-- Example Role Assignment (using Spatie) --}}
                                    {{-- @isset($allRoles)
                                        <select name="roles[]" multiple class="form-multiselect block w-full mt-1 rounded text-xs">
                                            @foreach($allRoles as $role)
                                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endisset --}}
                                </div>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <div class="flex items-center space-x-2">
                                    {{-- Submit button for the specific user's form --}}
                                    <button type="submit" form="user-form-{{ $user->id }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">Save</button>

                                    {{-- Password Change: Link to a dedicated page/modal for changing this user's password --}}
                                    {{-- Ensure this route exists and is handled by a controller method --}}
                                    {{-- <a href="{{ route('admin.users.password.edit', $user->id) }}" class="text-gray-600 hover:text-gray-900 text-xs font-medium">Change Pass</a> --}}

                                    {{-- Delete User (Add confirmation!) - Typically its own form/route --}}
                                    {{-- <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Are you sure you want to delete user {{ $user->name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">Delete</button>
                                    </form> --}}
                                </div>
                            </td>
                        </tr>
                    </form> {{-- End form for this user --}}
                @empty
                    <tr>
                        {{-- Adjust colspan based on the actual number of columns --}}
                        <td colspan="7" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">No users found matching your search criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links - Renders if you used paginate() in the controller --}}
    <div class="mt-6">
        {{-- Check if $users is paginated before trying to render links --}}
        @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
             {{ $users->appends(request()->query())->links() }} {{-- appends() keeps search query in pagination links --}}
        @endif
    </div>

</div>
{{-- @endsection --}}