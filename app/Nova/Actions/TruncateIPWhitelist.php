<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Heading;

class TruncateIPWhitelist extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */

    public $name = 'Truncate IP Whitelist';

    public function handle(ActionFields $fields, Collection $models)
    {
    if ($models->count() > 1) {
        return Action::danger('Please run this on only one resource.');
    }
     
     $selectModel = $models->first();


    if (auth()->user()->id != $selectModel->ownedBy) {
        if(auth()->user()->admin != '1') {
            return Action::danger('You are not authorized to truncate whitelist for this resource.');
        }
    }

    if($selectModel->allowed_ips === NULL) {
            return Action::danger('Whitelist seems empty already.');
    }

    $models->first()->update(['allowed_ips' => NULL]);

    return Action::message('Whitelist for this API config has been truncated, please re-add IP to whitelist.');


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

        ];
    }
}
