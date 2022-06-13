<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;

use App\Nova\User;
use Illuminate\Http\Request;
use App\Nova\Dashboards\Main;
use App\Nova\Gameoptions;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Menu\MenuGroup;
use App\Nova\CreateSessionErrorsLog;
use App\Nova\CallbackErrorsLog;
use App\Nova\AccessProfiles;
use App\Nova\ProviderBaseList;
use App\Nova\AccessProvidersList;
use App\Nova\CurrencySubkeys;
use App\Nova\GameTransactionHistory;
use App\Nova\GameTransactionLiveHistory;
use App\Nova\GamelistBaseView;
use App\Nova\ProfileGamesList;


class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::mainMenu(function (Request $request) {

            $userpanel = [
                MenuSection::dashboard(Main::class)->icon('chart-bar'),
                MenuSection::make('User Profile')->path('/resources/users/'.auth()->user()->id)->icon('key'),
                MenuSection::make('API', [
                    MenuItem::resource(Gameoptions::class),
                    MenuItem::resource(CurrencySubkeys::class),
                    MenuItem::resource(GameTransactionHistory::class),
                    MenuItem::resource(ProfileGamesList::class),


                    MenuGroup::make('Logs', [
                            MenuItem::resource(CreateSessionErrorsLog::class),
                            MenuItem::resource(CallbackErrorsLog::class),
                    ])
                ])->icon('lightning-bolt')->collapsable(),

                MenuSection::make('Links', [
                    MenuItem::externalLink('Docs', 'https://docs.tollgate.io'),
                    MenuItem::externalLink('Support', 'https://docs.tollgate.io'),
                ])->icon('information-circle')->collapsable(),
            ];

            $adminpanel = [
                MenuSection::make('Staff', [
                    MenuItem::resource(GameTransactionLiveHistory::class),
                    MenuItem::resource(User::class),

                    MenuGroup::make('Base List', [
                        MenuItem::resource(ProviderBaseList::class),
                        MenuItem::resource(GamelistBaseView::class),
                    ]),
                    MenuGroup::make('Profiles', [
                        MenuItem::resource(AccessProfiles::class),
                        MenuItem::resource(AccessProvidersList::class),
                    ])
                    ])->icon('shield-check')->collapsable(),
            ];

        if($request->user()->admin === 1) {
            return array_merge($userpanel, $adminpanel);
        } else {
            return $userpanel;
        }




        });
        Nova::userMenu(function (Request $request, Menu $menu2) {

            $menu2->prepend(
                MenuSection::make('Edit User Settings')->path('/resources/users/'.auth()->user()->id.'/edit')->icon('key'),

            );

            return $menu2;
        });





    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                
            ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
