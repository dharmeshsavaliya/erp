<?php

namespace App\Console\Commands;

use App\Category;
use App\Product;
use App\ScrapedProducts;
use App\Supplier;
use Illuminate\Console\Command;
use League\Csv\Reader;
use League\Csv\Statement;

class FixCategoryNameBySupplier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:fix-by-supplier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Product::where('is_scraped', 1)->where('category', '<', 4)->orderBy('id', 'DESC')->chunk(1000, function ($products) {
//        Product::where('is_approved', 0)->orderBy('id', 'DESC')->chunk(1000, function ($products) {
            echo 'Chunk again=======================================================' . "\n";
            foreach ($products as $product) {
                $this->classify2($product);
            }
        });

    }

    private function classify2($product) {
        $records = Category::where('id', '>', 3)->whereNotNull('references')->where('references', '!=', '')->orderBy('id', 'DESC')->get();
        foreach ($records as $record) {
            $originalCategory = $record->title;
            $rec = explode(',', $record->references);
            $scrapedProducts = $product->many_scraped_products;

            foreach ($scrapedProducts as $scrapedProduct) {
                $catt = $scrapedProduct->properties['category'] ?? [];
                if (is_array($catt)) {
                    $catt = implode('', $catt);
                }

                foreach ($rec as $kk=>$cat) {
                    $cat = strtoupper($cat);


                    if (stripos(strtoupper($catt), $cat) !== false
                        || stripos(strtoupper($scrapedProduct->title ?? ''), $cat) !== false
                        || stripos(strtoupper($scrapedProduct->url ?? ''), $cat) !== false
                    ) {
                        $gender = $this->getMaleOrFemale($scrapedProduct->properties);

                        dump($gender, $originalCategory, $cat, $catt);

                        if ($gender === false) {
                            $gender = $this->getMaleOrFemale($scrapedProduct->title);
                        }

                        if ($gender === false) {
                            $gender = $this->getMaleOrFemale($scrapedProduct->url);
                        }

                        if ($product->supplier === 'Tory Burch' || $originalCategory == 'Pumps') {
                            $gender = 2;
                        }

                        if ($originalCategory == 'Shirts' && $gender == 2) {
                            $originalCategory = 'Tops';
                        }

                        if ($originalCategory == 'Clutches' && $gender == 2) {
                            $originalCategory = 'Handbags';
                        }


                        if ($originalCategory == 'Coats & Jackets' && $gender == 3) {
                            $originalCategory = 'Coats & Jackets & Suits';
                        }

                        if ($originalCategory == 'Tops' && $gender == 3) {
                            $originalCategory = 'T-Shirts';
                        }

                        if ($originalCategory == 'Skirts') {
                            $gender = 2;
                        }

                        if ($originalCategory == 'Shawls And Scarves' && $gender == 3) {
                            $originalCategory = 'Scarves & Wraps';
                        }

//                    if ($originalCategory == 'Jumper' && $gender == 2) {
//                        $originalCategory = 'Others';
//                    }

                        if ($gender === false) {
                            $this->warn('NOOOOO' . $product->supplier);
                            $product->category = 1;
                            $product->save();
                            continue;
                        }


                        $parentCategory = Category::find($gender);
                        $childrenCategories = $parentCategory->childs;

                        foreach ($childrenCategories as $childrenCategory) {
                            if ($childrenCategory->title == $originalCategory) {
                                $product->category = $childrenCategory->id;
                                $this->error('SAVED');
                                $product->save();
                                return;
                            }

                            $grandChildren = $childrenCategory->childs;
                            foreach ($grandChildren as $grandChild) {
                                if ($grandChild->title == $originalCategory) {
                                    $product->category = $grandChild->id;
                                    $product->save();
                                    $this->error('SAVED');
                                    return;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function classify($product)
    {
        $scrapedProduct = ScrapedProducts::where('sku', $product->sku)->orderBy('id', 'DESC')->first();
        $category = $scrapedProduct->properties['category'] ?? [];
        if ($category === []) {
            dump('TITLE assigned..................................................');
            $category = $scrapedProduct->title;
        } else {
            if (is_array($category)) {
                $category = implode(' ', $category);
            }
        }

        $records = Category::where('id', '>', 3)->whereNotNull('references')->where('references', '!=', '')->orderBy('id', 'DESC')->get();

        foreach ($records as $record) {
            $originalCategory = $record->title;
            if (strlen($record->references) < 3) {
                continue;
            }
            $rec = explode(',', $record->references);
            foreach ($rec as $kk=>$cat) {
                if (stripos(strtoupper($category), strtoupper($cat)) !== false) {
                    $this->info($category . ' =>  ' . $cat . ' ' . $record->id . ' => ' .  $originalCategory);
                    $c = Category::where('title', $originalCategory)->first();
                    if (!$c) {
                        continue;
                    }

                    $gender = $this->getMaleOrFemale($scrapedProduct->properties);

                    if ($gender === false) {
                        $gender = $this->getMaleOrFemale($scrapedProduct->title);
                    }

                    if ($gender === false) {
                        $gender = $this->getMaleOrFemale($scrapedProduct->url);
                    }

                    if ($gender === false) {
                        $this->warn('NOOOOO');
                        $product->category = 1;
                        $product->save();
                        continue;
                    }


                    $parentCategory = Category::find($gender);
                    $childrenCategories = $parentCategory->childs;

                    foreach ($childrenCategories as $childrenCategory) {
                        if ($childrenCategory->title == $originalCategory) {
                            $product->category = $childrenCategory->id;
                            $this->error('SAVED');
                            $product->save();
                            return;
                        }

                        $grandChildren = $childrenCategory->childs;
                        foreach ($grandChildren as $grandChild) {
                            if ($grandChild->title == $originalCategory) {
                                $product->category = $grandChild->id;
                                $product->save();
                                $this->error('SAVED');
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    private function getMaleOrFemale($category) {
        if (is_array($category)) {
            $category = json_encode($category);
        }
        if (is_array($category)) {
            foreach ($category as $cat) {
                if (strtoupper($cat) === 'MAN' ||
                    strtoupper($cat) === 'MEN' ||
                    strtoupper($cat) === 'UOMO' ||
                    strtoupper($cat) === 'UOMONI') {
                    return 3;
                }
            }

            foreach ($category as $cat) {
                if (strtoupper($cat) === 'WOMAN' ||
                    strtoupper($cat) === 'WOMEN' ||
                    strtoupper($cat) === 'DONNA' ||
                    strtoupper($cat) === 'LADIES') {
                    return 3;
                }
            }

            return false;
        }

        $category = strtoupper($category);

        if (strpos($category, 'WOMAN') !== false ||
            strpos($category, 'WOMEN') !== false ||
            strpos($category, 'DONNA') !== false ||
            strpos($category, 'LADY') !== false ||
            strpos($category, 'LADIES') !== false ||
            strpos($category, 'GIRL') !== false
        ) {
            return 2;
        }

        if (strpos($category, 'MAN') !== false ||
            strpos($category, 'MEN') !== false ||
            strpos($category, 'UOMO') !== false ||
            strpos($category, 'GENTS') !== false ||
            strpos($category, 'UOMONI') !== false ||
            strpos($category, 'GENTLEMAN') !== false ||
            strpos($category, 'GENTLEMEN') !== false
        ) {
            return 3;
        }

        return false;
    }
}
