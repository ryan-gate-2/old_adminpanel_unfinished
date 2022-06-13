<?php

namespace App\Console\Commands;
use App\Models\Slotlayer\CurrencyPrices;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slotlayer:updateprices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update USD value for currencies';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currencies = CurrencyPrices::all();
        
        $requestPrices = Http::get('https://apilayer.net/api/live?access_key=4196a42c3f74d661d3a808d0aa31b39c&currencies=EUR,GBP,TRY,CAD,PLN,TND,BRL,AUD,CHF,KRW,INR&source=USD&format=1');
        $decodedPrices = json_decode($requestPrices, true);
        if($decodedPrices['success'] === true) {
        $quotes = $decodedPrices['quotes'];

        foreach($currencies as $currency) {
            $currencyName = 'USD'.$currency->currency;

            $currency->update([
                'price' => $quotes[$currencyName],
                'updated_at' => now(),
            ]);
        }
    }
    }
}