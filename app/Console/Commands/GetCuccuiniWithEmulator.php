<?php

namespace App\Console\Commands;

use App\ScrapedProducts;
use App\Product;
use App\Brand;
use App\CronJobReport;
use App\Services\Bots\CucLoginEmulator;
use App\Setting;
use App\Services\Bots\WebsiteEmulator;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Wa72\HtmlPageDom\HtmlPageCrawler;


class GetCuccuiniWithEmulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cuccu:get-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $country;
    protected $IP;

    public function handle(): void
    {
        $report = CronJobReport::create([
        'signature' => $this->signature,
        'start_time'  => Carbon::now()
     ]);

        $letters = env('SCRAP_ALPHAS', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        if (strpos($letters, 'C') === false) {
            return;
        }

        $this->authenticate();

        $report->update(['end_time' => Carbon:: now()]);
    }

    private function authenticate() {
        $url = 'http://shop.cuccuini.it/it/register.html';

        $duskShell = new CucLoginEmulator();
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $content = $duskShell->emulate($this, $url, '');
        } catch (Exception $exception) {
            $content = ['', ''];
        }

    }

    public function doesProductExist($url): bool
    {
        $duskShell = new CucLoginEmulator();
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $content = $duskShell->emulate($this, $url, '');
        } catch (Exception $exception) {
            $content = ['', ''];
        }

        if (strlen($content[0]) > 3 && strlen($content[1]) > 4) {
            return true;
        }

        return false;
    }


    private function setCountry(): void
    {

        $this->country = 'IT';
    }

    private function setIP(): void
    {
        $this->IP = '5.61.4.70	' . ':' . '8080';
    }
}
