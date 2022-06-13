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

class ProviderUpsert extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */

    public $name = 'Upsert Missing Prov.';

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
        $reset = \App\Models\Slotlayer\AccessProviders::where('access_profile', '=', $selectModel->id)->delete();
    }
    if($fields->mode === 'delete') {
        $reset = \App\Models\Slotlayer\AccessProviders::where('access_profile', '=', $selectModel->id)->delete();
        \Artisan::call('optimize:clear');
        return Action::message('Deleted all providers.');
    }

    if($fields->mode === 'upsert') {

    foreach(\App\Models\Slotlayer\ListProvidersBase::all() as $provider) {

        $searchIfExistsInModel = \App\Models\Slotlayer\AccessProviders::where('access_profile', '=', $selectModel->id)->where('provider', ' =', $provider->provider_id)->first();

        if(!$searchIfExistsInModel) {
            $pricing = $provider->ggr;

            $finalPrice = $pricing;
            if($fields->pricingmodifier !== 0) {
                if($fields->pricingmodifier < 0) {
                    $finalPrice = $pricing - $fields->pricingmodifier;
                }
                if($fields->pricingmodifier > 0) {
                    $finalPrice = $pricing + $fields->pricingmodifier;
                }
            }

            \App\Models\Slotlayer\AccessProviders::insert([
                'provider' => $provider->provider_id,
                'price' => $finalPrice,
                'access_profile' => $selectModel->id,
                'base_index' => $provider->index_rating,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
        \Artisan::call('optimize:clear');

        return Action::message('Upsert completed.');


    }

    if($fields->mode === 'index_rating') {

    foreach(\App\Models\Slotlayer\ListProvidersBase::all() as $provider) {

        $searchIfExistsInModel = \App\Models\Slotlayer\AccessProviders::where('access_profile', '=', $selectModel->id)->where('provider', ' =', $provider->provider_id)->first();

        if($searchIfExistsInModel) {

            $searchIfExistsInModel->update([
                'base_index' => $provider->index_rating,
                'updated_at' => now(),
            ]);
        }

    }

            \Artisan::call('optimize:clear');

        return Action::message('Index rating re-populated.');


    }



        return Action::message('Completed inserting providers');


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
            Text::make('Bulk Price Modifier', 'pricingmodifier')->help('Uses default pricing from provider base list, you can add or deduct using this form.')->default(0)->rules('required', 'min:1', 'max:66'),

            Select::make('Mode', 'mode')->options([
                'upsert' => 'Upsert only missing providers',
                'reset' => 'Re-populate list, including price settings',
                'index_rating' => 'Reset index rating',
                'delete' => 'Delete all providers from this profile'
            ])->default('Upsert'),

        ];
    }
}
