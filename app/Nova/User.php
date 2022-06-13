<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Select;
use Inspheric\Fields\Indicator;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\UiAvatar;
use Laravel\Nova\Fields\Boolean;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name'; 

    public static function label()
    {
        return 'User Management';
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email',
    ];

    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/resources/users';
    }

    public static function authorizedToCreate(Request $request)
    {
        if($request->user()) {
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

    public function authorizedToDelete(Request $request)
    {
        return false;
    }
    
    public function authorizedToUpdate(Request $request)
    {
        if($request->user()) {
            if($request->user()->id == $this->resource->id) {
                return true;
            } elseif($request->user()->admin == '1' || $request->user()->support_user == '1') {
                return true;
            } else {
                return false;
            }
        } else {
                return false;
        }
    }
    public static function detailQuery(NovaRequest $request, $query)
    {
        if($request->user()) {
            if($request->user()->admin != '1') {
                return $query->where('id', $request->user()->id);
            } else {
                return $query;
            }
        } else {
            return;
        }
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if($request->user()) {
            if($request->user()->admin != '1') {
                return $query->where('id', $request->user()->id);
            } else {
                return $query;
            }
        } else {
            return;
        }
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
            UiAvatar::make('#')->bold()->disableDownload(),
            Text::make('Name')
                ->sortable()->readonly(function ($request) {
                    return $request->user()->admin != "1";
            })->rules('required', 'max:25'),
            ID::make('ID')->asBigInt(),
            Hidden::make('ID')->hideWhenUpdating()->rules('required', 'max:9')->creationRules('unique:users,id')->default(rand(10000000, 900000000)),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:100')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),
                /*
            DateTime::make('Last Billing', 'last_bill')->hideWhenCreating()->readonly()->sortable(),
            */
            DateTime::make('Creation Date', 'created_at')->hideWhenCreating()->readonly()->sortable(),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),



        Boolean::make('Active')
            ->trueValue('1')
            ->hideWhenUpdating(function ($request) {
                    return $request->user()->admin != "1";
            })
            ->readonly(function ($request) {
                    return $request->user()->admin != "1";
            })
            ->falseValue('0'),

            //new Panel('Agreement', $this->userAgreement()),

            new Panel('Extra', $this->slotsFields()),

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


    protected function slotsFields()
    {
        return [
            Textarea::make('Extra Notes', 'note')
            ->alwaysShow()
            ->rules('max:999')
            ->withMeta(['extraAttributes' => [
                'placeholder' => 'Extra notes..']
            ]),
        ];
    }

    protected function userAgreement()
    {
        return [
            Textarea::make('Interval Invoice', 'billing_cycle')
            ->alwaysShow()
            ->readonly(function ($request) {
                    return $request->user()->admin != "1";
            })
            ->rules('max:999')
            ->withMeta(['extraAttributes' => [
                'placeholder' => 'Extra notes..']
            ]),
        ];
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
