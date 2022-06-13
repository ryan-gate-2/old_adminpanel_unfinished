<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;
use Coroowicaksono\ChartJsIntegration\StackedChart;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\RecentGameTransactions;
use App\Nova\Metrics\DueGgrPerProvider;

class Main extends Dashboard
{


    public function label()
    {
        return 'Dashboard';
    }
        /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
        new RecentGameTransactions,
        new DueGgrPerProvider,
                new NewUsers,
        ];
    }
}
