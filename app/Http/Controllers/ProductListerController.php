<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Product;
use App\ProductReference;
use App\Setting;
use App\Stage;
use App\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductListerController extends Controller
{
	public function __construct() {

		$this->middleware('permission:lister-list',['only' => ['index']]);
		$this->middleware('permission:lister-edit',['only' => ['edit','isUploaded']]);
	}


	public function index(Stage $stage){

		$products = Product::latest()
		                   ->where('stage','>=',$stage->get('ImageCropper'))
		                   ->whereNull('dnf')
											 ->select(['id', 'sku', 'size', 'price_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])
		                   ->paginate(Setting::get('pagination'));

		$roletype = 'Lister';

		$category_selection = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple'])
		                                        ->selected(1)
		                                        ->renderAsDropdown();

		return view('partials.grid',compact('products','roletype', 'category_selection'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function edit(Product $productlister){

		return redirect( route('products.show',$productlister->id) );
	}

	public function isUploaded(Product $product,Stage $stage){

		if( $product->isUploaded  == 1)
			return back()->with('error','Product already upload.');

		$result = $this->magentoSoapApiUpload($product);

		if($result){

			$product->isUploaded = 1;
			$product->stage = $stage->get('Lister');
			$product->is_uploaded_date = Carbon::now();
			$product->save();

			NotificaitonContoller::store('has Uploaded',['Approvers'],$product->id);
			ActivityConroller::create($product->id,'lister','create');

			return back()->with('success','Product has been Uploaded');

		}

		return back()->with('error','Error Occured while uploading');

	}

	public function magentoSoapApiUpload($product){

		$options = array(
			'trace' => true,
			'connection_timeout' => 120,
			'wsdl_cache' => WSDL_CACHE_NONE,
		);
		$proxy = new \SoapClient(config('magentoapi.url'), $options);
		$sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

//		$attributeSets = $proxy->catalogProductAttributeSetList($sessionId);
//		$attributeSet = current($attributeSets);

		$sku = $product->sku . $product->color;
		$categories = CategoryController::getCategoryTreeMagentoIds($product->category);

		$brand= $product->brands()->get();
		array_push($categories,$brand[0]->magento_id);

		if ($product->references) {
			$product->references()->delete();
		}

		$reference = new ProductReference;
		$reference->product_id = $product->id;
		$reference->sku = $product->sku;
		$reference->color = $product->color;
		$reference->save();

		if(!empty($product->size)) {

			$associated_skus = [];
			$sizes_array = explode(',', $product->size);

			foreach ( $sizes_array as $size ) {
				$reference = new ProductReference;
				$reference->product_id = $product->id;
				$reference->sku = $product->sku;
				$reference->color = $product->color;
				$reference->size = $size;
				$reference->save();

				$productData = array(
					'categories'            => $categories,
					'name'                  => $product->name,
					'description'           => '<p></p>',
					'short_description'     => $product->short_description,
					'website_ids'           => array(1),
					// Id or code of website
					'status'                => 2,
					// 1 = Enabled, 2 = Disabled
					'visibility'            => 1,
					// 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
					'tax_class_id'          => 2,
					// Default VAT
					'weight'                => 0,
					'stock_data' => array(
						'use_config_manage_stock' => 1,
						'manage_stock' => 1,
					),
					'price'                 => $product->price_inr,
					// Same price than configurable product, no price change
					'special_price'         => $product->price_special,
					'additional_attributes' => array(
						'single_data' => array(
							array( 'key' => 'composition', 'value' => $product->composition, ),
							array( 'key' => 'color', 'value' => $product->color, ),
							array( 'key' => 'sizes', 'value' => $size, ),
							array( 'key' => 'country_of_manufacture', 'value' => $product->made_in, ),
							array( 'key' => 'brands', 'value' => BrandController::getBrandName( $product->brand ), ),
						),
					),
				);
				// Creation of product simple
				try {
					$result            = $proxy->catalogProductCreate( $sessionId, 'simple', 14, $sku . '-' . $size, $productData );
					$associated_skus[] = $sku . '-' . $size;
				} catch (\Exception $e) {
					if ($e->getMessage() == 'The value of attribute "SKU" must be unique') {
						$product->isUploaded = 1;
						$product->save();
					}
				}
			}

			/**
			 * Configurable product
			 */
			$productData = array(
				'categories'              => $categories,
				'name'                    => $product->name,
				'description'             => '<p></p>',
				'short_description'       => $product->short_description,
				'website_ids'             => array(1),
				// Id or code of website
				'status'                  => 2,
				// 1 = Enabled, 2 = Disabled
				'visibility'              => 4,
				// 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
				'tax_class_id'            => 2,
				// Default VAT
				'weight'                  => 0,
				'stock_data' => array(
					'use_config_manage_stock' => 1,
					'manage_stock' => 1,
				),
				'price'                   => $product->price_inr,
				// Same price than configurable product, no price change
				'special_price'           => $product->price_special,
				'associated_skus'         => $associated_skus,
				// Simple products to associate
				'configurable_attributes' => array( 155 ),
				'additional_attributes'   => array(
					'single_data' => array(
						array( 'key' => 'composition', 'value' => $product->composition, ),
						array( 'key' => 'color', 'value' => $product->color, ),
						array( 'key' => 'country_of_manufacture', 'value' => $product->made_in, ),
						array( 'key' => 'brands', 'value' => BrandController::getBrandName( $product->brand ), ),
					),
				),
			);

			// Creation of configurable product
			try {
				$result = $proxy->catalogProductCreate( $sessionId, 'configurable', 14, $sku, $productData );
			} catch (\Exception $e) {
				if ($e->getMessage() == 'The value of attribute "SKU" must be unique') {
					$product->isUploaded = 1;
					$product->save();
				}
			}
		}
		else{

			$measurement = 'L-'.$product->lmeasurement.',H-'.$product->hmeasurement.',D-'.$product->dmeasurement;

			$productData = array(
				'categories'            => $categories,
				'name'                  => $product->name,
				'description'           => '<p></p>',
				'short_description'     => $product->short_description,
				'website_ids'           => array(1),
				// Id or code of website
				'status'                => 2,
				// 1 = Enabled, 2 = Disabled
				'visibility'            => 4,
				// 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
				'tax_class_id'          => 2,
				// Default VAT
				'weight'                => 0,
				'stock_data' => array(
					'use_config_manage_stock' => 1,
					'manage_stock' => 1,
				),
				'price'                 => $product->price_inr,
				// Same price than configurable product, no price change
				'special_price'         => $product->price_special,
				'additional_attributes' => array(
					'single_data' => array(
						array( 'key' => 'composition', 'value' => $product->composition, ),
						array( 'key' => 'color', 'value' => $product->color, ),
						array( 'key' => 'measurement', 'value' => $measurement, ),
						array( 'key' => 'country_of_manufacture', 'value' => $product->made_in, ),
						array( 'key' => 'brands', 'value' => BrandController::getBrandName( $product->brand ), ),
					),
				),
			);
			// Creation of product simple
			try {
				$result  = $proxy->catalogProductCreate( $sessionId, 'simple', 4, $sku, $productData );
			} catch (\Exception $e) {
				if ($e->getMessage() == 'The value of attribute "SKU" must be unique') {
					$product->isUploaded = 1;
					$product->save();
				}
			}
		}

		if (isset($result)) {
			$images = $product->getMedia(config('constants.media_tags'));

			$i = 0;
			$productId = $result;

			foreach ($images as $image){

				$image->getUrl();

				$file = array(
					'name' => $image->getBasenameAttribute(),
					'content' => base64_encode(file_get_contents($image->getAbsolutePath())),
					'mime' => mime_content_type($image->getAbsolutePath())
				);

				$types = $i ? array('') : array('size_guide','image','small_image','thumbnail');
				$types = $i == 1 ? array('hover_image') : $types;

				$result = $proxy->catalogProductAttributeMediaCreate(
					$sessionId,
					$productId,
					array('file' => $file, 'label' => $image->getBasenameAttribute() , 'position' => ++$i , 'types' => $types, 'exclude' => 0)
				);
			}
		} else {
			$result = false;
		}

		return $result;
	}

}
