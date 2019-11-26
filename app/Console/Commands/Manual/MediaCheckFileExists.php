<?php

namespace App\Console\Commands\Manual;

use Illuminate\Console\Command;
use App\Product;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;

class GetProductImageForScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:check-file-exists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Check if images for products exist on the disk and remove media if it doesn't exist";

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
     * @return mixed
     */
    public function handle()
    {
        // Set empty cnt
        $cnt = 0;

        // Get all products
        $products = Product::all();

        // Loop over products
        foreach ($products as $product) {
            // Check for media
            if ($product->hasMedia(config('constants.media_tags'))) {
                $medias = $product->getMedia(config('constants.media_tags'));

                if ($medias != null) {
                    foreach ($medias as $media) {
                        $file = public_path() . '/' . $media->disk . (!empty($media->directory) ? '/' . $media->directory : '') . '/' . $media->filename . '.' . $media->extension;
                        if ( !file_exists($file) ) {
                            echo "REMOVED " . $file . " FROM DATABASE FOR PRODUCT " . $product->id . "\n";
                            $cnt++;
                        }
                    }
                }
            }
        }

        // Output result
        echo "\n" . $cnt . " file(s) deleted\n";
    }
}
