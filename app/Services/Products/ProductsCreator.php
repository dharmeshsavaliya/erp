<?php

namespace App\Services\Products;

use Validator;
use Illuminate\Support\Facades\Log;
use App\Brand;
use App\Category;
use App\ColorNamesReference;
use App\Product;
use App\ProductStatus;
use App\ScrapActivity;
use App\Supplier;
use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;


class ProductsCreator
{
    public function createProduct($image, $isExcel = 0)
    {
        // Debug log
        Log::channel('productUpdates')->debug("[Start] createProduct is called");

        // Set supplier
        $supplier = Supplier::where('scraper_name', $image->website)->first();

        // Do we have a supplier?
        if ($supplier == null) {
            // Debug
            Log::channel('productUpdates')->debug("[Error] Supplier is null " . $image->website);

            // Return false
            return false;
        } else {
            $supplier = $supplier->supplier;
        }

        // Get formatted data
        $formattedPrices = $this->formatPrices($image);
        $formattedDetails = $this->getGeneralDetails($image->properties);

        // Set data.sku for validation
        $data[ 'sku' ] = ProductHelper::getSku($image->sku);
        $validator = Validator::make($data, [
            'sku' => 'unique:products,sku'
        ]);

        // Product validated
        if ($validator->fails()) {
            // Debug
            Log::channel('productUpdates')->debug("[validator] fails - sku exists " . ProductHelper::getSku($image->sku));

            // Try to get the product from the database
            $product = Product::where('sku', $data[ 'sku' ])->first();

            // Does the product exist? This should not fail, since the validator told us it's there
            if (!$product) {
                // Debug
                Log::channel('productUpdates')->debug("[Error] No product!");

                // Return false
                return false;
            }

            // Is the product approved?
            if (!StatusHelper::isApproved($image->status_id)) {
                // Check if we can update - not manually entered
                $manual = ProductStatus::where('name', 'MANUAL_TITLE')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    $product->name = $image->title;
                }

                // Check if we can update - not manually entered
                $manual = ProductStatus::where('name', 'MANUAL_SHORT_DESCRIPTION')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    $product->short_description = $image->description;
                }

                // Check if we can update - not manually entered
                $manual = ProductStatus::where('name', 'MANUAL_COLOR')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    $product->color = ColorNamesReference::getProductColorFromObject($image);
                }

                $manual = ProductStatus::where('name', 'MANUAL_COMPOSITION')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    // Check for composition key
                    if (isset($image->properties[ 'composition' ])) {
                        $product->composition = trim($image->properties[ 'composition' ] ?? '');
                    }

                    // Check for material_used key
                    if (isset($image->properties[ 'material_used' ])) {
                        $product->composition = trim($image->properties[ 'material_used' ] ?? '');
                    }
                }
            }

            // Get current sizes
            $sizes = $product->size;

            // Update with scraped sizes
            if (is_array($image->properties[ 'sizes' ]) && count($image->properties[ 'sizes' ]) >= 1) {
                $sizes = implode(',', $image->properties[ 'sizes' ] ?? []);
            }

            // Store everything again in sizes
            $product->size = $sizes;

            // Store measurement
            $product->lmeasurement = $formattedDetails[ 'lmeasurement' ] > 0 ? $formattedDetails[ 'lmeasurement' ] : null;
            $product->hmeasurement = $formattedDetails[ 'hmeasurement' ] > 0 ? $formattedDetails[ 'hmeasurement' ] : null;
            $product->dmeasurement = $formattedDetails[ 'dmeasurement' ] > 0 ? $formattedDetails[ 'dmeasurement' ] : null;
            $product->price = $formattedPrices[ 'price' ];
            $product->price_inr = $formattedPrices[ 'price_inr' ];
            $product->price_special = $formattedPrices[ 'price_special' ];
            $product->is_scraped = $isExcel == 1 ? $product->is_scraped : 1;
            $product->save();

            if ($image->is_sale) {
                $product->is_on_sale = 1;
                $product->save();
            }

            if ($db_supplier = Supplier::where('supplier', $supplier)->first()) {
                if ($product) {
                    $product->suppliers()->syncWithoutDetaching([
                        $db_supplier->id => [
                            'title' => $image->title,
                            'description' => $image->description,
                            'supplier_link' => $image->url,
                            'stock' => 1,
                            'price' => $formattedPrices[ 'price' ],
                            'price_discounted' => $formattedPrices[ 'price_discounted' ],
                            'size' => $formattedDetails[ 'size' ],
                            'color' => $formattedDetails[ 'color' ],
                            'composition' => $formattedDetails[ 'composition' ],
                            'sku' => $image->original_sku
                        ]
                    ]);
                }
            }

            $dup_count = 0;
            $supplier_prices = [];

            foreach ($product->suppliers_info as $info) {
                if ($info->price != '') {
                    $supplier_prices[] = $info->price;
                }
            }

            foreach (array_count_values($supplier_prices) as $price => $c) {
                $dup_count++;
            }

            if ($dup_count > 1) {
                $product->is_price_different = 1;
            } else {
                $product->is_price_different = 0;
            }

            $product->stock += 1;
            $product->save();

            $supplier = $image->website;

            $params = [
                'website' => $supplier,
                'scraped_product_id' => $product->id,
                'status' => 1
            ];

            ScrapActivity::create($params);

            Log::channel('productUpdates')->debug("[Success] Updated product");

            return;

        } else {
            Log::channel('productUpdates')->debug("[validator] success - new sku " . ProductHelper::getSku($image->sku));
            $product = new Product;
        }

        if ($product === null) {
            Log::channel('productUpdates')->debug("[Skipped] Product is null");
            return;
        }

        $product->status_id = 2;
        $product->sku = str_replace(' ', '', $image->sku);
        $product->brand = $image->brand_id;
        $product->supplier = $supplier;
        $product->name = $image->title;
        $product->short_description = $image->description;
        $product->supplier_link = $image->url;
        $product->stage = 3;
        $product->is_scraped = $isExcel == 1 ? 0 : 1;
        $product->stock = 1;
        $product->is_without_image = 1;
        $product->is_on_sale = $image->is_sale ? 1 : 0;

        $product->composition = $formattedDetails[ 'composition' ];
        $product->color = ColorNamesReference::getProductColorFromObject($image);
        $product->size = $formattedDetails[ 'size' ];
        $product->lmeasurement = (int)$formattedDetails[ 'lmeasurement' ];
        $product->hmeasurement = (int)$formattedDetails[ 'hmeasurement' ];
        $product->dmeasurement = (int)$formattedDetails[ 'dmeasurement' ];
        $product->measurement_size_type = $formattedDetails[ 'measurement_size_type' ];
        $product->made_in = $formattedDetails[ 'made_in' ];
        $product->category = $formattedDetails[ 'category' ];

        $product->price = $formattedPrices[ 'price' ];
        $product->price_inr = $formattedPrices[ 'price_inr' ];
        $product->price_special = $formattedPrices[ 'price_special' ];

        try {
            $product->save();
            Log::channel('productUpdates')->debug("[New] Product created with ID " . $product->id);
        } catch (\Exception $exception) {
            Log::channel('productUpdates')->alert("[Exception] Couldn't create product");
            Log::channel('productUpdates')->alert($exception->getMessage());
            return;
        }

        if ($db_supplier = Supplier::where('supplier', $supplier)->first()) {
            $product->suppliers()->syncWithoutDetaching([
                $db_supplier->id => [
                    'title' => $image->title,
                    'description' => $image->description,
                    'supplier_link' => $image->url,
                    'stock' => 1,
                    'price' => $formattedPrices[ 'price' ],
                    'price_discounted' => $formattedPrices[ 'price_discounted' ],
                    'size' => $formattedDetails[ 'size' ],
                    'color' => $formattedDetails[ 'color' ],
                    'composition' => $formattedDetails[ 'composition' ],
                    'sku' => $image->original_sku
                ]
            ]);
        }
    }

    public function formatPrices($image)
    {
        // Get brand from database
        $brand = Brand::find($image->brand_id);

        // Check for EUR to INR
        if (!empty($brand->euro_to_inr)) {
            $price_inr = (double) $brand->euro_to_inr * (double) $image->price;
        } else {
            $price_inr = (double) Setting::get('euro_to_inr') * (double) $image->price;
        }

        // Set INR price and special price
        $price_inr = round($price_inr, -3);
        $price_special = $price_inr - ($price_inr * $brand->deduction_percentage) / 100;
        $price_special = round($price_special, -3);

        // Return prices
        return [
            'price' => $image->price,
            'price_discounted' => $image->discounted_price,
            'price_inr' => $price_inr,
            'price_special' => $price_special
        ];
    }

    public function getGeneralDetails($properties_array)
    {
        if (array_key_exists('material_used', $properties_array)) {
            $composition = (string)$properties_array[ 'material_used' ];
        }

        if (array_key_exists('color', $properties_array)) {
            $color = $properties_array[ 'color' ];
        }

        if (array_key_exists('sizes', $properties_array)) {
            $sizes = $properties_array[ 'sizes' ];
            $size = implode(',', $sizes);
        }

        if (array_key_exists('dimension', $properties_array)) {
            if (!is_array($properties_array[ 'dimension' ])) {
                if (strpos($properties_array[ 'dimension' ], 'Width') !== false || strpos($properties_array[ 'dimension' ], 'W') !== false) {
                    if (preg_match_all('/Width ([\d]+)/', $properties_array[ 'dimension' ], $match)) {
                        $lmeasurement = (int)$match[ 1 ][ 0 ];
                        $measurement_size_type = 'measurement';
                    }

                    if (preg_match_all('/W ([\d]+)/', $properties_array[ 'dimension' ], $match)) {
                        $lmeasurement = (int)$match[ 1 ][ 0 ];
                        $measurement_size_type = 'measurement';
                    }
                }

                if (strpos($properties_array[ 'dimension' ], 'Height') !== false || strpos($properties_array[ 'dimension' ], 'H') !== false) {
                    if (preg_match_all('/Height ([\d]+)/', $properties_array[ 'dimension' ], $match)) {
                        $hmeasurement = (int)$match[ 1 ][ 0 ];
                    }

                    if (preg_match_all('/H ([\d]+)/', $properties_array[ 'dimension' ], $match)) {
                        $hmeasurement = (int)$match[ 1 ][ 0 ];
                    }
                }

                if (strpos($properties_array[ 'dimension' ], 'Depth') !== false || strpos($properties_array[ 'dimension' ], 'D') !== false) {
                    if (preg_match_all('/Depth ([\d]+)/', $properties_array[ 'dimension' ], $match)) {
                        $dmeasurement = (int)$match[ 1 ][ 0 ];
                    }

                    if (preg_match_all('/D ([\d]+)/', $properties_array[ 'dimension' ], $match)) {
                        $dmeasurement = (int)$match[ 1 ][ 0 ];
                    }
                }

                if (strpos($properties_array[ 'dimension' ], 'x') !== false) {
                    $formatted = str_replace('cm', '', $properties_array[ 'dimension' ]);
                    $formatted = str_replace(' ', '', $formatted);
                    $exploded = explode('x', $formatted);

                    if (array_key_exists('0', $exploded)) {
                        $lmeasurement = (int)$exploded[ 0 ];
                        $measurement_size_type = 'measurement';
                    }

                    if (array_key_exists('1', $exploded)) {
                        $hmeasurement = (int)$exploded[ 1 ];
                    }

                    if (array_key_exists('2', $exploded)) {
                        $dmeasurement = (int)$exploded[ 2 ];
                    }
                }
            }
        }

        if (array_key_exists('category', $properties_array)) {
            $categories = Category::all();
            $category_id = 1;

            foreach ($properties_array[ 'category' ] as $key => $cat) {
                $up_cat = strtoupper($cat);

                if ($up_cat == 'WOMAN') {
                    $up_cat = 'WOMEN';
                }

                if ($key == 0 && $up_cat == 'WOMEN') {
                    $women_children = Category::where('title', 'WOMEN')->first()->childs;
                }

                if (isset($women_children)) {
                    foreach ($women_children as $children) {
                        if (strtoupper($children->title) == $up_cat) {
                            $category_id = $children->id;
                        }

                        foreach ($children->childs as $child) {
                            if (strtoupper($child->title) == $up_cat) {
                                $category_id = $child->id;
                            }
                        }
                    }
                } else {
                    foreach ($categories as $category) {
                        if (strtoupper($category->title) == $up_cat) {
                            $category_id = $category->id;
                        }
                    }
                }

            }

            $category = $category_id;
        }

        if (array_key_exists('country', $properties_array)) {
            $made_in = $properties_array[ 'country' ];
        }

        return [
            'composition' => isset($composition) ? $composition : '',
            'color' => isset($color) ? $color : '',
            'size' => isset($size) ? $size : '',
            'lmeasurement' => isset($lmeasurement) ? $lmeasurement : '',
            'hmeasurement' => isset($hmeasurement) ? $hmeasurement : '',
            'dmeasurement' => isset($dmeasurement) ? $dmeasurement : '',
            'measurement_size_type' => isset($measurement_size_type) ? $measurement_size_type : '',
            'made_in' => isset($made_in) ? $made_in : '',
            'category' => isset($category) ? $category : 1,
        ];
    }
}
