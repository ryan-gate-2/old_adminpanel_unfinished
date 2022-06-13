<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Number;

class AccessProvidersList extends ResourceRegular
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

    public function authorizedToDelete(Request $request)
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
        return false;
    }


    public function authorizedToUpdate(Request $request)
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

    public static function indexQuery(NovaRequest $request, $query)
    {
        if(auth()->user()) {
            if($request->user()->admin != '1') {
                abort(403, 'You have no access here.');
                die();
            } else {
                return true;
            }
        } else {
          return false;
        }
    }

    public static function detailQuery(NovaRequest $request, $query)
    {
        if(auth()->user()) {
            if($request->user()->admin != '1') {
                abort(403, 'You have no access here.');
                die();
            } else {
                return true;
            }
        } else {
          return false;
        }
    }

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Slotlayer\AccessProviders::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'provider';
 
     public static function label()
    {
        return 'Provider Access & Pricing';
    }


    public static function perPageOptions()
    {
        return [200, 400, 700, 2000];
    } 

    /**
     * The columns that should be searched.
     *
     * @var array 
     */
    public static $search = [
        'provider',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Hidden::make('ID')->hideWhenUpdating()->rules('required', 'max:9')->creationRules('unique:access_providers,id'),
            
            BelongsTo::make('Parent Profile', 'accessprofile', 'App\Nova\AccessProfiles')->hideFromIndex(function ($request) {
                    return $request->user()->admin != "1";
            })
                ->hideWhenUpdating(function ($request) {
                        return $request->user()->admin != "1";
                })
            ->readonly(function ($request) {
                    return $request->user()->admin != "1";
            }),

            Text::make('Provider ID', 'provider')
                ->sortable()
                ->rules('required', 'min:1', 'max:100'),


            Text::make('Active Price %', 'price')
                ->sortable()
                ->rules('required', 'min:1', 'max:100'),

            Text::make('Aprox. Costprice %',
            function () {
                return \App\Models\Slotlayer\ListProvidersBase::costPrice($this->resource->provider);
            })->asHtml()->hideFromDetail(),

            Text::make('Default Base %',
            function () {
                return \App\Models\Slotlayer\ListProvidersBase::basePrice($this->resource->provider);
            })->asHtml()->hideFromDetail(),


            

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
        return [];
    }
}
