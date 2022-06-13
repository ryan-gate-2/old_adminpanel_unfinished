<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Panel;
use Laravel\Nova\Place;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Degecko\NovaFiltersSummary\FiltersSummary;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Line;
use Illuminate\Support\Str;

class GameTransactionHistory extends Resource
{ 

    public static function label()
    {
        return 'Games History';
    }
    
    public static $group = 'API';


    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }
        public static function detailQuery(NovaRequest $request, $query)
    {
        if($request->user()->admin != '1') {
            return $query->where('ownedBy', $request->user()->id);
        } else {
            return $query;
        }
    }
    public static $model = \App\Models\Slotlayer\Gametransactions::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public static $title = 'Game Transactions';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'casinoid', 'player', 'gameid'
    ];

    public static function indexQuery(NovaRequest $request, $query)
    {
        if($request->user()->admin != '1') {
            return $query->where('ownedBy', $request->user()->id);
        } else {
            return $query;
        }
    }


    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Hidden::make('Internal', 'id'),
            DateTime::make(__('Timestamp'), 'created_at_orig'),
            Text::make('TX', 'txid'),

            Text::make('Player', 'player')->readonly()->hideFromIndex(function ($request) {
                    return $request->user()->admin != "1";
            }),

            Stack::make('Bet', [
                Line::make('Bet', 'bet', function () {
                return $this->currency.' '.($this->bet / 100);
                })->asHeading(),
                
                Line::make('Bet', 'bet', function () {
                return '$ '.(number_format(($this->usd_exchange * $this->bet / 100), 4, '.', ''));
                })->asSmall(),
            ])->hideWhenUpdating()->hideFromDetail(),

            Stack::make('Win', [
                Line::make('Win', 'win', function () {
                return $this->currency.' '.($this->win / 100);
                })->asHeading(),
                Line::make('Win', 'win', function () {
                return '$ '.(number_format(($this->usd_exchange * $this->win / 100), 4, '.', ''));
                })->asSmall(),
            ])->hideWhenUpdating()->hideFromDetail(),

            Text::make('Game ID', 'gameid'),
            Text::make('Subkey', function () {
                $casinoid = $this->casinoid;
                return "<a class='underline' href='/resources/currency-subkeys/$casinoid'>click</a>";
            })->sortable()->readonly()->asHtml(), 
            BelongsTo::make('User')->rules('required')
            ->readonly(function ($request) {
                    return $request->user()->admin != "1";
            }),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
