<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use App\Models\Slotlayer\Gametransactions;


class RecentGameTransactions extends Trend
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        if(auth()->user()->admin == '1') {
            return $this->countByHours($request, Gametransactions::class);
        } else {
            return $this->countByHours($request, Gametransactions::where('ownedBy', auth()->user()->id));
        }
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            12 => __('12 Hours'),
            3 => __('3 Hours'),
            6 => __('6 Hours'),
            24 => __('24 Hours'),
        ];
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'recent-game-transactions';
    }
}
