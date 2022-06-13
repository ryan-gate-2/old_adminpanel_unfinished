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
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Line;

class ProviderBaseList extends ResourceRegular
{
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
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
    public static $model = \App\Models\Slotlayer\ListProvidersBase::class;

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


    public static $indexDefaultOrder = [
        'index_rating' => 'desc'
    ];

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
        return 'Provider List Base';
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
            Text::make('#',
            function () {
                return "<img style='border-radius: 4px;' width='35px' height='35px' src='https://dkimages.imgix.net/v2/image_sq_alt/providers/small/".$this->provider_id.".svg?fit=crop&auto=compress&w=70&h=70&q=70' >";
            })->asHtml()->hideFromDetail(),

            Stack::make('Details', [
                        Line::make('Error', 'provider_name')->asHeading(),
                        Line::make('Error Message', 'provider_id')->displayUsing(function ($value) {
                            return Str::limit($value, 50);
                        })->asSmall(),
            ])->hideWhenUpdating()->hideFromDetail(),
            Text::make('Provider ID', 'provider_id')
                ->sortable()
                ->hideFromIndex()
                ->readonly(),

            Text::make('Provider Name', 'provider_name')
                ->sortable()
                ->hideFromIndex()
                ->readonly(),
            Text::make('Base Index Rating', 'index_rating')
                ->sortable()
                ->readonly(),
        Text::make('Type', 'type')
                ->sortable()
                ->readonly(),
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
            ->readonly(function ($request) {
                    return $request->user()->admin != "1";
            })
            ->falseValue('0'),

            ];
    }


    protected function activityFields()
    {
        return [
            Text::make('API Endpoint URL', 'callbackurl')
                ->hideFromIndex()
                ->default('http://casinourl.com/api/callback/bulkbet/')
                ->rules('required', 'max:128', 'min:3'),

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
