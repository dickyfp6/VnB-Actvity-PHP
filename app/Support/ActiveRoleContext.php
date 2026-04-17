<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;

class ActiveRoleContext
{
    public const SESSION_KEY = 'active_role';

    /**
     * Role priority from highest privilege to lowest.
     */
    public const ROLE_PRIORITY = [
        'direktur_utama',
        'pcx_manager',
        'intercomm',
        'manager',
        'employee',
    ];

    public static function resolve(Request $request, User $user): ?string
    {
        $hasSession = method_exists($request, 'hasSession') && $request->hasSession();
        $availableRoles = self::availableRoles($user);
        if (empty($availableRoles)) {
            return null;
        }

        $sessionRole = $hasSession
            ? self::normalizeRole((string) $request->session()->get(self::SESSION_KEY, ''))
            : null;
        if ($sessionRole !== null && in_array($sessionRole, $availableRoles, true)) {
            return $sessionRole;
        }

        $preferredRole = self::normalizeRole((string) ($user->last_active_role ?? ''));
        $resolved = in_array($preferredRole, $availableRoles, true)
            ? $preferredRole
            : self::defaultRole($availableRoles);

        if ($resolved !== null) {
            if ($hasSession) {
                $request->session()->put(self::SESSION_KEY, $resolved);
            }
            if ($user->last_active_role !== $resolved) {
                $user->forceFill(['last_active_role' => $resolved])->save();
            }
        }

        return $resolved;
    }

    public static function switch(Request $request, User $user, string $role): string
    {
        abort_unless(method_exists($request, 'hasSession') && $request->hasSession(), 400, 'Session tidak tersedia.');

        $targetRole = self::normalizeRole($role);
        abort_unless($targetRole !== null, 422, 'Role tidak valid.');

        $availableRoles = self::availableRoles($user);
        abort_unless(in_array($targetRole, $availableRoles, true), 403, 'Anda tidak memiliki role tersebut.');

        $request->session()->put(self::SESSION_KEY, $targetRole);
        if ($user->last_active_role !== $targetRole) {
            $user->forceFill(['last_active_role' => $targetRole])->save();
        }

        return $targetRole;
    }

    public static function current(Request $request, User $user): ?string
    {
        return self::resolve($request, $user);
    }

    public static function hasActiveRole(Request $request, User $user, array $roles): bool
    {
        $activeRole = self::current($request, $user);
        $normalized = array_values(array_filter(array_map(fn ($role) => self::normalizeRole((string) $role), $roles)));

        return $activeRole !== null && in_array($activeRole, $normalized, true);
    }

    public static function availableRoles(User $user): array
    {
        $roles = $user->getRoleNames()->map(fn (string $role) => self::normalizeRole($role))
            ->filter()
            ->unique()
            ->values()
            ->all();

        usort($roles, function (string $a, string $b): int {
            $priorityA = array_search($a, self::ROLE_PRIORITY, true);
            $priorityB = array_search($b, self::ROLE_PRIORITY, true);

            return ($priorityA === false ? 999 : $priorityA) <=> ($priorityB === false ? 999 : $priorityB);
        });

        return $roles;
    }

    private static function defaultRole(array $availableRoles): ?string
    {
        foreach (self::ROLE_PRIORITY as $role) {
            if (in_array($role, $availableRoles, true)) {
                return $role;
            }
        }

        return $availableRoles[0] ?? null;
    }

    private static function normalizeRole(string $role): ?string
    {
        $normalized = trim(strtolower($role));

        if ($normalized === 'admin') {
            return 'direktur_utama';
        }

        return in_array($normalized, self::ROLE_PRIORITY, true) ? $normalized : null;
    }
}