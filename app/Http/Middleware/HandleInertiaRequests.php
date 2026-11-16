<?php

namespace App\Http\Middleware;

use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    public function share(\Illuminate\Http\Request $request): array
    {
        return [
            ...parent::share($request),
            'flash' => [
                'cart_success' => fn () => $request->session()->get('cart_success'),
            ],
        ];
    }
}
