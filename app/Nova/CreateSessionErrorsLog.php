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
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\DateTime;


class CreateSessionErrorsLog extends ResourceRegular
{

    public static function authorizedToCreate(Request $request)
    {
        return false;

    }


    public function authorizedToUpdate(Request $request)
    {
        return false;
    }


    public function authorizedToDelete(Request $request)
    {
        if($request->user()->id == $this->resource->ownedBy) {
            return true;
        } elseif($request->user()->admin == '1') {
            return true;
        } else {
            return false;
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
    public static function detailQuery(NovaRequest $request, $query)
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
    public static $model = \App\Models\Slotlayer\CreateSessionErrors::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'error_code';

    public static function perPageOptions()
    {
        return [150, 300, 500, 1000];
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'uid',
    ];

    public static function label()
    {
        return 'Create Session Errors';
    }

    public static $indexDefaultOrder = [
        'created_at' => 'desc'
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
            Hidden::make('uid'),
            DateTime::make('Time', 'created_at')->hideWhenCreating()->readonly()->sortable(),

            BelongsTo::make('User')->rules('required', 'max:50', 'min:3')
            ->hideFromIndex(function ($request) {
                    return $request->user()->admin != "1";
            })
            ->readonly(function ($request) {
                    return $request->user()->admin != "1";
            }),
            Stack::make('Details', [
                        Line::make('Error', 'error_code')->asHeading(),
                        Line::make('Error Message', 'error_message')->displayUsing(function ($value) {
                            return Str::limit($value, 50);
                        })->asSmall(),
            ])->hideWhenUpdating()->hideFromDetail(),


            Text::make('API Key', 'apikey')->hideFromIndex()->readonly(),

            Text::make('Error', 'error_code')->hideFromIndex()->readonly(),

            Textarea::make('Error Message', 'error_message')->hideFromIndex()->readonly()->alwaysShow(),
             
        
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
            ->confirmButtonText('Generate new secret key')
            ->cancelButtonText("Cancel"),
    ];
    }
}
