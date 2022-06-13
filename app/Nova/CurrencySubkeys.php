<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;
use Inspheric\Fields\Indicator;
use Laravel\Nova\Panel;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;


class CurrencySubkeys extends ResourceRegular
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
    public static $model = \App\Models\Slotlayer\Gameoptions::class;

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
        return 'Fiat Currencies';
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
        
            Select::make('Fiat Currency', 'native_currency')->options([
                'USD' => 'US Dollar',
                'EUR' => 'Euro', 
            ])->rules('required')->default('USD')
            ->readonly(function ($request) {
                    return $request->user()->admin != "1";
            }),

 
            Text::make('Parent API Key', 'parent_key')
                ->sortable()->displayUsing(function ($value) {
                            return Str::limit($value, 20);
                })
                ->default(function ($request) {
                    return Str::uuid().'-'.rand(1000, 999999);
                })
                ->rules('required', 'max:55', 'min:10')
                ->readonly(function() {
                    return $this->resource->id ? true : false;
                }),
            new Panel('Activity', $this->activityFields()),

            Boolean::make('Active', 'active')
                ->trueValue('1')
                ->hideWhenUpdating(function ($request) {
                        return $request->user()->admin != "1";
                })
                ->readonly(function ($request) {
                        return $request->user()->admin != "1";
                })
                ->falseValue('0'),

            

        ];
    }


    protected function activityFields()
    {
        return [
            DateTime::make('Last Activity', 'updated_at')->displayUsing(function($lastActive) {
                if ($lastActive === null) {
                        return null;
                    }
                return $lastActive->diffForHumans();
            })->hideWhenUpdating()->hideWhenCreating()->readonly()->sortable(),

            Text::make('Real Sessions', 'real_sessions')
                ->default(0)
                ->readonly()
                ->rules('required', 'max:5', 'min:5'),

            Text::make('Demo Sessions', 'demo_sessions')
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
