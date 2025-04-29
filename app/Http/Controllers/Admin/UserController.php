<?php

namespace App\Http\Controllers\Admin; // Ensure this namespace matches your application structure

use App\Http\Controllers\Controller;
use App\Models\User; // Import the User model - adjust namespace if needed
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade for checking logged-in user
use Illuminate\Validation\Rule; // Needed for unique email validation on update

// If using a permissions package like Spatie, you might import Role/Permission models:
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Apply middleware for authorization (Optional - can be done in routes).
     * We will perform checks within each method instead.
     */
    public function __construct()
    {
        // Ensure user is authenticated (applied via route group)
        // $this->middleware('auth');
    }

    /**
     * Display a listing of the resource (the user management page).
     * Corresponds to the GET /admin/users route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // --- Authorization Check ---
        // Check if the authenticated user has the global 'admin' role.
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
             abort(403, 'Unauthorized action. You must be an administrator.');
        }

        // --- Fetch Users with Search ---
        $searchEmail = $request->input('search_email'); // Get search term from query string
        $query = User::query(); // Start building a query on the User model

        if ($searchEmail) {
            // If a search term exists, filter by email
            $query->where('email', 'like', '%' . $searchEmail . '%');
        }

        // Fetch paginated results, ordered by ID
        // Consider adding ->with('roles') or ->with('permissions') if needed for the view
        $users = $query->orderBy('id')->paginate(15); // Adjust pagination count as needed

        // --- Fetch Roles/Permissions (If managing them on this page) ---
        // This depends heavily on how Kanka manages permissions.
        // You would need to fetch the necessary data here to populate checkboxes/selects in the view.
        // Example using Spatie Laravel Permissions package:
        // $allPermissions = Permission::all();
        // $allRoles = Role::all();

        // --- Return the View ---
        // Pass the fetched users (and optionally roles/permissions) to the Blade view.
        // Make sure the view name 'admin.users' matches the file path (resources/views/admin/users.blade.php).
        return view('admin.users', compact('users' /*, 'allPermissions', 'allRoles'*/));
    }

    /**
     * Show the form for creating a new resource.
     * Not implemented for this example page.
     */
    public function create()
    {
        abort(404); // Or redirect, or implement if needed later
    }

    /**
     * Store a newly created resource in storage.
     * Not implemented for this example page.
     */
    public function store(Request $request)
    {
        abort(404); // Or redirect, or implement if needed later
    }

    /**
     * Display the specified resource.
     * Not typically needed for an admin list/edit page. User profiles might handle this.
     */
    public function show(User $user) // Using Route Model Binding
    {
        abort(404); // Or redirect, or implement if needed later
    }

    /**
     * Show the form for editing the specified resource.
     * Not needed if editing is done directly on the index page table.
     */
    public function edit(User $user) // Using Route Model Binding
    {
         abort(404); // Or redirect, or implement if needed later
    }

    /**
     * Update the specified user in storage.
     * Corresponds to the PUT /admin/users/{user} route.
     * Handles the form submission from the user list page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user  (Using Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user) // Using Route Model Binding
    {
        // --- Authorization Check ---
        // Check if the authenticated user has the global 'admin' role.
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action. You must be an administrator.');
        }

        // --- Validation ---
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            // Ensure email is unique, but ignore the current user's own email address
            'email' => [
                'required',
                'email',
                'max:191',
                Rule::unique('users')->ignore($user->id),
            ],
            // Validate permissions array if you are submitting them
            // The exact validation depends on how permissions are submitted (e.g., array of IDs, names)
            'permissions' => 'nullable|array',
            // Add validation for any other fields you allow editing
        ]);

        // --- Update User Model ---
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        // Update other basic fields from the User model if needed
        // $user->some_other_field = $request->input('some_other_field');

        // --- Update Permissions/Roles ---
        // This section is highly dependent on Kanka's specific permission system.
        // You MUST implement the logic here based on how Kanka stores roles/permissions.
        // This might involve checking the $request->input('permissions') array
        // and using methods like $user->syncRoles(), $user->syncPermissions(),
        // or manually updating pivot tables or flags on the user model.
        // Example using Spatie Laravel Permissions package (REPLACE THIS):
        /*
        if ($request->has('permissions')) {
            // Get the list of submitted permission names/IDs
            $permissionsToSync = $request->input('permissions', []);
            // Sync the permissions (removes old, adds new)
            $user->syncPermissions($permissionsToSync);
        } else {
             // If no permissions array is sent, maybe remove all direct permissions
             $user->syncPermissions([]);
        }
        // Similarly, you might sync roles:
        // $rolesToSync = $request->input('roles', []); // Assuming roles are submitted
        // $user->syncRoles($rolesToSync);
        */

        // --- Save the User ---
        $user->save(); // Persist changes to the database

        // --- Redirect Back ---
        // Redirect back to the user list page with a success message
        return redirect()->route('admin.users.index')
                         ->with('success', 'User "' . $user->name . '" updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * Corresponds to the DELETE /admin/users/{user} route (if you add it).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // --- Authorization Check ---
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }
        // Prevent users from deleting themselves?
        if (Auth::id() === $user->id) {
             return redirect()->route('admin.users.index')->withErrors(['error' => 'You cannot delete your own account.']);
        }

        // --- Delete User ---
        $userName = $user->name; // Store name for message before deleting
        // You might need to detach roles/permissions or handle related data before deleting
        $user->delete();

        // --- Redirect Back ---
        return redirect()->route('admin.users.index')
                         ->with('success', 'User "' . $userName . '" deleted successfully.');

        // abort(501); // Remove this if implementing delete
    }

    // --- Optional Methods for Password Change ---
    // You would need corresponding routes and views for these

    /**
     * Show the form for editing the specified user's password.
     */
    // public function editPassword(User $user)
    // {
    //     if (!Auth::user() || !Auth::user()->hasRole('admin')) { abort(403); }
    //     return view('admin.users_password', compact('user')); // Need to create this view
    // }

    /**
     * Update the specified user's password in storage.
     */
    // public function updatePassword(Request $request, User $user)
    // {
    //     if (!Auth::user() || !Auth::user()->hasRole('admin')) { abort(403); }
    //     $request->validate([
    //         'password' => 'required|string|min:8|confirmed', // Add Kanka's password rules
    //     ]);
    //     $user->password = bcrypt($request->password); // Hash the new password
    //     $user->save();
    //     return redirect()->route('admin.users.index')->with('success', 'Password for ' . $user->name . ' updated.');
    // }
}