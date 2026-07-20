<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuPermission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    private const ROLES = ['manager', 'user'];
    private const MENU_KEYS = ['dashboard', 'whatsapp', 'whatsapp-inbox', 'customers', 'csv'];

    public function edit()
    {
        $permissions = MenuPermission::query()
            ->whereIn('role', self::ROLES)
            ->get()
            ->groupBy('role');

        return view('admin.permissions', [
            'roles' => self::ROLES,
            'menuKeys' => self::MENU_KEYS,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'visible' => ['array'],
            'visible.*.*' => ['boolean'],
        ]);

        $visible = $validated['visible'] ?? [];

        foreach (self::ROLES as $role) {
            foreach (self::MENU_KEYS as $menuKey) {
                MenuPermission::updateOrCreate(
                    ['role' => $role, 'menu_key' => $menuKey],
                    ['is_visible' => isset($visible[$role][$menuKey])]
                );
            }
        }

        return back()->with('success', 'Menu permissions updated successfully.');
    }
}