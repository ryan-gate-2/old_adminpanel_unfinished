<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;

class PopulateSubGamelist extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */

    public $name = 'Populate Sub Gameslist';

    public function handle(ActionFields $fields, Collection $models)
    {
    if ($models->count() > 1) {
        return Action::danger('Please run this on only one resource.');
    }
     
     $selectModel = $models->first();
    
    if(auth()->user()->admin != '1') {
            return Action::danger('You are not authorized to run this action.');
    }

    if($fields->mode === 'reset') {
        $reset = \App\Models\Slotlayer\AccessGamesList::where('access_profile', '=', $selectModel->id)->delete();
    }
    if($fields->mode === 'delete') {
        $reset = \App\Models\Slotlayer\AccessGamesList::where('access_profile', '=', $selectModel->id)->delete();
        \Artisan::call('optimize:clear');
        return Action::message('Deleted all providers.');
    }

    if($fields->mode === 'index_rating_generate') {
        $getTheGames = \App\Models\Slotlayer\AccessGamesList::all()->where('access_profile', $selectModel->id);

    foreach($getTheGames as $basegame) {

        $searchIfExistsInModel = \App\Models\Slotlayer\AccessProviders::where('access_profile', '=', $selectModel->id)->where('provider', ' =', $basegame->game_provider)->first();



        if(!$searchIfExistsInModel) {

            \App\Models\Slotlayer\AccessGamesList::insert([
                'game' => $basegame->game_slug,
                'name' => $basegame->game_name,
                'demo' => $basegame->demo_mode,
                'type' => $basegame->type,
                'descr' => $basegame->game_desc ?? 'Game',
                'bonusbuy' => $basegame->bonusbuy,
                'rtp' => $basegame->rtp ?? rand(97.98, 98.29),
                'provider' => $basegame->game_provider,
                'access_profile' => $selectModel->id,
                'active' => $basegame->disabled,
                'hidden' => $basegame->hidden,
                'current_index_rating' => 0,
                'base_index' => $basegame->index_rating ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
        \Artisan::call('optimize:clear');

        return Action::message('Populate upsert completed.');

    }


    if($fields->mode === 'upsert') {

    foreach(\App\Models\Slotlayer\GamelistBase::all() as $basegame) {

        $searchIfExistsInModel = \App\Models\Slotlayer\AccessGamesList::where('access_profile', '=', $selectModel->id)->where('provider', ' =', $basegame->game_provider)->first();


        if(!$searchIfExistsInModel) {

            \App\Models\Slotlayer\AccessGamesList::insert([
                'game' => $basegame->game_slug,
                'name' => $basegame->game_name,
                'demo' => $basegame->demo_mode,
                'type' => $basegame->type,
                'descr' => $basegame->game_desc ?? 'Game',
                'bonusbuy' => $basegame->bonusbuy,
                'rtp' => $basegame->rtp ?? rand(97.98, 98.29),
                'provider' => $basegame->game_provider,
                'access_profile' => $selectModel->id,
                'active' => $basegame->disabled,
                'hidden' => $basegame->hidden,
                'current_index_rating' => 0,
                'base_index' => $basegame->index_rating ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
        \Artisan::call('optimize:clear');

        return Action::message('Populate upsert completed.');

    }

            \Artisan::call('optimize:clear');

        return Action::message('Index rating re-populated.');

    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {

        return [

            Text::make('Fudge Factor', 'fudge_factor')->help('Increase this number to increase scrambling game list to appear more randomized (5 to 25).')->default(5)->rules('required', 'min:1', 'max:5'),
            Select::make('Mode', 'mode')->options([
                'upsert' => 'Upsert games you have access to profile gamelist',
                'index_rating_generate' => 'Generate the index based on provider & game profile index base ratings.',
                'index_rating' => 'Reset index rating',
                'delete' => 'Delete all providers from this profile'
            ]),
        ];
    }
}
