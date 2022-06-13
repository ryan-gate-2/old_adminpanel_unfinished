<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Panel;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;

class Gameoptions extends ResourceRegular
{

    public static function authorizedToCreate(Request $request)
    {
        if(auth()->user()) {
            if($request->user()->admin != '1') {
                return false;
            } else {
                return true;
            }
        } else {
          return false;
        }
    }
    public function authorizedToReplicate(Request $request)
    {
        if(auth()->user()) {
            if($request->user()->admin != '1') {
                return false;
            } else {
                return true;
            }
        } else {
          return false;
        }
    }



    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        if($request->user()->id == $this->resource->ownedBy) {
            return true;
        } elseif($request->user()->admin == '1') {
            return true;
        } else {
            return false;
        }
    }
    public static function detailQuery(NovaRequest $request, $query)
    {
        if($request->user()->admin != '1') {
            return $query->where('ownedBy', $request->user()->id);
        } else {
            return $query;
        }
    }

    
    public static function indexQuery(NovaRequest $request, $query)
    {
        if($request->user()->admin != '1') {
            return $query->where('ownedBy', $request->user()->id);
        } else {
            return $query;
        }
    }



    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Slotlayer\GameoptionsParent::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'apikey';

    public static function perPageOptions()
    {
        return [50, 100, 150, 250, 500];
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    public static function label()
    {
        return 'Webhook Settings';
    }

    public static function factoryWord()
    {
        $faker = \Faker\Factory::create();
        return $faker->word();
    }
    
        /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            BelongsTo::make('User')->rules('required', 'max:50', 'min:3')
            ->hideFromIndex(function ($request) {
                    return $request->user()->admin != "1";
            })
            ->readonly(function ($request) {
                    return $request->user()->admin != "1";
            }), 
            
            BelongsTo::make('Access', 'accessprofile', 'App\Nova\AccessProfiles')->hideFromIndex(function ($request) {
                    return $request->user()->admin != "1";
            })
                ->hideWhenUpdating(function ($request) {
                        return $request->user()->admin != "1";
                })
            ->readonly(function ($request) {
                    return $request->user()->admin != "1";
            }),

        
            Text::make('API Key', 'apikey_parent')
                ->sortable()
                ->default(function ($request) {
                    return Str::uuid().'-'.rand(1000, 999999);
                })
                ->rules('required', 'max:55', 'min:10')
                ->readonly(function() {
                    return $this->resource->id ? true : false;
                }),

                Password::make('Secret Password', 'operator_secret')
                ->sortable()
                ->readonly()
                ->hideFromIndex()
                ->help('Go to index page and select dots to regenerate new secret key.')
                ->rules('required', 'max:32', 'min:3')
                ->default(Str::random(12))->withMeta(['extraAttributes' => ['type' => 'password']]),

            Boolean::make('Active', 'active')
                ->trueValue('1')
                ->hideWhenUpdating(function ($request) {
                        return $request->user()->admin != "1";
                })
                ->readonly(function ($request) {
                        return $request->user()->admin != "1";
                })
                ->falseValue('0'),

            
            new Panel('Operator Configuration', $this->configurationFields()),
            new Panel('Activity', $this->activityFields()),

        ];
    }

    protected function configurationFields()
    {
        return [
            Text::make('API Endpoint URL', 'callbackurl')
                ->hideFromIndex()
                ->help('Make sure your API base endpoint ends with a slash, you can review completed endpoints after updating.')
                ->default('http://betboi.io/api/callback/tollgate/')
                ->rules('required', 'max:128', 'min:3'),

            Text::make('Allowed IPs', 'allowed_ips')
                ->hideFromIndex()
                ->help('Add IP from action overview. Read documentation for guidance to add IPs.')
                ->default('123.123.123.123')
                ->readonly()
                ->rules('required', 'max:128', 'min:3'),

            Text::make('Casino Website URL', 'operatorurl')
                ->hideFromIndex()
                ->help('Casino URL is used in various games to redirect player on cashier buttons and on errors.')
                ->default('http://betboi.io')
                ->rules('required', 'max:128', 'min:3'),

                    Text::make('Webhook Endpoints (Callback Base + Prefix)',
                        function () {

                        return '<b>Balance:</b> <i>'.$this->resource->callbackurl.$this->resource->slots_prefix.'/balance</i>
                                <br>
                                <b>Bet:</b> <i>'.$this->resource->callbackurl.$this->resource->slots_prefix.'/bet</i>
                                ';
                    })->hideFromIndex()->hideWhenUpdating()->asHtml(), 

        Boolean::make('Return Log', 'return_log')
            ->trueValue('1')
            ->hideWhenUpdating(function ($request) {
                    return $request->user()->admin != "1";
            })
            ->default('0')
            ->readonly(function ($request) {
                    return $request->user()->admin != "1";
            })
            ->falseValue('0'),

            ];
    }


    protected function activityFields()
    {
        return [
            DateTime::make('Created', 'created_at')->hideWhenUpdating()->hideWhenCreating()->readonly()->sortable(),
            DateTime::make('Last Activity', 'updated_at')->hideWhenUpdating()->hideWhenCreating()->readonly()->sortable()->displayUsing(function($lastActive) {
                if ($lastActive === null) {
                        return null;
                    }
                return $lastActive->diffForHumans();
            }),
            Text::make('Real Sessions Created', 'real_sessions_stat')
                ->hideFromIndex()
                ->default(0)
                ->readonly()
                ->rules('required', 'max:5', 'min:5'),
            ];


    }
        /** 
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
    return [
        (new Actions\ViewOperatorSecret)->onlyOnTableRow()->showOnDetail()
            ->confirmText(' Do you want generate new operator secret? Operator secret is only show once. It will immediately invalidate the old operator secret for any use.')
            ->confirmButtonText('Activate the above new secret key')
            ->cancelButtonText("Cancel"),
        (new Actions\AddAllowedIP)->onlyOnTableRow()->showOnDetail()
            ->confirmButtonText('Add IP to allowed list')
            ->cancelButtonText("Cancel"),
        (new Actions\TruncateIPWhitelist)->onlyOnTableRow()->showOnDetail()
            ->confirmButtonText('Truncate IP Whitelist')
            ->cancelButtonText("Cancel"),
        ];
    }
}
