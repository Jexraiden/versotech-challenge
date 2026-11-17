<?php

namespace App\Http\Middleware;

use App\Services\CatalogQueries;
use App\Services\SessionCart;
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
        $user = $request->user();
        $navigation = null;
        if ($user !== null) {
            $navigation = [
                'categories' => app(CatalogQueries::class)->categoriesForPriceTable($user->company->price_table_id),
                'cart' => app(SessionCart::class)->present($request, $user),
            ];
        }

        return [
            ...parent::share($request),
            'auth' => ['user' => $user === null ? null : ['name' => $user->name]],
            'navigation' => $navigation,
            'flash' => [
                'cart_success' => fn () => $request->session()->get('cart_success'),
            ],
        ];
    }
}
