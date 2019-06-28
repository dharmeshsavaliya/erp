<?php

namespace App\Http\Controllers;

use App\Category;
use App\CropAmends;
use App\Image;
use App\ListingHistory;
use App\Product;
use App\Setting;
use App\Sizes;
use App\Stage;
use App\Brand;
use App\UserProductFeedback;
use Carbon\Carbon;
use File;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Plank\Mediable\Media;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;


class ProductCropperController extends Controller
{
	//
	public function __construct() {

		$this->middleware('permission:imagecropper-list',['only' => ['sList','index']]);
		$this->middleware('permission:imagecropper-create', ['only' => ['create','store']]);
		$this->middleware('permission:imagecropper-edit', ['only' => ['edit','update']]);


		$this->middleware('permission:imagecropper-delete', ['only' => ['destroy']]);
	}

	public function index(Stage $stage){

		$products = Product::latest()
												->where('stock', '>=', 1)
		                   ->where('stage','>=',$stage->get('Supervisor'))
		                   ->whereNull('dnf')
		                   ->withMedia(config('constants.media_tags'))
											 ->select(['id', 'sku', 'size', 'price_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])
		                   ->paginate(Setting::get('pagination'));

		$roletype = 'ImageCropper';

		$category_selection = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple'])
		                                        ->selected(1)
		                                        ->renderAsDropdown();

		return view('partials.grid',compact('products','roletype', 'category_selection'))
			->with('i', (request()->input('page', 1) - 1) * 10);
	}

	public function edit(Sizes $sizes,Product $productimagecropper)
	{

		if( $productimagecropper->isUploaded == 1)
			return redirect(route('products.show',$productimagecropper->id));

		$data = [];

		$data['dnf'] = $productimagecropper->dnf;
		$data['id'] = $productimagecropper->id;
		$data['name'] = $productimagecropper->name;
		$data['short_description'] =$productimagecropper->short_description;
		$data['sku'] = $productimagecropper->sku;
//		$data['supplier_link'] = $productimagecropper->supplier_link;
		$data['description_link'] = $productimagecropper->description_link;
		$data['location'] = $productimagecropper->location;
		$data['product_link'] = $productimagecropper->product_link;

		$data['measurement_size_type'] = $productimagecropper->measurement_size_type;
		$data['lmeasurement'] = $productimagecropper->lmeasurement;
		$data['hmeasurement'] = $productimagecropper->hmeasurement;
		$data['dmeasurement'] = $productimagecropper->dmeasurement;

		$data['size_value'] = $productimagecropper->size_value;
		$data['sizes_array'] = $sizes->all();

		$data['size'] = $productimagecropper->size;


		$data['composition'] = $productimagecropper->composition;
		$data['made_in'] = $productimagecropper->made_in;
		$data['brand'] = $productimagecropper->brand;
		$data['color'] = $productimagecropper->color;
		$data['price'] = $productimagecropper->price;

		$data['isApproved'] = $productimagecropper->isApproved;
		$data['isUploaded'] = $productimagecropper->isUploaded;
		$data['isFinal'] = $productimagecropper->isFinal;
		$data['rejected_note'] = $productimagecropper->rejected_note;

		$data['images']  = $productimagecropper->getMedia(config('constants.media_tags'));

		$data['category'] = Category::attr(['name' => 'category','class' => 'form-control','disabled' => 'disabled'])
		                            ->selected($productimagecropper->category)
		                            ->renderAsDropdown();

		return view('imagecropper.edit',$data);
	}

	public function update(Request $request,Guard $auth, Product $productimagecropper,Stage $stage)
	{

//		$productattribute->dnf = $request->input('dnf');
		$productimagecropper->stage = $stage->get('ImageCropper');

		/*$productimagecropper->measurement_size_type = $request->input('measurement_size_type');
		$productimagecropper->lmeasurement = $request->input('lmeasurement');
		$productimagecropper->hmeasurement = $request->input('hmeasurement');
		$productimagecropper->dmeasurement = $request->input('dmeasurement');
		$productimagecropper->size = $request->input('size');
		$productimagecropper->color = $request->input('color');

		$productimagecropper->size_value = $request->input('size_value');

		if($request->input('measurement_size_type') == 'size')
			$validations['size_value'] = 'required_without:dnf';
		elseif ( $request->input('measurement_size_type') == 'measurement' ){
			$validations['lmeasurement'] = 'required_without_all:hmeasurement,dmeasurement,dnf|numeric';
			$validations['hmeasurement'] = 'required_without_all:lmeasurement,dmeasurement,dnf|numeric';
			$validations['dmeasurement'] = 'required_without_all:lmeasurement,hmeasurement,dnf|numeric';
		}*/


		$validations  = [];

		//:-( ahead
		$check_image = 0;
		$images = $productimagecropper->getMedia(config('constants.media_tags'));
		$images_no = sizeof($images);

		for( $i = 0 ; $i < 5 ; $i++) {

			if ( $request->input( 'oldImage'.$i ) != 0 ) {
				$validations['image.'.$i] = 'mimes:jpeg,bmp,png,jpg';

				if( empty($request->file('image.'.$i ) ) ){
					$check_image++;
				}
			}
		}

		$messages = [];
		if($check_image == $images_no) {
			$validations['image'] = 'required';
			$messages['image.required'] ='Atleast on image is required. Last image can not be removed';
		}
		//:-( over

		$this->validate( $request, $validations );

		self::replaceImages($request,$productimagecropper);

		$productimagecropper->last_imagecropper = Auth::id();
		$productimagecropper->save();

		NotificaitonContoller::store( 'has searched', ['Listers'], $productimagecropper->id );
		ActivityConroller::create($productimagecropper->id,'imagecropper','create');

		return redirect()->route( 'productimagecropper.index' )
		                 ->with( 'success', 'ImageCropper updated successfully.' );
	}

	public function replaceImages($request,$productattribute){

		$delete_array = [];
		for( $i = 0 ; $i < 5 ; $i++) {

			if ( $request->input( 'oldImage' . $i ) != 0 ) {
				$delete_array[] = $request->input( 'oldImage' . $i );
			}

			if( !empty($request->file('image.'.$i ) ) ){

				$media = MediaUploader::fromSource($request->file('image.'.$i ))->upload();
				$productattribute->attachMedia($media,config('constants.media_tags'));
			}
		}

		$results = Media::whereIn('id' , $delete_array )->get();
		$results->each(function($media) {
			Image::trashImage($media->basename);
			$media->delete();
		});
	}

	public static function rejectedProductCountByUser(){

		return Product::where('last_imagecropper', Auth::id() )
		              ->where('isApproved',-1)
		              ->count();
	}

	public function getListOfImagesToBeVerified(Stage $stage) {
	    $products = Product::where('is_image_processed', 1)
            ->where('is_crop_rejected', 0)
            ->where('is_crop_approved', 0)
            ->where('is_crop_being_verified', 0)
            ->whereDoesntHave('amends')
            ->paginate(24);

	    $totalApproved = 0;
	    $totalRejected = 0;
	    $totalSequenced = 0;

	    if (Auth::user()->hasRole('Crop Approval')) {
	        $stats = UserProductFeedback::where('user_id')->whereIn('action', [
	            'CROP_APPROVAL_REJECTED',
                'CROP_SEQUENCED_REJECTED'
            ])->get();
	        $totalApproved = Product::where('crop_approved_by', Auth::id())->count();
	        $totalRejected = Product::where('crop_rejected_by', Auth::id())->count();
	        $totalSequenced = Product::where('crop_rejected_by', Auth::id())->count();
        } else {
            $stats = DB::table('products')->selectRaw('SUM(is_image_processed) as cropped, COUNT(*) AS total, SUM(is_crop_approved) as approved, SUM(is_crop_rejected) AS rejected')->where('is_scraped', 1)->where('is_without_image', 0)->first();
        }


//
//        $secondProduct = Product::where('is_image_processed', 1)
//            ->where('is_crop_rejected', 0)
//            ->where('is_crop_approved', 0)
//            ->whereDoesntHave('amends')
//            ->first();

//        return redirect()->action('ProductCropperController@showImageToBeVerified', $secondProduct->id);

	    return view('products.crop_list', compact('products', 'stats', 'totalRejected', 'totalSequenced', 'totalApproved'));
    }

    public function showImageToBeVerified($id, Stage $stage) {
        $product = Product::find($id);
        $product->is_crop_being_verified = 1;
        $product->save();

        $secondProduct = Product::where('is_image_processed', 1)
            ->where('id', '!=', $id)
            ->where('is_crop_rejected', 0)
            ->where('is_crop_approved', 0)
            ->whereDoesntHave('amends')
            ->where('is_crop_being_verified', 0)
            ->first();

        $category = $product->category;
        $img = $this->getCategoryForCropping($category);

	    return view('products.crop', compact('product', 'secondProduct', 'img', 'category'));
    }

    public function getApprovedImages() {
        $products = Product::where('is_image_processed', 1)
            ->where('is_crop_approved', 1)
            ->paginate(24);

//        $stats = DB::table('products')->selectRaw('SUM(is_image_processed) as cropped, COUNT(*) AS total, SUM(is_crop_approved) as approved, SUM(is_crop_rejected) AS rejected')->where('is_scraped', 1)->where('is_without_image', 0)->first();


//
//        $secondProduct = Product::where('is_image_processed', 1)
//            ->where('is_crop_rejected', 0)
//            ->where('is_crop_approved', 0)
//            ->whereDoesntHave('amends')
//            ->first();

//        return redirect()->action('ProductCropperController@showImageToBeVerified', $secondProduct->id);

        return view('products.approved_crop_list', compact('products'));
    }

    private function getCategoryForCropping($categoryId) {
	    $imagesForGrid = [
	        'Shoes' => 'shoes_grid.png',
            'Backpacks' => 'Backpack.png',
            'Beach' => 'Backpack.png',
            'Travel' => 'Backpack.png',
            'Belt' => 'belt.png',
            'Belts' => 'belt.png',
            'Clothing' => 'Clothing.png',
            'Skirts' => 'Clothing.png',
            'Pullovers' => 'Clothing.png',
            'Shirt' => 'Clothing.png',
            'Dresses' => 'Clothing.png',
            'Kaftan' => 'Clothing.png',
            'Tops' => 'Clothing.png',
            'Jumpers & Jump Suits' => 'Clothing.png',
            'Pant' => 'Clothing.png',
            'Pants' => 'Clothing.png',
            'Dress' => 'Clothing.png',
            'Sweatshirt/s & Hoodies' => 'Clothing.png',
            'Shirts' => 'Clothing.png',
            'Denim' => 'Clothing.png',
            'Sweat Pants' => 'Clothing.png',
            'T-Shirts' => 'Clothing.png',
            'Sweater' => 'Clothing.png',
            'Sweaters' => 'Clothing.png',
            'Clothings' => 'Clothing.png',
            'Coats & Jackets' => 'Clothing.png',
            'Tie & Bow Ties' => 'Bow.png',
            'Clutches' => 'Clutch.png',
            'Document Holder' => 'Clutch.png',
            'Clutch Bags' => 'Clutch.png',
            'Crossbody Bag' => 'Clutch.png',
            'Wristlets' => 'Clutch.png',
            'Crossbody Bags' => 'Clutch.png',
            'Make-Up Bags' => 'Clutch.png',
            'Belt Bag' => 'Clutch.png',
            'Belt Bags' => 'Clutch.png',
            'Hair Accessories' => 'Hair_accessories.png',
            'Beanies & Caps' => 'Hair_accessories.png',
            'Handbags' => 'Handbag.png',
            'Duffle Bags' => 'Handbag.png',
            'Laptop Bag' => 'Handbag.png',
            'Bucket Bags' => 'Handbag.png',
            'Laptop Bags' => 'Handbag.png',
            'Jewelry' => 'Jewellery.png',
            'Shoulder Bags' => 'Shoulder_bag.png',
            'Sunglasses & Frames' => 'Sunglasses.png',
            'Gloves' => 'Sunglasses.png', //need to be made for gloves
            'Tote Bags' => 'Tote.png',
            'Wallet' => 'Wallet.png',
            'Wallets & Cardholder' => 'Wallet.png',
            'Wallets & Cardholders' => 'Wallet.png',
            'Key Pouches' => 'Wallet.png',
            'Key Pouch' => 'Wallet.png',
            'Coin Case / Purse' => 'Wallet.png',
            'Shawls And Scarves' => 'Shawl.png',
            'Shawls And Scarve' => 'Shawl.png',
            'Scarves & Wraps' => 'Shawl.png',
            'Key Rings & Chains' => 'Keychains.png',
            'Key Rings & Chain' => 'Keychains.png',
        ];

	    $category = Category::find($categoryId);
	    $catName = $category->title;

        if (array_key_exists($catName, $imagesForGrid)) {
            return $imagesForGrid[$catName];
        }

	    if ($category->parent_id > 1) {
	        $category = Category::find($category->parent_id);
	        return $imagesForGrid[trim($category->title)] ?? '';
        }

	    return '';

    }

    public function ammendCrop($id, Request $request, Stage $stage) {
	    $product = Product::findOrFail($id);

	    $this->validate($request, [
	        'size' => 'required'
        ]);

	    $sizes = $request->get('size');
	    $padding = $request->get('padding');
	    $urls = $request->get('url');
	    $mediaIds = $request->get('mediaIds');


	    foreach ($sizes as $key=>$size) {
	        if ($size != 'ok') {
	            $rec = new CropAmends();
	            $rec->file_url = $urls[$key];
	            $rec->settings = ['size' => $size, 'padding' => $padding[$key] ?? 96, 'media_id' => $mediaIds[$key]];
	            $rec->product_id = $id;
	            $rec->save();
            }
        }

        $secondProduct = Product::where('is_image_processed', 1)
            ->where('stage', '=', $stage->get('ImageCropper'))
            ->where('id', '!=', $id)
            ->where('is_crop_rejected', 0)
            ->where('is_crop_approved', 0)
            ->whereDoesntHave('amends')
            ->first();

//        $this->deleteUncroppedImages($product);

        return redirect()->action('ProductCropperController@showImageToBeVerified', $secondProduct->id)->with('message', 'Cropping approved successfully!');

    }

    public function giveAmends() {
	    $amend = CropAmends::where('status', 1)->first();

	    return response()->json($amend);
    }

    public function saveAmends(Request $request) {
	    $this->validate($request,[
	        'file' => 'required',
	        'product_id' => 'required',
	        'media_id' => 'required',
            'amend_id' => 'required'
        ]);

        $product = Product::findOrFail($request->get('product_id'));
        $product->is_crop_being_verified = 0;
        Media::where('id', $request->get('media_id'))->delete();

        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $media = MediaUploader::fromSource($image)->upload();
            $product->attachMedia($media, 'gallery');
        }

        $amend = CropAmends::findOrFail($request->get('amend_id'));
        $amend->delete();

        return response()->json([
            'status' => 'success'
        ]);


    }

    public function approveCrop($id,Stage $stage) {
	    $product = Product::findOrFail($id);
	    $product->is_crop_approved = 1;
	    $product->crop_approved_by = Auth::user()->id;
	    $product->crop_approved_at = Carbon::now()->toDateTimeString();
	    $product->save();

        $e = new ListingHistory();
        $e->user_id = Auth::user()->id;
        $e->product_id = $product->id;
        $e->content = ['action' => 'CROP_APPROVAL', 'page' => 'Approved Listing Page'];
        $e->action = 'CROP_APPROVAL';
        $e->save();

        $secondProduct = Product::where('is_image_processed', 1)
            ->where('stage', '=', $stage->get('ImageCropper'))
            ->where('id', '!=', $id)
            ->where('is_crop_rejected', 0)
            ->where('is_crop_approved', 0)
            ->where('is_crop_being_verified', 0)
            ->whereDoesntHave('amends')
            ->first();

        $this->deleteUncroppedImages($product);

        return redirect()->action('ProductCropperController@showImageToBeVerified', $secondProduct->id)->with('message', 'Cropping approved successfully!');
    }

    private function deleteUncroppedImages($product) {
        if ($product->hasMedia(config('constants.media_tags'))) {
            $tc = count($product->getMedia(config('constants.media_tags')));
            if ($tc < 6) {
                return;
            }
            foreach ($product->getMedia(config('constants.media_tags')) as $key=>$image) {
                if (stripos(strtoupper($image->filename), 'CROPPED') === false) {
                    $image_path = $image->getAbsolutePath();

                    if (File::exists($image_path)) {
                        try {
                            File::delete($image_path);
                        } catch (\Exception $exception) {

                        }
                    }

                    $image->delete();
                }
            }

            $product->is_image_processed = 1;
            $product->save();

        }
    }

    private function deleteCroppedImages($product) {
        if ($product->hasMedia(config('constants.media_tags'))) {
            foreach ($product->getMedia(config('constants.media_tags')) as $key=>$image) {
                if (stripos(strtoupper($image->filename), 'CROPPED') !== false) {
                    $image_path = $image->getAbsolutePath();

                    if (File::exists($image_path)) {
                        try {
                            File::delete($image_path);
                        } catch (\Exception $exception) {

                        }
                    }

                    $image->delete();
                }
            }

            $product->is_image_processed = 1;
            $product->save();

        }
    }

    public function rejectCrop($id,Stage $stage, Request $request) {
        $product = Product::findOrFail($id);
        $product->is_crop_rejected = 1;
        $product->crop_remark = $request->get('remark');
        $product->crop_rejected_by = Auth::user()->id;
        $product->is_approved = 0;
        $product->is_crop_approved = 0;
        $product->is_crop_ordered = 0;
        $product->is_crop_being_verified = 0;
        $product->crop_rejected_at = Carbon::now()->toDateTimeString();
        $product->save();

        $e = new ListingHistory();
        $e->user_id = Auth::user()->id;
        $e->product_id = $product->id;
        $e->content = ['action' => 'PRODUCT_LISTING', 'page' => 'Approved Listing Page'];
        $e->action = 'CROP_REJECTED';
        $e->save();

        if ($request->get('senior') && $product) {
            $s = new UserProductFeedback();
            $s->user_id = $product->crop_approved_by;
            $s->senior_user_id = Auth::user()->id;
            $s->action = 'CROP_APPROVAL_REJECTED';
            $s->content = ['action' => 'CROP_APPROVAL_REJECTED', 'previous_action' => 'CROP_APPROVAL', 'current_action' => 'CROP_REJECTED', 'message' => 'Your cropping approval has been rejected.'];
            $s->message = 'Your cropping approval has been rejected. The reason was: '. $request->get('remark');
            $s->product_id = $product->id;
            $s->save();
        }

        if ($request->isXmlHttpRequest()) {
            return response()->json([
                'status' => 'success'
            ]);
        }

        $secondProduct = Product::where('is_image_processed', 1)
            ->where('stage', '=', $stage->get('ImageCropper'))
            ->where('id', '!=', $id)
            ->where('is_crop_rejected', 0)
            ->where('is_crop_approved', 0)
            ->where('is_crop_being_verified', 0)
            ->first();

        return redirect()->action('ProductCropperController@showImageToBeVerified', $secondProduct->id)->with('message', 'Cropping rejected!');
    }

    public function crop_issue_page(Request $request) {

    }

    public function showRejectedCrops(Request $request)
    {
        $products = Product::where('is_crop_rejected', 1);
        $reason = '';
        $supplier = [];
        $selected_categories = [];

        if ($request->get('reason') !== '') {
            $reason = $request->get('reason');
            $products = $products->where('crop_remark' , 'LIKE', "%$reason%");
            $products = $products->where('id', 'LIKE', "%$reason%");
            $products = $products->where('sku', 'LIKE', "%$reason%");
        }

        $suppliers = DB::select('
				SELECT id, supplier
				FROM suppliers

				INNER JOIN (
					SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
					) as product_suppliers
				ON suppliers.id = product_suppliers.supplier_id
		');

        if ($request->supplier[0] != null) {

            $supplier = $request->get('supplier');
            $products = $products->whereIn('id', DB::table('product_suppliers')->whereIn('supplier_id', $supplier)->pluck('product_id'));
        }

        if ($request->category[0] != null && $request->category[0] != 1) {
            $category_children = [];
            foreach ($request->category as $category) {
                $is_parent = Category::isParent($category);


                if ($is_parent) {
                    $childs = Category::find($category)->childs()->get();

                    foreach ($childs as $child) {
                        $is_parent = Category::isParent($child->id);

                        if ($is_parent) {
                            $children = Category::find($child->id)->childs()->get();

                            foreach ($children as $chili) {
                                array_push($category_children, $chili->id);
                            }
                        } else {
                            array_push($category_children, $child->id);
                        }
                    }
                } else {
                    array_push($category_children, $category);
                }

            }
            $products = $products->whereIn('category', $category_children);
            $selected_categories = [$request->get('category')[0]];
        }

        $category_array = Category::renderAsArray();


        $products = $products->orderBy('updated_at', 'DESC')->paginate(24);

        return view('products.rejected_crop_list', compact('products', 'suppliers', 'supplier', 'reason', 'selected_categories', 'category_array'));
    }

    public function showRejectedImageToBeverified($id) {
	    $product = Product::find($id);
	    $secondProduct = Product::where('id', '!=', $id)->where('is_crop_rejected', 1)->first();

	    return view('products.rejected_crop', compact('product', 'secondProduct'));
    }

    public function downloadImagesForProducts($id, $type) {
	    $product = Product::findOrFail($id);

	    $medias = $product->getMedia('gallery');
	    $zip_file = md5(time()) . '.zip';
	    $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE);
        foreach ($medias as $key => $media) {
            $fileName = $media->getAbsolutePath();
            if ($type === 'cropped' && stripos(strtoupper($media->filename), 'CROPPED') !== false) {
                $zip->addFile($fileName, $media->filename . '.' . $media->extension);
            }
	        if ($type === 'original' && stripos(strtoupper($media->filename), 'CROPPED') === false ) {
	            $zip->addFile($fileName, $media->filename . '.' . $media->extension);
            }
        }

	    $zip->close();

	    return response()->download($zip_file);

    }

    public function approveRejectedCropped($id, Request $request) {
	    $this->validate($request, [
	        'images' => 'required'
        ]);
	    $product = Product::find($id);

	    $files = $request->allFiles();

	    $this->deleteCroppedImages($product);

	    foreach ($files['images'] as $file) {
	        $media = MediaUploader::fromSource($file)->useFilename('CROPPED_' . time() . '_' . rand(555,455545))->upload();
	        $product->attachMedia($media, 'gallery');
        }

        $product->is_crop_rejected = 0;
        $product->is_crop_approved = 0;
        $product->reject_approved_by = Auth::user()->id;
        $product->save();

        $l = new ListingHistory();
        $l->action = 'CROP_REJECTED_APPROVAL';
        $l->content = ['action' => 'CROP_REJECTED_APPROVAL', 'message' => 'The rejected cropped image is back for re verification!'];
        $l->user_id = Auth::user()->id;
        $l->product_id = $product->id;
        $l->save();

        $secondProduct = Product::where('id', '!=', $id)->where('is_crop_rejected', 1)->first();

        return redirect()->action('ProductCropperController@showRejectedImageToBeverified', $secondProduct->id)->with('message', 'Rejected image approved and has been moved to approval grid.');

    }

    public function updateCroppedImages(Request $request) {
	    dd($request->all());

    }

    public function giveImagesToBeAmended() {
	    $image = CropAmends::where('status', 1)->first();
	    return response()->json($image);
    }

    public function showCropOrderRejectedList() {
	    $products = Product::where('is_order_rejected', 1)->orderBy('updated_at', 'DESC')->paginate(24);

    }

    public function showCropVerifiedForOrdering() {
	    $product = Product::where('is_crop_approved', 1)->where('is_crop_ordered', 0)->orderBy('is_order_rejected', 'DESC')->first();
	    $total = Product::where('is_crop_approved', 1)->where('is_crop_ordered', 0)->count();

	    return view('products.sequence', compact('product', 'total'));

    }

    public function skipSequence($id, Request $request) {
	    $product = Product::findOrFail($id);
	    $product->is_crop_approved = 0;
	    $product->is_crop_ordered = 0;
	    $product->save();

	    $l = new ListingHistory();
	    $l->action = 'SKIP_SEQUENCE';
	    $l->product_id = $product->id;
	    $l->user_id = Auth::user()->id;
	    $l->content = ['action' => 'SKIP_SEQUENCE', 'page' => 'Sequence Approver'];
	    $l->save();





	    if ($request->isXmlHttpRequest()) {
	        return response()->json([
	            'status' => 'success'
            ]);
        }

	    return redirect()->action('ProductCropperController@showCropVerifiedForOrdering');

    }

    public function rejectSequence($id, Request $request)
    {
        $product = Product::findOrFail($id);
        $product->is_crop_ordered = 0;
        $product->is_approved = 0;
        $product->save();

        $l = new ListingHistory();
        $l->action = 'REJECT_SEQUENCE';
        $l->product_id = $product->id;
        $l->user_id = Auth::user()->id;
        $l->content = ['action' => 'REJECT_SEQUENCE', 'page' => 'Approved Listing'];
        $l->save();

        if ($request->get('senior') && $product) {
            $s = new UserProductFeedback();
            $s->user_id = $product->crop_ordered_by;
            $s->senior_user_id = Auth::user()->id;
            $s->action = 'CROP_SEQUENCED_REJECTED';
            $s->content = ['action' => 'CROP_SEQUENCED_REJECTED', 'previous_action' => 'CROP_SEQUENCED', 'current_action' => 'CROP_SEQUENCED_REJECTED', 'message' => 'Your sequencing has been rejected.'];
            $s->message = 'Your crop sequence was not proper. Please check for this one';
            $s->product_id = $product->id;
            $s->save();
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function saveSequence($id, Request $request) {

	    $product  = Product::findOrFail($id);

	    $medias = $request->get('images');
	    foreach ($medias as $mediaId=>$order) {
	        if ($order!==null) {
                DB::table('mediables')->where('media_id', $mediaId)->where('mediable_type', 'App\Product')->update([
                    'order' => $order
                ]);
            } else {
	            DB::table('mediables')->where('media_id', $mediaId)->where('mediable_type', 'App\Product')->delete();
	            DB::table('media')->where('id', $mediaId)->delete();
            }
        }

	    $product->is_crop_ordered = 1;
	    $product->crop_ordered_by = Auth::user()->id;
	    $product->crop_ordered_at = Carbon::now()->toDateTimeString();
	    $product->save();

	    $l = new ListingHistory();
	    $l->action = 'CROP_SEQUENCED';
	    $l->user_id = Auth::user()->id;
	    $l->product_id = $product->id;
	    $l->content = ['action' => 'CROP_SEQUENCED', 'page' => 'Crop Sequencer'];
	    $l->save();

	    return redirect()->action('ProductCropperController@showCropVerifiedForOrdering')->with('message', 'Previous image ordered successfully!');
	}
}
