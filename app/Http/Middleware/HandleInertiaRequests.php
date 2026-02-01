<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'role' => $request->user()->role,
                    // Agregamos esto para que Vue lo vea:
                    'profile_photo_url' => $request->user()->profile_photo_url, 
                    'profile' => $request->user()->profile, 
                    
                    'following' => $request->user()->following()
                        ->select('users.id', 'users.name')
                        ->with('profile:user_id,profile_photo_path') 
                        ->get()
                        ->map(fn($u) => [
                            'id' => $u->id,
                            'name' => $u->name,
                            'avatar' => $u->profile_photo_url
                        ]),
                ] : null,
            ],
        ];
    }
    
}
