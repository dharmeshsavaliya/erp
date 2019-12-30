<?php


namespace App\Http\Controllers;


use App\Category;
use App\ColorReference;
use App\CroppedImageReference;
use App\Jobs\PushToMagento;
use App\ListingHistory;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\ScrapedProducts;
use App\Sale;
use App\Setting;
use App\Sizes;
use App\Sop;
use App\Stage;
use App\Brand;
use App\User;
use App\Supplier;
use App\Stock;
use App\Colors;
use App\ReadOnly\LocationList;
use App\UserProduct;
use App\UserProductFeedback;
use App\Helpers\QueryHelper;
use App\Helpers\StatusHelper;
use Cache;
use Auth;
use Carbon\Carbon;
use Chumper\Zipper\Zipper;
use Dompdf\Exception;
use FacebookAds\Object\ProductFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use App\SimplyDutyCategory;
use App\HsCodeGroup;
use App\HsCodeGroupsCategoriesComposition;
use App\HsCode;
use App\HsCodeSetting;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
//		$this->middleware( 'permission:product-list', [ 'only' => [ 'show' ]]);
//		$this->middleware('permission:product-lister', ['only' => ['listing']]);
        $this->middleware('permission:product-lister', ['only' => ['listing']]);
//		$this->middleware('permission:product-create', ['only' => ['create','store']]);
//		$this->middleware('permission:product-edit', ['only' => ['edit','update']]);

//		$this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->archived == 'true') {
            $products = Product::onlyTrashed()->latest()->select(['id', 'sku', 'name']);
        } else {
            $products = Product::latest()->select(['id', 'sku', 'name']);
        }
        $term = $request->term;
        $archived = $request->archived;

        if (!empty($term)) {
            $products = $products->where(function ($query) use ($term) {
                return $query
                    ->orWhere('id', 'like', '%' . $term . '%')
                    ->orWhere('name', 'like', '%' . $term . '%')
                    ->orWhere('sku', 'like', '%' . $term . '%');
            });
        }

        $products = $products->paginate(Setting::get('pagination'));

        return view('products.index', compact('products', 'term', 'archived'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }


    public function approvedListing(Request $request)
    {
        $cropped = $request->cropped;
        $colors = (new Colors)->all();
        $categories = Category::all();
        $category_tree = [];
        $categories_array = [];
        $brands = Brand::getAll();

        $suppliers = DB::select('
				SELECT id, supplier
				FROM suppliers

				INNER JOIN (
					SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
					) as product_suppliers
				ON suppliers.id = product_suppliers.supplier_id
		');

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    $category_tree[ $parent->parent_id ][ $parent->id ][ $category->id ];
                } else {
                    $category_tree[ $parent->id ][ $category->id ] = $category->id;
                }
            }

            $categories_array[ $category->id ] = $category->parent_id;
        }

        if ((int)$request->get('status_id') > 0) {
            $newProducts = Product::where('status_id', (int)$request->get('status_id'));
        } else {
            $newProducts = Product::where('status_id', StatusHelper::$finalApproval);
        }

        // Run through query helper
        $newProducts = QueryHelper::approvedListingOrder($newProducts);

        $term = $request->input('term');
        $brand = '';
        $category = '';
        $color = '';
        $supplier = [];
        $type = '';
        $assigned_to_users = '';

        if ($request->brand[ 0 ] != null) {
            $newProducts = $newProducts->whereIn('brand', $request->get('brand'));
        }

        if ($request->color[ 0 ] != null) {
            $newProducts = $newProducts->whereIn('color', $request->get('color'));
        }
        if ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) {
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

            $newProducts = $newProducts->whereIn('category', $category_children);
            $category = $request->category[ 0 ];
        }
        if ($request->type != '') {
            if ($request->type == 'Not Listed') {
                $newProducts = $newProducts->where('isFinal', 0)->where('isUploaded', 0);
            } else {
                if ($request->type == 'Listed') {
                    $newProducts = $newProducts->where('isUploaded', 1);
                } else {
                    if ($request->type == 'Approved') {
                        $newProducts = $newProducts->where('is_approved', 1);
                    } else {
                        if ($request->type == 'Image Cropped') {
                            $newProducts = $newProducts->where('is_image_processed', 1);
                        }
                    }
                }
            }

            $type = $request->get('type');
        }
        //
        if (trim($term) != '') {
            $newProducts = $newProducts->where(function ($query) use ($term) {
                $query->where('id', 'LIKE', "%$term%")->orWhere('sku', 'LIKE', "%$term%");
            });
        }


        if ($request->get('user_id') > 0) {
            $newProducts = $newProducts->where('approved_by', $request->get('user_id'));
        }


        $selected_categories = $request->category ? $request->category : [1];
        $category_array = Category::renderAsArray();
        $users = User::all();

        $newProducts = $newProducts->with(['media', 'brands', 'log_scraper_vs_ai'])->paginate(100);

        return view('products.final_listing', [
            'products' => $newProducts,
            'products_count' => $newProducts->total(),
            'colors' => $colors,
            'brands' => $brands,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'category_tree' => $category_tree,
            'categories_array' => $categories_array,
            // 'category_selection'	=> $category_selection,
            // 'category_search'	=> $category_search,
            'term' => $term,
            'brand' => $brand,
            'category' => $category,
            'color' => $color,
            'supplier' => $supplier,
            'type' => $type,
            'users' => $users,
            'assigned_to_users' => $assigned_to_users,
            'cropped'	=> $cropped,
//            'left_for_users'	=> $left_for_users,
            'category_array' => $category_array,
            'selected_categories' => $selected_categories,
        ]);
    }

    public function approvedListingCropConfirmation(Request $request)
    {
        $colors = (new Colors)->all();
        $categories = Category::all();
        $category_tree = [];
        $categories_array = [];
        $brands = Brand::getAll();

        $suppliers = DB::select('
				SELECT id, supplier
				FROM suppliers

				INNER JOIN (
					SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
					) as product_suppliers
				ON suppliers.id = product_suppliers.supplier_id
		');

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    $category_tree[ $parent->parent_id ][ $parent->id ][ $category->id ];
                } else {
                    $category_tree[ $parent->id ][ $category->id ] = $category->id;
                }
            }

            $categories_array[ $category->id ] = $category->parent_id;
        }

        // Prioritize suppliers
        $newProducts = Product::where('status_id', StatusHelper::$cropApprovalConfirmation);

        $newProducts = QueryHelper::approvedListingOrder($newProducts);

        $term = $request->input('term');
        $brand = '';
        $category = '';
        $color = '';
        $supplier = [];
        $type = '';
        $assigned_to_users = '';

        if ($request->brand[ 0 ] != null) {
            $newProducts = $newProducts->whereIn('brand', $request->get('brand'));
        }

        if ($request->color[ 0 ] != null) {
            $newProducts = $newProducts->whereIn('color', $request->get('color'));
        }
        if ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) {
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

            $newProducts = $newProducts->whereIn('category', $category_children);
            $category = $request->category[ 0 ];
        }
        if ($request->type != '') {
            if ($request->type == 'Not Listed') {
                $newProducts = $newProducts->where('isFinal', 0)->where('isUploaded', 0);
            } else {
                if ($request->type == 'Listed') {
                    $newProducts = $newProducts->where('isUploaded', 1);
                } else {
                    if ($request->type == 'Approved') {
                        $newProducts = $newProducts->where('is_approved', 1);
                    } else {
                        if ($request->type == 'Image Cropped') {
                            $newProducts = $newProducts->where('is_image_processed', 1);
                        }
                    }
                }
            }

            $type = $request->get('type');
        }
        //
        if (trim($term) != '') {
            $newProducts = $newProducts->where(function ($query) use ($term) {
                $query->where('id', 'LIKE', "%$term%")->orWhere('sku', 'LIKE', "%$term%");
            });
        }


        if ($request->get('user_id') > 0) {
            $newProducts = $newProducts->where('approved_by', $request->get('user_id'));
        }

        $selected_categories = $request->category ? $request->category : [1];
        $category_array = Category::renderAsArray();
        $users = User::all();

        $newProducts = QueryHelper::approvedListingOrder($newProducts);

        $newProducts = $newProducts->with(['media', 'brands', 'log_scraper_vs_ai'])->paginate(50);

        return view('products.final_crop_confirmation', [
            'products' => $newProducts,
            'products_count' => $newProducts->total(),
            'colors' => $colors,
            'brands' => $brands,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'category_tree' => $category_tree,
            'categories_array' => $categories_array,
            // 'category_selection'	=> $category_selection,
            // 'category_search'	=> $category_search,
            'term' => $term,
            'brand' => $brand,
            'category' => $category,
            'color' => $color,
            'supplier' => $supplier,
            'type' => $type,
            'users' => $users,
            'assigned_to_users' => $assigned_to_users,
//            'cropped'	=> $cropped,
//            'left_for_users'	=> $left_for_users,
            'category_array' => $category_array,
            'selected_categories' => $selected_categories,
        ]);
    }

    public function approvedMagento(Request $request)
    {
        // Get queue count
        $queueSize = Queue::size('listMagento');

        $colors = (new Colors)->all();
        $categories = Category::all();
        $category_tree = [];
        $categories_array = [];
        $brands = Brand::getAll();

        $suppliers = DB::select('
				SELECT id, supplier
				FROM suppliers

				INNER JOIN (
					SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
					) as product_suppliers
				ON suppliers.id = product_suppliers.supplier_id
		');

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    $category_tree[ $parent->parent_id ][ $parent->id ][ $category->id ];
                } else {
                    $category_tree[ $parent->id ][ $category->id ] = $category->id;
                }
            }

            $categories_array[ $category->id ] = $category->parent_id;
        }

        $newProducts = Product::where('isUploaded', 1)->orderBy('listing_approved_at', 'DESC');

        $term = $request->input('term');
        $brand = '';
        $category = '';
        $color = '';
        $supplier = [];
        $type = '';
        $assigned_to_users = '';

        if ($request->brand[ 0 ] != null) {
            $newProducts = $newProducts->whereIn('brand', $request->get('brand'));
        }

        if ($request->color[ 0 ] != null) {
            $newProducts = $newProducts->whereIn('color', $request->get('color'));
        }
        if ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) {
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

            $newProducts = $newProducts->whereIn('category', $category_children);
            $category = $request->category[ 0 ];
        }
        if ($request->type != '') {
            if ($request->type == 'Not Listed') {
                $newProducts = $newProducts->where('isFinal', 0)->where('isUploaded', 0);
            } else {
                if ($request->type == 'Listed') {
                    $newProducts = $newProducts->where('isUploaded', 1);
                } else {
                    if ($request->type == 'Approved') {
                        $newProducts = $newProducts->where('is_approved', 1);
                    } else {
                        if ($request->type == 'Image Cropped') {
                            $newProducts = $newProducts->where('is_image_processed', 1);
                        }
                    }
                }
            }

            $type = $request->get('type');
        }
        //
        if (trim($term) != '') {
            $newProducts = $newProducts->where(function ($query) use ($term) {
                $query->where('id', 'LIKE', "%$term%")->orWhere('sku', 'LIKE', "%$term%");
            });
        }


        if ($request->get('user_id') > 0) {
            $newProducts = $newProducts->where('approved_by', $request->get('user_id'));
        }


        $selected_categories = $request->category ? $request->category : [1];
        $category_array = Category::renderAsArray();
        $users = User::all();

        $newProducts = $newProducts->with(['media', 'brands'])->paginate(50);


        return view('products.in_magento', [
            'products' => $newProducts,
            'products_count' => $newProducts->total(),
            'colors' => $colors,
            'brands' => $brands,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'category_tree' => $category_tree,
            'categories_array' => $categories_array,
            // 'category_selection'	=> $category_selection,
            // 'category_search'	=> $category_search,
            'term' => $term,
            'brand' => $brand,
            'category' => $category,
            'color' => $color,
            'supplier' => $supplier,
            'type' => $type,
            'users' => $users,
            'assigned_to_users' => $assigned_to_users,
//            'cropped'	=> $cropped,
//            'left_for_users'	=> $left_for_users,
            'category_array' => $category_array,
            'selected_categories' => $selected_categories,
            'queueSize' => $queueSize
        ]);
    }

    public function showListigByUsers(Request $request)
    {
        $whereFirst = '';
        if ($request->get('date')) {
            $whereFirst = ' AND DATE(created_at) = "' . $request->get('date') . '"';
        }
        $users = UserProduct::groupBy(['user_id'])
            ->select(DB::raw('
            user_id,
            COUNT(product_id) as total_assigned, 
            (SELECT COUNT(DISTINCT(listing_histories.product_id)) FROM listing_histories WHERE listing_histories.user_id = user_products.user_id AND action IN ("LISTING_APPROVAL", "LISTING_REJECTED") ' . $whereFirst . ') as total_acted'));

        if ($request->get('date')) {
            $users = $users->whereRaw('DATE(created_at) = "' . $request->get('date') . '"');
        }

        $users = $users->with('user')->get();
        return view('products.assigned_products', compact('users'));

    }


    public function listing(Request $request, Stage $stage)
    {
        $colors = (new Colors)->all();
        $categories = Category::all();
        $category_tree = [];
        $categories_array = [];
        $brands = Brand::getAll();

        $suppliers = DB::select('
				SELECT id, supplier
				FROM suppliers

				INNER JOIN (
					SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
					) as product_suppliers
				ON suppliers.id = product_suppliers.supplier_id
		');

        // dd($suppliers);

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    $category_tree[ $parent->parent_id ][ $parent->id ][ $category->id ];
                } else {
                    $category_tree[ $parent->id ][ $category->id ] = $category->id;
                }
            }

            $categories_array[ $category->id ] = $category->parent_id;
        }

        // $category_selection = Category::attr(['name' => 'category', 'class' => 'form-control quick-edit-category', 'data-id' => ''])
        // 																			 ->renderAsDropdown();

        $term = $request->input('term');
        $brand = '';
        $category = '';
        $color = '';
        $supplier = [];
        $type = '';
        $assigned_to_users = '';

        $brandWhereClause = '';
        $colorWhereClause = '';
        $categoryWhereClause = '';
        $supplierWhereClause = '';
        $typeWhereClause = '';
        $termWhereClause = '';
        $croppedWhereClause = '';
        $stockWhereClause = ' AND stock >= 1';

        $userWhereClause = '';

        // if (Auth::user()->hasRole('Products Lister')) {
        // 	$products = Auth::user()->products();
        // } else {
        // 	$products = (new Product)->newQuery();
        // }


        if ($request->brand[ 0 ] != null) {
            // $products = $products->whereIn('brand', $request->brand);
            $brands_list = implode(',', $request->brand);

            $brand = $request->brand[ 0 ];
            $brandWhereClause = " AND brand IN ($brands_list)";
        }

        if ($request->color[ 0 ] != null) {
            // $products = $products->whereIn('color', $request->color);
            $colors_list = implode(',', $request->color);

            $color = $request->color[ 0 ];
            $colorWhereClause = " AND color IN ($colors_list)";
        }
        //
        if ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) {
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

            // $products = $products->whereIn('category', $category_children);
            $category_list = implode(',', $category_children);

            $category = $request->category[ 0 ];
            $categoryWhereClause = " AND category IN ($category_list)";
        }
        //
        if ($request->supplier[ 0 ] != null) {
            $suppliers_list = implode(',', $request->supplier);

            // $products = $products->with('Suppliers')
            // ->whereRaw("products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))");

            $supplier = $request->supplier;
            $supplierWhereClause = " AND products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))";
        }
        //
        if ($request->type != '') {
            if ($request->type == 'Not Listed') {
                // $products = $products->newQuery()->where('isFinal', 0)->where('isUploaded', 0);
                $typeWhereClause = ' AND isFinal = 0 AND isUploaded = 0';
            } else {
                if ($request->type == 'Listed') {
                    // $products = $products->where('isUploaded', 1);
                    $typeWhereClause = ' AND isUploaded = 1';
                } else {
                    if ($request->type == 'Approved') {
                        // $products = $products->where('is_approved', 1)->whereNull('last_imagecropper');
                        $typeWhereClause = ' AND is_approved = 1 AND last_imagecropper IS NULL';
                    } else {
                        if ($request->type == 'Image Cropped') {
                            // $products = $products->where('is_approved', 1)->whereNotNull('last_imagecropper');
                            $typeWhereClause = ' AND is_approved = 1 AND last_imagecropper IS NOT NULL';
                        }
                    }
                }
            }

            $type = $request->type;
        }
        //
        if (trim($term) != '') {
            // $products = $products
            // ->orWhere( 'sku', 'LIKE', "%$term%" )
            // ->orWhere( 'id', 'LIKE', "%$term%" )//		                                 ->orWhere( 'category', $term )
            // ;

            $termWhereClause = ' AND (sku LIKE "%' . $term . '%" OR id LIKE "%' . $term . '%")';

            // if ($term == - 1) {
            // 	$products = $products->orWhere( 'isApproved', - 1 );
            // }

            // if ( Brand::where('name', 'LIKE' ,"%$term%")->first() ) {
            // 	$brand_id = Brand::where('name', 'LIKE' ,"%$term%")->first()->id;
            // 	$products = $products->orWhere( 'brand', 'LIKE', "%$brand_id%" );
            // }
            //
            // if ( $category = Category::where('title', 'LIKE' ,"%$term%")->first() ) {
            // 	$category_id = $category = Category::where('title', 'LIKE' ,"%$term%")->first()->id;
            // 	$products = $products->orWhere( 'category', CategoryController::getCategoryIdByName( $term ) );
            // }
            //
            // if (!empty( $stage->getIDCaseInsensitive( $term ) ) ) {
            // 	$products = $products->orWhere( 'stage', $stage->getIDCaseInsensitive( $term ) );
            // }
        }
        //  else {
        // 	if ($request->brand[0] == null && $request->color[0] == null && ($request->category[0] == null || $request->category[0] == 1) && $request->supplier[0] == null && $request->type == '') {
        // 		$products = $products;
        // 	}
        // }


        // $products = $products->where('is_scraped', 1)->where('stock', '>=', 1);
        $cropped = $request->cropped == "on" ? "on" : '';
        if ($request->get('cropped') == 'on') {
            // $products = $products->where('is_image_processed', 1);
            $croppedWhereClause = ' AND is_crop_approved = 1';
        }

        if ($request->users == 'on') {
            $users_products = User::role('Products Lister')->pluck('id');
            // dd($users_products);
            $users = [];
            foreach ($users_products as $user) {
                $users[] = $user;
            }
            $users_list = implode(',', $users);

            $userWhereClause = " AND products.id IN (SELECT product_id FROM user_products WHERE user_id IN ($users_list))";
            $stockWhereClause = '';
            $assigned_to_users = 'on';
        }

        $left_for_users = '';
        if ($request->left_products == 'on') {
            // $users_products = User::role('Products Lister')->pluck('id');
            //
            // $users_list = implode(',', $users_products);

            $userWhereClause = " AND products.id NOT IN (SELECT product_id FROM user_products)";
            $stockWhereClause = " AND stock >= 1 AND is_crop_approved = 1 AND is_crop_ordered = 1 AND is_image_processed = 1 AND isUploaded = 0 AND isFinal = 0";
            $left_for_users = 'on';
        }

        // if (Auth::user()->hasRole('Products Lister')) {
        // 	// dd('as');
        // 	$products_count = Auth::user()->products;
        // 	$products = Auth::user()->products()->get()->toArray();

        // $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // $perPage = Setting::get('pagination');
        // $currentItems = array_slice($products, $perPage * ($currentPage - 1), $perPage);
        //
        // $products = new LengthAwarePaginator($currentItems, count($products), $perPage, $currentPage, [
        //   'path'  => LengthAwarePaginator::resolveCurrentPath()
        // ]);

        // dd($products);
        // } else {
        // $products_count = $products->take(5000)->get();
        // $products = $products->take(5000)->orderBy('is_image_processed', 'DESC')->orderBy('created_at', 'DESC')->get()->toArray();

        $messages = UserProductFeedback::where('action', 'LISTING_APPROVAL_REJECTED')->where('user_id', Auth::id())->with('product')->get();

        if (Auth::user()->hasRole('Products Lister')) {
            $sql = '
											SELECT *, user_products.user_id as product_user_id,
											(SELECT mm1.created_at FROM remarks mm1 WHERE mm1.id = remark_id) AS remark_created_at
											FROM products

											LEFT JOIN (
												SELECT user_id, product_id FROM user_products
												) as user_products
											ON products.id = user_products.product_id

											LEFT JOIN (
												SELECT MAX(id) AS remark_id, taskid FROM remarks WHERE module_type = "productlistings" GROUP BY taskid
												) AS remarks
											ON products.id = remarks.taskid

											WHERE stock>=1 AND is_approved = 0 AND is_listing_rejected = 0 AND is_crop_approved = 1 AND is_crop_ordered = 1 ' . $brandWhereClause . $colorWhereClause . $categoryWhereClause . $supplierWhereClause . $typeWhereClause . $termWhereClause . $croppedWhereClause . $stockWhereClause . ' AND id IN (SELECT product_id FROM user_products WHERE user_id = ' . Auth::id() . ')
											 AND id NOT IN (SELECT product_id FROM product_suppliers WHERE supplier_id = 60)
											ORDER BY listing_approved_at DESC, category, is_crop_ordered DESC, remark_created_at DESC, created_at DESC
				';
        } else {
            $sql = '
                SELECT *, user_products.user_id as product_user_id,
                (SELECT mm1.created_at FROM remarks mm1 WHERE mm1.id = remark_id) AS remark_created_at
                FROM products

                LEFT JOIN (
                    SELECT user_id, product_id FROM user_products
                    ) as user_products
                ON products.id = user_products.product_id

                LEFT JOIN (
                    SELECT MAX(id) AS remark_id, taskid FROM remarks WHERE module_type = "productlistings" GROUP BY taskid
                    ) AS remarks
                ON products.id = remarks.taskid
                WHERE stock>=1 AND is_approved = 0 AND is_listing_rejected = 0  AND is_crop_approved = 1 AND is_crop_ordered = 1  ' . $stockWhereClause . $brandWhereClause . $colorWhereClause . $categoryWhereClause . $supplierWhereClause . $typeWhereClause . $termWhereClause . $croppedWhereClause . $userWhereClause . '
                ORDER BY listing_approved_at DESC, category, is_crop_ordered DESC, remark_created_at DESC, products.updated_at DESC
				';
        }
        $new_products = DB::select($sql);

//			dd($new_products);
        $products_count = count($new_products);
        //
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        $currentItems = array_slice($new_products, $perPage * ($currentPage - 1), $perPage);

        $new_products = new LengthAwarePaginator($currentItems, count($new_products), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);
        // }
        // dd($products);

        $selected_categories = $request->category ? $request->category : [1];
        // $category_search = Category::attr(['name' => 'category[]','class' => 'form-control'])
        //                                         ->selected($selected_categories)
        //                                         ->renderAsDropdown();

        $category_array = Category::renderAsArray();

        $userStats = [];
        $userStats[ 'approved' ] = ListingHistory::where('action', 'LISTING_APPROVAL')->where('user_id', Auth::user()->id)->count();
        $userStats[ 'rejected' ] = ListingHistory::where('action', 'LISTING_REJECTED')->where('user_id', Auth::user()->id)->count();

        // dd($category_array);

        return view('products.listing', [
            'products' => $new_products,
            'products_count' => $products_count,
            'colors' => $colors,
            'brands' => $brands,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'category_tree' => $category_tree,
            'categories_array' => $categories_array,
            // 'category_selection'	=> $category_selection,
            // 'category_search'	=> $category_search,
            'term' => $term,
            'brand' => $brand,
            'category' => $category,
            'color' => $color,
            'supplier' => $supplier,
            'type' => $type,
            'assigned_to_users' => $assigned_to_users,
            'cropped' => $cropped,
            'left_for_users' => $left_for_users,
            'category_array' => $category_array,
            'selected_categories' => $selected_categories,
            'messages' => $messages,
            'userStatus' => $userStats
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, Sizes $sizes)
    {
        $data = [];

        $data[ 'dnf' ] = $product->dnf;
        $data[ 'id' ] = $product->id;
        $data[ 'name' ] = $product->name;
        $data[ 'short_description' ] = $product->short_description;
        $data[ 'activities' ] = $product->activities;
        $data[ 'scraped' ] = $product->scraped_products;

        $data[ 'measurement_size_type' ] = $product->measurement_size_type;
        $data[ 'lmeasurement' ] = $product->lmeasurement;
        $data[ 'hmeasurement' ] = $product->hmeasurement;
        $data[ 'dmeasurement' ] = $product->dmeasurement;

        $data[ 'size' ] = $product->size;
        $data[ 'size_value' ] = $product->size_value;
        $data[ 'sizes_array' ] = $sizes->all();

        $data[ 'composition' ] = $product->composition;
        $data[ 'sku' ] = $product->sku;
        $data[ 'made_in' ] = $product->made_in;
        $data[ 'brand' ] = $product->brand;
        $data[ 'color' ] = $product->color;
        $data[ 'price' ] = $product->price;
        $data['status'] = $product->status_id;
//		$data['price'] = $product->inr;
        $data[ 'euro_to_inr' ] = $product->euro_to_inr;
        $data[ 'price_inr' ] = $product->price_inr;
        $data[ 'price_inr_special' ] = $product->price_inr_special;

        $data[ 'isApproved' ] = $product->isApproved;
        $data[ 'rejected_note' ] = $product->rejected_note;
        $data[ 'isUploaded' ] = $product->isUploaded;
        $data[ 'isFinal' ] = $product->isFinal;
        $data[ 'stock' ] = $product->stock;
        $data[ 'reason' ] = $product->rejected_note;

        $data[ 'product_link' ] = $product->product_link;
        $data[ 'supplier' ] = $product->supplier;
        $data[ 'supplier_link' ] = $product->supplier_link;
        $data[ 'description_link' ] = $product->description_link;
        $data[ 'location' ] = $product->location;

        $data[ 'suppliers' ] = '';

        foreach ($product->suppliers as $key => $supplier) {
            if ($key == 0) {
                $data[ 'suppliers' ] .= $supplier->supplier;
            } else {
                $data[ 'suppliers' ] .= ", $supplier->supplier";
            }
        }

        $data[ 'images' ] = $product->getMedia(config('constants.media_tags'));


        $data[ 'categories' ] = $product->category ? CategoryController::getCategoryTree($product->category) : '';

        $data[ 'has_reference' ] = ScrapedProducts::where('sku', $product->sku)->first() ? true : false;

        $data[ 'product' ] = $product;

        return view('partials.show', $data);
    }

    public function bulkUpdate(Request $request)
    {
        $selected_products = json_decode($request->selected_products, true);
        $category = $request->category[ 0 ];

        foreach ($selected_products as $id) {
            $product = Product::find($id);
            $product->category = $category;
            $product->save();

            $lh = new ListingHistory();
            $lh->user_id = Auth::user()->id;
            $lh->product_id = $id;
            $lh->content = ['Category updated', $category];
            $lh->save();

        }

        return redirect()->back()->withSuccess('You have successfully bulk updated products!');
    }

    public function updateName(Request $request, $id)
    {
        $product = Product::find($id);
        $product->name = $request->name;
        $product->save();

        $lh = new ListingHistory();
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Name updated', $request->get('name')];
        $lh->save();

        return response('success');
    }

    public function updateDescription(Request $request, $id)
    {
        $product = Product::find($id);
        $product->short_description = $request->description;
        $product->save();

        $lh = new ListingHistory();
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Description updated', $request->get('description')];
        $lh->save();

        return response('success');
    }

    public function updateComposition(Request $request, $id)
    {
        $product = Product::find($id);
        $product->composition = $request->composition;
        $product->save();

        $lh = new ListingHistory();
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Composition updated', $request->get('composition')];
        $lh->save();

        return response('success');
    }

    public function updateColor(Request $request, $id)
    {
        $product = Product::find($id);
        $originalColor = $product->color;
        $product->color = $request->color;
        $product->save();

        $lh = new ListingHistory();
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Color updated', $request->get('color')];
        $lh->save();

        if (!$originalColor) {
            return response('success');
        }

        $color = (new Colors)->getID($originalColor);
        if ($color) {
            return response('success');
        }


        $colorReference = ColorReference::where('original_color', $originalColor)->first();
        if ($colorReference) {
            return response('success');
        }

        $colorReference = new ColorReference();
        $colorReference->original_color = $originalColor;
        $colorReference->brand_id = $product->brand;
        $colorReference->erp_color = $request->get('color');
        $colorReference->save();

        return response('success');

    }

    public function updateCategory(Request $request, $id)
    {
        $product = Product::find($id);
        $product->category = $request->category;
        $product->save();

        $lh = new ListingHistory();
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Category updated', $request->get('category')];
        $lh->save();

        return response('success');
    }

    public function updateSize(Request $request, $id)
    {
        $product = Product::find($id);
        $product->size = is_array($request->size) && count($request->size) > 0 ? implode(',', $request->size) : '';
        $product->lmeasurement = $request->lmeasurement;
        $product->hmeasurement = $request->hmeasurement;
        $product->dmeasurement = $request->dmeasurement;
        $product->save();

        $lh = new ListingHistory();
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Sizes updated', $request->get('lmeasurement') . ' X ' . $request->get('hmeasurement') . ' X ' . $request->get('dmeasurement')];
        $lh->save();

        return response('success');
    }

    public function updatePrice(Request $request, $id)
    {
        $product = Product::find($id);
        $product->price = $request->price;

        if (!empty($product->brand)) {
            $product->price_inr = $this->euroToInr($product->price, $product->brand);
            $product->price_inr_special = $this->calculateSpecialDiscount($product->price_inr_special, $product->brand);
        }

        $product->save();

        $l = new ListingHistory();
        $l->user_id = Auth::user()->id;
        $l->product_id = $id;
        $l->content = ['Price updated', $product->price];

        return response()->json([
            'price_inr' => $product->price_inr,
            'price_inr_special' => $product->price_inr_special
        ]);
    }

    public function quickDownload($id)
    {
        $product = Product::find($id);

        $products_array = [];

        if ($product->hasMedia(config('constants.media_tags'))) {
            foreach ($product->getMedia(config('constants.media_tags')) as $image) {
                $path = public_path('uploads') . '/' . $image->filename . '.' . $image->extension;
                array_push($products_array, $path);
            }
        }


        \Zipper::make(public_path("$product->sku.zip"))->add($products_array)->close();

        return response()->download(public_path("$product->sku.zip"))->deleteFileAfterSend();
    }

    public function quickUpload(Request $request, $id)
    {
        $product = Product::find($id);
        $image_url = '';

        if ($request->hasFile('images')) {
            $product->detachMediaTags(config('constants.media_tags'));

            foreach ($request->file('images') as $key => $image) {
                $media = MediaUploader::fromSource($image)
                                        ->toDirectory('product/'.floor($product->id / config('constants.image_per_folder')))
                                        ->upload();
                $product->attachMedia($media, config('constants.media_tags'));

                if ($key == 0) {
                    $image_url = $media->getUrl();
                }
            }

            $product->last_imagecropper = Auth::id();
            $product->save();
        }

        return response()->json([
            'image_url' => $image_url,
            'last_imagecropper' => $product->last_imagecropper
        ]);
    }

    public function calculateSpecialDiscount($price, $brand)
    {
        $dis_per = BrandController::getDeductionPercentage($brand);
        $dis_price = $price - ($price * $dis_per) / 100;

        return round($dis_price, -3);
    }

    public function euroToInr($price, $brand)
    {
        $euro_to_inr = BrandController::getEuroToInr($brand);

        if (!empty($euro_to_inr)) {
            $inr = $euro_to_inr * $price;
        } else {
            $inr = Setting::get('euro_to_inr') * $price;
        }

        return round($inr, -3);
    }

    public function listMagento(Request $request, $id)
    {
        // Get product by ID
        $product = Product::find($id);

        // If we have a product, push it to Magento
        if ($product !== null) {
            // Dispatch the job to the queue
            PushToMagento::dispatch($product)->onQueue('magento');

            // Update the product so it doesn't show up in final listing
            $product->isUploaded = 1;
            $product->save();

            // Return response
            return response()->json([
                'result' => 'queuedForDispatch',
                'status' => 'listed'
            ]);
        }

        // Return error response by default
        return response()->json([
            'result' => 'productNotFound',
            'status' => 'error'
        ]);
    }

    public function unlistMagento(Request $request, $id)
    {
        $product = Product::find($id);

        $result = app('App\Http\Controllers\ProductApproverController')->magentoSoapUnlistProduct($product);

        return response()->json([
            'result' => $result,
            'status' => 'unlisted'
        ]);
    }

    public function approveMagento(Request $request, $id)
    {
        $product = Product::find($id);

        $result = app('App\Http\Controllers\ProductApproverController')->magentoSoapUpdateStatus($product);

        return response()->json([
            'result' => $result,
            'status' => 'approved'
        ]);
    }

    public function updateMagento(Request $request, $id)
    {
        $product = Product::find($id);

        $result = app('App\Http\Controllers\ProductAttributeController')->magentoProductUpdate($product);

        return response()->json([
            'result' => $result[ 1 ],
            'status' => 'updated'
        ]);
    }

    public function approveProduct(Request $request, $id)
    {
        $product = Product::find($id);

        $product->is_approved = 1;
        $product->approved_by = Auth::user()->id;
        $product->listing_approved_at = Carbon::now()->toDateTimeString();
        $product->save();

        $l = new ListingHistory();
        $l->user_id = Auth::user()->id;
        $l->product_id = $product->id;
        $l->action = 'LISTING_APPROVAL';
        $l->content = ['action' => 'LISTING_APPROVAL', 'message' => 'Listing approved!'];
        $l->save();

        ActivityConroller::create($product->id, 'productlister', 'create');

//		if (Auth::user()->hasRole('Products Lister')) {
//			$products_count = Auth::user()->products()->count();
//			$approved_products_count = Auth::user()->approved_products()->count();
//			if (($products_count - $approved_products_count) < 100) {
//				$requestData = new Request();
//				$requestData->setMethod('POST');
//				$requestData->request->add(['amount_assigned' => 100]);
//
//				app('App\Http\Controllers\UserController')->assignProducts($requestData, Auth::id());
//			}
//		}

        return response()->json([
            'result' => true,
            'status' => 'is_approved'
        ]);
    }

    public function archive($id)
    {
        $product = Product::find($id);
        $product->delete();

        return redirect()->back()
            ->with('success', 'Product archived successfully');
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->find($id);
        $product->restore();

        return redirect()->back()
            ->with('success', 'Product restored successfully');
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $product->forceDelete();

        return redirect()->back()
            ->with('success', 'Product deleted successfully');
    }

    public function attachProducts($model_type, $model_id, $type = null, $customer_id = null, Request $request)
    {

        $roletype = $request->input('roletype') ?? 'Sale';
        $products = Product::where('stock', '>=', 1)
            ->select(['id', 'sku', 'size', 'price_inr_special', 'brand', 'isApproved', 'stage', 'created_at'])
            ->orderBy("created_at","DESC")
            ->paginate(Setting::get('pagination'));

        $doSelection = true;
        $customer_id = $customer_id ?? null;

        if ($type == 'images') {
            $attachImages = true;
        } else {
            $attachImages = false;
        }

        if ($model_type == 'broadcast-images') {
            $attachImages = true;
            $doSelection = false;
        }

        if (Order::find($model_id)) {
            $selected_products = self::getSelectedProducts($model_type, $model_id);
        } else {
            $selected_products = [];
        }

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control'])
            ->selected(1)
            ->renderAsDropdown();


        return view('partials.grid', compact('products', 'roletype', 'model_id', 'selected_products', 'doSelection', 'model_type', 'category_selection', 'attachImages', 'customer_id'));
    }

    public function attachImages($model_type, $model_id = null, $status = null, $assigned_user = null, Request $request)
    {
        $roletype = $request->input('roletype') ?? 'Sale';
        $term = $request->input('term');
        $perPageLimit = $request->get("per_page");

        if (Order::find($model_id)) {
            $selected_products = self::getSelectedProducts($model_type, $model_id);
        } else {
            $selected_products = [];
        }

        if (empty($perPageLimit)) {
            $perPageLimit = Setting::get('pagination');
        }

        $sourceOfSearch = $request->get("source_of_search", "na");

        // start add fixing for the price range since the one request from price is in range
        // price  = 0 , 100

        $priceRange = $request->get("price", null);

        if ($priceRange && !empty($priceRange)) {
            @list($minPrice, $maxPrice) = explode(",", $priceRange);
            // adding min price
            if (isset($minPrice)) {
                $request->request->add(['price_min' => $minPrice]);
            }
            // addin max price
            if (isset($maxPrice)) {
                $request->request->add(['price_max' => $maxPrice]);
            }
        }

        $products = (new Product())->newQuery()->latest();

        if ($request->brand[ 0 ] != null) {
            $products = $products->whereIn('brand', $request->brand);
        }

        if ($request->color[ 0 ] != null) {
            $products = $products->whereIn('color', $request->color);
        }

        if ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) {
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
        }

        if ($request->price_min != null) {
            $products = $products->where('price_inr_special', '>=', $request->price_min);
        }

        if ($request->price_max != null) {
            $products = $products->where('price_inr_special', '<=', $request->price_max);
        }

        if ($request->supplier[ 0 ] != null) {
            $suppliers_list = implode(',', $request->supplier);

            $products = $products->whereRaw("products.id in (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))");
        }

        if (trim($request->size) != '') {
            $products = $products->whereNotNull('size')->where(function ($query) use ($request) {
                $query->where('size', $request->size)->orWhere('size', 'LIKE', "%$request->size,")->orWhere('size', 'LIKE', "%,$request->size,%");
            });
        }

        if ($request->location[ 0 ] != null) {
            $products = $products->whereIn('location', $request->location);
        }

        if ($request->type[ 0 ] != null && is_array($request->type)) {
            if (count($request->type) > 1) {
                $products = $products->where(function ($query) use ($request) {
                    $query->where('is_scraped', 1)->orWhere('status', 2);
                });
            } else {
                if ($request->type[ 0 ] == 'scraped') {
                    $products = $products->where('is_scraped', 1);
                } elseif ($request->type[ 0 ] == 'imported') {
                    $products = $products->where('status', 2);
                } else {
                    $products = $products->where('isUploaded', 1);
                }
            }
        }

        if ($request->date != '') {
            if (isset($products)) {
                if ($request->type[ 0 ] != null && $request->type[ 0 ] == 'uploaded') {
                    $products = $products->where('is_uploaded_date', 'LIKE', "%$request->date%");
                } else {
                    $products = $products->where('created_at', 'LIKE', "%$request->date%");
                }
            }
        }

        if (trim($term) != '') {
            $products = $products->where(function ($query) use ($term) {
                $query->where('sku', 'LIKE', "%$term%")
                      ->orWhere('id', 'LIKE', "%$term%")
                      ->orWhere('name', 'LIKE', "%$term%")
                      ->orWhere('short_description', 'LIKE', "%$term%");

                if ($term == -1) {
                    $query = $query->orWhere('isApproved', -1);
                }

                $brand_id = \App\Brand::where('name', 'LIKE', "%$term%")->value('id');
                if ($brand_id) {
                    $products = $products->orWhere('brand', 'LIKE', "%$brand_id%");
                }

                $category_id = $category = Category::where('title', 'LIKE', "%$term%")->value('id');
                if ($category_id) {
                    $products = $products->orWhere('category', $category_id);
                }

            });

            if ($roletype != 'Selection' && $roletype != 'Searcher') {

                $products = $products->whereNull('dnf');
            }
        }

        if ($request->ids[ 0 ] != null) {
            $products = $products->whereIn('id', $request->ids);
        }


        $selected_categories = $request->category ? $request->category : 1;

        if ($request->quick_product === 'true') {
            $products = $products->where('quick_product', 1);
        }

        // assing product to varaible so can use as per condition for join table media
        if ($request->quick_product !== 'true') {
            $products = $products->whereRaw("(stock > 0 OR (supplier LIKE '%In-Stock%'))");
        }

        // if source is attach_media for search then check product has image exist or not
        $products = $products->join("mediables", function ($query) {
            $query->on("mediables.mediable_id", "products.id")->where("mediable_type", 'like', "App%Product");
        });

        if($request->get("unsupported",null) != "") {

            $mediaIds = \DB::table("media")->where("aggregate_type","image")->join("mediables", function ($query) {
                $query->on("mediables.media_id", "media.id")->where("mediables.mediable_type", 'like', "App%Product");
            })->whereNotIn("extension",config("constants.gd_supported_files"))->select("id")->pluck("id")->toArray();

            $products = $products->whereIn("mediables.media_id",$mediaIds);
        }

        $products = $products->groupBy('products.id');

        if (!empty($request->quick_sell_groups) && is_array($request->quick_sell_groups)) {
            $products = $products->whereRaw("(id in (select product_id from product_quicksell_groups where quicksell_group_id in (".implode(",", $request->quick_sell_groups).") ))");
        }

        // select fields..
        $products = $products->select(['products.id','name','short_description','color','sku', 'products.size', 'price_eur_special', 'price_inr_special', 'supplier', 'purchase_status', 'products.created_at']);

        if ($request->get('is_on_sale') == 'on') {
            $products = $products->where('is_on_sale', 1);
        }

        $products_count = $products->get()->count();

        if($request->has("limit")) {
            $perPageLimit = ($request->get("limit") == "all") ? $products_count : $request->get("limit");
        }

        $products = $products->paginate($perPageLimit);
        $all_product_ids = [];
        if ($request->ajax()) {
            $html = view('partials.image-load', [
                'products' => $products,
                'all_product_ids' => $all_product_ids,
                'selected_products' => $request->selected_products ? json_decode($request->selected_products) : [],
                'model_type' => $model_type
            ])->render();

            return response()->json(['html' => $html,'products_count' => $products_count]);
        }

        $filtered_category = json_decode($request->category, true);
        $brand = $request->brand;
        $message_body = $request->message ? $request->message : '';
        $sending_time = $request->sending_time ?? '';
        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple-cat-list input-lg', 'multiple' => 'multiple', 'data-placeholder' => 'Select Category..'])
            ->selected($filtered_category)
            ->renderAsDropdown();

        $locations = \App\ProductLocation::pluck("name","name");
        $suppliers = Supplier::select(['id', 'supplier'])->whereIn('id', DB::table('product_suppliers')->selectRaw('DISTINCT(`supplier_id`) as suppliers')->pluck('suppliers')->toArray())->get();

        $quick_sell_groups = \App\QuickSellGroup::select('id', 'name')->orderBy('id', 'desc')->get();

        return view('partials.image-grid', compact('products', 'products_count', 'roletype', 'model_id', 'selected_products', 'model_type', 'status', 'assigned_user', 'category_selection', 'brand', 'filtered_category',  'message_body', 'sending_time', 'locations', 'suppliers', 'all_product_ids', 'quick_sell_groups'));
    }


    public function attachProductToModel($model_type, $model_id, $product_id)
    {

        switch ($model_type) {
            case 'order':
                $action = OrderController::attachProduct($model_id, $product_id);

                break;

            case  'sale':
                $action = SaleController::attachProduct($model_id, $product_id);
                break;
            case 'stock':
                $stock = Stock::find($model_id);
                $product = Product::find($product_id);

                $stock->products()->attach($product);
                $action = 'Attached';
                break;
        }


        return ['msg' => 'success', 'action' => $action];
    }

    public static function getSelectedProducts($model_type, $model_id)
    {
        $selected_products = [];

        switch ($model_type) {
            case 'order':
                $order = Order::find($model_id);
                if(!empty($order)) {
                    $selected_products = $order->order_product()->with('product')->get()->pluck('product.id')->toArray();
                }
                break;

            case 'sale':
                $sale = Sale::find($model_id);
                if(!empty($sale)) {
                    $selected_products = json_decode($sale->selected_product, true) ?? [];
                }
                break;

            default :
                $selected_products = [];
        }

        return $selected_products;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'sku' => 'required|unique:products'
        ]);

        $product = new Product;

        // return response()->json(['ok' => $request->file('image')->getClientOriginalExtension()]);

        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->size = is_array($request->size) ? implode(',', $request->size) : ($request->size ?? $request->other_size);
        $product->brand = $request->brand;
        $product->color = $request->color;
        $product->supplier = $request->supplier;
        $product->location = $request->location;
        $product->category = $request->category ?? 1;
        $product->price = $request->price;
        $product->stock = 1;

        $brand = Brand::find($request->brand);

        if ($request->price) {
            if (isset($request->brand) && !empty($brand->euro_to_inr)) {
                $product->price_inr = $brand->euro_to_inr * $product->price;
            } else {
                $product->price_inr = Setting::get('euro_to_inr') * $product->price;
            }

            $deduction_percentage = $brand && $brand->deduction_percentage ? $brand->deduction_percentage : 1;
            $product->price_inr = round($product->price_inr, -3);
            $product->price_inr_special = $product->price_inr - ($product->price_inr * $deduction_percentage) / 100;

            $product->price_inr_special = round($product->price_inr_special, -3);
        }

        $product->save();

        if ($request->supplier == 'In-stock') {
            $product->suppliers()->attach(11); // In-stock ID
        }

        $product->detachMediaTags(config('constants.media_tags'));
        $media = MediaUploader::fromSource($request->get('is_image_url') ? $request->get('image') : $request->file('image'))
                                ->toDirectory('product/'.floor($product->id / config('constants.image_per_folder')))
                                ->upload();
        $product->attachMedia($media, config('constants.media_tags'));

        $product_image = $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getUrl() : '';

        if ($request->order_id) {
            $order_product = new OrderProduct;

            $order_product->order_id = $request->order_id;
            $order_product->sku = $request->sku;
            $order_product->product_price = $request->price_inr_special;
            $order_product->size = $request->size;
            $order_product->color = $request->color;
            $order_product->qty = $request->quantity;

            $order_product->save();

            // return response($product);

            return response(['product' => $product, 'order' => $order_product, 'quantity' => $request->quantity, 'product_image' => $product_image]);
        } elseif ($request->stock_id) {
            $stock = Stock::find($request->stock_id);
            $stock->products()->attach($product);

            return response(['product' => $product, 'product_image' => $product_image]);
        }

        if ($request->ajax()) {
            return response()->json(['msg' => 'success']);
        }

        return redirect()->back()->with('success', 'You have successfully uploaded product!');
    }

    public function giveImage()
    {
        // Get next product
        $product = Product::where('status_id', StatusHelper::$autoCrop)
            ->where('category', '>', 3);

        // Add order
        $product = QueryHelper::approvedListingOrder($product);

        // Get first product
        $product = $product->first();

        if (!$product) {
            // Return JSON
            return response()->json([
                'status' => 'no_product'
            ]);
        }

        // Get images
        $images = $product->media()->get(['filename', 'extension', 'mime_type', 'disk', 'directory']);

        // Get category
        $category = $product->product_category;

        // Get other information related to category
        $cat = $category->title;
        $parent = '';
        $child = '';

        try {
            if ($cat != 'Select Category') {
                if ($category->isParent($category->id)) {
                    $parent = $cat;
                    $child = $cat;
                } else {
                    $parent = $category->parent()->first()->title;
                    $child = $cat;
                }
            }
        } catch ( \ErrorException $e ) {
            //
        }

        // Set new status
        $product->status_id = StatusHelper::$isBeingCropped;
        $product->save();

        // Return product
        return response()->json([
            'product_id' => $product->id,
            'image_urls' => $images,
            'l_measurement' => $product->lmeasurement,
            'h_measurement' => $product->hmeasurement,
            'd_measurement' => $product->dmeasurement,
            'category' => "$parent $child",
            '' => ''
        ]);

    }

    public function saveImage(Request $request)
    {
        // Find the product or fail
        $product = Product::findOrFail($request->get('product_id'));

        // Check if this product is being cropped
        if ($product->status_id != StatusHelper::$isBeingCropped) {
            return response()->json([
                'status' => 'unknown product'
            ], 400);
        }

        // Check if we have a file
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $media = MediaUploader::fromSource($image)
                                    ->useFilename('CROPPED_' . time() . '_' . rand(555, 455545))
                                    ->toDirectory('product/'.floor($product->id / config('constants.image_per_folder')).'/' . $product->id)
                                    ->upload();
            $product->attachMedia($media, config('constants.media_gallery_tag'));
            $product->crop_count = $product->crop_count + 1;
            $product->save();

            $imageReference = new CroppedImageReference();
            $imageReference->original_media_id = $request->get('media_id');
            $imageReference->new_media_id = $media->id;
            $imageReference->original_media_name = $request->get('filename');
            $imageReference->new_media_name = $media->filename . '.' . $media->extension;
            $imageReference->speed = $request->get('time');
            $imageReference->product_id = $product->id;
            $imageReference->save();

            $product->cropped_at = Carbon::now()->toDateTimeString();
            $product->status_id = StatusHelper::$cropApproval;
            $product->save();

            // get the status as per crop
            if($product->category > 0) {
                $category = \App\Category::find($product->category);
                if(!empty($category) && $category->status_after_autocrop > 0) {
                    \App\Helpers\StatusHelper::updateStatus($product,$category->status_after_autocrop);
                }
            }

        } else {
            $product->status_id = StatusHelper::$cropSkipped;
            $product->save();
        }


        return response()->json([
            'status' => 'success'
        ]);
    }

    public function rejectedListingStatistics()
    {
        $products = DB::table('products')->where('is_listing_rejected', 1)->groupBy(['listing_remark', 'supplier'])->selectRaw('COUNT(*) as total_count, supplier, listing_remark')->orderBy('total_count', 'DESC')->get();


        return view('products.rejected_stats', compact('products'));
    }

    public function addListingRemarkToProduct(Request $request)
    {
        $productId = $request->get('product_id');
        $remark = $request->get('remark');

        $product = Product::find($productId);
        if ($product) {
            $product->listing_remark = $remark;
            $product->is_listing_rejected = $request->get('rejected');
            $product->listing_rejected_by = Auth::user()->id;
            $product->is_approved = 0;
            $product->listing_rejected_on = date('Y-m-d');
            $product->save();
        }

        if ($request->get('senior') && $product) {
            $s = new UserProductFeedback();
            $s->user_id = $product->approved_by;
            $s->senior_user_id = Auth::user()->id;
            $s->action = 'LISTING_APPROVAL_REJECTED';
            $s->content = ['action' => 'LISTING_APPROVAL_REJECTED', 'previous_action' => 'LISTING_APPROVAL', 'current_action' => 'LISTING_REJECTED', 'message' => 'Your listing has been rejected because of : ' . $remark];
            $s->message = "Your listing approval has been discarded by the Admin because of this issue: $remark. Please make sure you check these details before approving any future product.";
            $s->product_id = $product->id;
            $s->save();
        }

        if ($request->get('rejected') && $product) {
            $l = new ListingHistory();
            $l->action = 'LISTING_REJECTED';
            $l->content = ['action' => 'LISTING_REJECTED', 'page' => 'LISTING'];
            $l->user_id = Auth::user()->id;
            $l->product_id = $product->id;
            $l->save();
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function showAutoRejectedProducts()
    {
        $totalRemaining = Product::where('stock', '>=', 1)->where('is_listing_rejected_automatically', 1)->count();
        $totalDone = Product::where('stock', '>=', 1)->where('was_auto_rejected', 1)->count();

        return view('products.auto_rejected_stats', compact('totalDone', 'totalRemaining'));
    }

    public function affiliateProducts(Request $request)
    {
        $colors = (new Colors)->all();
        $category_tree = [];
        $brands = Brand::all();
        $brand = null;
        $price = null;
        $color = [];
        $products = Product::where('is_without_image', 0);

        if ($request->get('sku')) {
            $products = $products->where(function ($query) use ($request) {
                $sku = $request->get('sku');
                $query->where('sku', $sku)
                    ->orWhere('name', 'LIKE', "%$sku%")
                    ->orWhere('short_description', 'LIKE', "%$sku%");
            });
        }

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    $category_tree[ $parent->parent_id ][ $parent->id ][ $category->id ];
                } else {
                    $category_tree[ $parent->id ][ $category->id ] = $category->id;
                }
            }

            $categories_array[ $category->id ] = $category->parent_id;
        }

        if ($request->get('brand') > 0) {
            $brand = $request->get('brand');
            $products = $products->where('brand', $brand);
        }

        $selected_categories = $request->get('category') ? : [1];
        if ($request->get('category')[ 0 ] != null && $request->get('category')[ 0 ] != 1) {
            $category_children = [];

            foreach ($request->get('category') as $category) {
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
        }

        if ($request->color[ 0 ] != null) {
            $products = $products->whereIn('color', $request->color);
            $color = $request->color;
        }

        if ($request->get('price')[ 0 ] !== null) {
            $price = $request->get('price');
            $price = explode(',', $price);
            $products = $products->whereBetween('price_inr_special', [$price[ 0 ], $price[ 1 ]]);
        }

        $category_array = Category::renderAsArray();

        $products = $products->paginate(20);

        $c = $color;


        return view('products.affiliate', compact('products', 'request', 'brands', 'categories_array', 'category_array', 'selected_categories', 'brand', 'colors', 'c', 'price'));

    }

    public function showRejectedListedProducts(Request $request)
    {
        $products = new Product;
        $products = $products->where('stock', '>=', 1);
        $reason = '';
        $supplier = [];
        $selected_categories = [];

        if ($request->get('reason') !== '') {
            $reason = $request->get('reason');
            $products = $products->where('listing_remark', 'LIKE', "%$reason%");
        }

        if ($request->get('date') !== '') {
            $date = $request->get('date');
            $products = $products->where('listing_rejected_on', 'LIKE', "%$date%");
        }

        if ($request->get('id') !== '') {
            $id = $request->get('id');
            $products = $products->where('id', $id)->orWhere('sku', 'LIKE', "%$id%");
        }

        if ($request->get('user_id') > 0) {
            $products = $products->where('listing_rejected_by', $request->get('user_id'));
        }

        if ($request->get('type') === 'accepted') {
            $products = $products->where('is_listing_rejected', 0)->where('listing_remark', '!=', '');
        } else {
            $products = $products->where('is_listing_rejected', 1);
        }

        $suppliers = DB::select('
				SELECT id, supplier
				FROM suppliers

				INNER JOIN (
					SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
					) as product_suppliers
				ON suppliers.id = product_suppliers.supplier_id
		');

        if ($request->supplier[ 0 ] != null) {

            $supplier = $request->get('supplier');
            $products = $products->whereIn('id', DB::table('product_suppliers')->whereIn('supplier_id', $supplier)->pluck('product_id'));
        }


        if ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) {
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
            $selected_categories = [$request->get('category')[ 0 ]];
        }
        $users = User::all();

        $category_array = Category::renderAsArray();

        $products = $products->with('log_scraper_vs_ai')->where('stock', '>=', 1)->where('is_listing_rejected', 1)->orderBy('listing_rejected_on', 'DESC')->orderBy('updated_at', 'DESC')->paginate(25);

        $rejectedListingSummary = DB::table('products')->where('stock', '>=', 1)->selectRaw('DISTINCT(listing_remark) as remark, COUNT(listing_remark) as issue_count')->where('is_listing_rejected', 1)->groupBy('listing_remark')->orderBy('issue_count', 'DESC')->get();

        return view('products.rejected_listings', compact('products', 'reason', 'category_array', 'selected_categories', 'suppliers', 'supplier', 'request', 'users', 'rejectedListingSummary'));
    }

    public function updateProductListingStats(Request $request)
    {

        $product = Product::find($request->get('product_id'));
        if ($product) {
            $product->is_corrected = $request->get('is_corrected');
            $product->is_script_corrected = $request->get('is_script_corrected');
            $product->save();
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function deleteProduct(Request $request)
    {
        $product = Product::find($request->get('product_id'));

        if ($product) {
            $product->forceDelete();
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function relistProduct(Request $request)
    {
        $product = Product::find($request->get('product_id'));

        if ($product) {
            $product->is_listing_rejected = $request->get('rejected');
            $product->save();
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function productStats(Request $request)
    {
        $products = Product::orderBy('updated_at', 'DESC');

        if ($request->get('status') != '') {
            $status = $request->get('status') == 'approved' ? 1 : 0;
            $products = $products->where('is_approved', $status);
        }
        if ($request->has('user_id') >= 1) {
            $products = $products->where(function ($query) use ($request) {
                $query->where('approved_by', $request->get('user_id'))
                    ->orWhere('crop_approved_by', $request->get('user_id'))
                    ->orWhere('listing_rejected_by', $request->get('user_id'))
                    ->orWhere('crop_rejected_by', $request->get('user_id'))
                    ->orWhere('crop_ordered_by', $request->get('user_id'));
            });
        }
        $sku = '';

        if ($request->get('sku') != '') {
            $sku = $request->get('sku');
            $products = $products->where('sku', 'LIKE', "%$sku%");
        }

        if ($request->get('range_start') != '') {
            $products = $products->where(function ($query) use ($request) {
                $query->where('crop_approved_at', '>=', $request->get('range_start'))
                    ->orWhere('listing_approved_at', '>=', $request->get('range_start'))
                    ->orWhere('listing_rejected_on', '>=', $request->get('range_start'))
                    ->orWhere('crop_ordered_at', '>=', $request->get('range_start'))
                    ->orWhere('crop_rejected_at', '>=', $request->get('range_start'));
            });
        }
        if ($request->get('range_end') != '') {
            $products = $products->where(function ($query) use ($request) {
                $query->where('crop_approved_at', '<=', $request->get('range_end'))
                    ->orWhere('listing_approved_at', '<=', $request->get('range_end'))
                    ->orWhere('listing_rejected_on', '<=', $request->get('range_end'))
                    ->orWhere('crop_ordered_at', '<=', $request->get('range_end'))
                    ->orWhere('crop_rejected_at', '<=', $request->get('range_end'));
            });
        }

        $products = $products->paginate(50);
        $users = User::all();

        return view('products.stats', compact('products', 'sku', 'users', 'request'));
    }

    public function showSOP(Request $request)
    {
        $sopType = $request->get('type');
        $sop = Sop::where('name', $sopType)->first();

        if (!$sop) {
            $sop = new Sop();
            $sop->name = $request->get('type');
            $sop->content = '<p>Start Here...</p>';
            $sop->save();
        }

        return view('products.sop', compact('sop'));

    }

    public function saveSOP(Request $request)
    {
        $sopType = $request->get('type');
        $sop = Sop::where('name', $sopType)->first();

        if (!$sop) {
            $sop = new Sop();
            $sop->name = $request->get('type');
            $sop->save();
        }

        $sop->content = $request->get('content');
        $sop->save();

        return redirect()->back()->with('message', 'Updated successfully!');
    }

    public function getSupplierScrappingInfo(Request $request)
    {
        return View('scrap.supplier-info');
    }

    public function deleteImage()
    {
        $productId = request("product_id", 0);
        $mediaId = request("media_id", 0);
        $mediaType = request("media_type", "gallery");


        $cond = Db::table("mediables")->where([
            "media_id" => $mediaId,
            "mediable_id" => $productId,
            "tag" => $mediaType,
            "mediable_type" => "App\Product"
        ])->delete();

        if ($cond) {
            return response()->json(["code" => 1, "data" => []]);
        }

        return response()->json(["code" => 0, "data" => [], "message" => "No media found"]);

    }

    public function sendMessageSelectedCustomer(Request $request)
    {
        $customerIds = $request->get('customers_id', '');
        $customerIds = explode(',', $customerIds);
        $brand       = request()->get("brand",null);
        $category    = request()->get("category",null);
        $numberOfProduts = request()->get("number_of_products",10);
        $quick_sell_groups = request()->get("quick_sell_groups",[]);

        $product = new \App\Product;

        $toBeRun =  false;
        if(!empty($brand)) {
            $toBeRun =  true;
            $product = $product->where("brand",$brand);
        }

        if(!empty($category)) {
            $toBeRun =  true;
            $product = $product->where("category",$category);
        }

        if (!empty($quick_sell_groups)) {
            $toBeRun =  true;
            $product = $product->whereRaw("(products.id in (select product_id from product_quicksell_groups where quicksell_group_id in (". $quick_sell_groups.") ))");
        }

        $extraParams = [];

        if($toBeRun) {
            $limit = (!empty($numberOfProduts) && is_numeric($numberOfProduts)) ? $numberOfProduts : 10;
            $imagesQuery = $product->join("mediables as m","m.mediable_id", "products.id")->select("media_id")->groupBy("products.id")
            ->limit($limit)
            ->get()->pluck("media_id")->toArray();
            if(!empty($imagesQuery)) {
                $extraParams["images"] = json_encode(array_unique($imagesQuery));
            }
        }


        // get the status for approval
        $approveMessage = \App\Helpers\DevelopmentHelper::needToApproveMessage();

        $is_queue = 0;
        if ($approveMessage == 1) {
            $is_queue = 1;
        }

        foreach ($customerIds as $k => $customerId) {
            $requestData = new Request();
            $requestData->setMethod('POST');
            $params = $request->except(['_token', 'customers_id', 'return_url']);
            $params['customer_id'] = $customerId;
            $params['is_queue'] = $is_queue;
            $requestData->request->add($params + $extraParams);

            app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'customer');
        }

        if ($request->ajax()) {
            return response()->json(['msg' => 'success']);
        }

        if ($request->get('return_url')) {
            return redirect("/".$request->get('return_url'));
        }

        return redirect('/erp-leads');

    }

    public function queueCustomerAttachImages(Request $request)
    {
        $data['_token'] = $request->_token;
        $data['send_pdf'] = $request->send_pdf;
        $data['pdf_file_name'] = !empty($request->pdf_file_name) ? $request->pdf_file_name : "";
        $data['images'] = $request->images;
        $data['image'] = $request->image;
        $data['screenshot_path'] = $request->screenshot_path;
        $data['message'] = $request->message;
        $data['customer_id'] = $request->customer_id;
        $data['status'] = $request->status;

        \App\Jobs\AttachImagesSend::dispatch($data);

        $json = request()->get("json",false);

        if($json) {
            return response()->json(["code" => 200]);
        }

        if ($request->get('return_url')) {
            return redirect($request->get('return_url'));
        }

        return redirect()->route('customer.post.show',$request->customer_id)->withSuccess('Message Send For Queue');
    }

    public function hsCodeIndex(Request $request){

        if($request->category || $request->keyword){
            $products = Product::select('composition','category')->where('composition', 'LIKE', '%' . request('keyword') . '%')->where('category',$request->category[0])->groupBy('composition')->get();
          
           foreach ($products as $product) {

            if($product->category != null){
                $categoryTree = CategoryController::getCategoryTree($product->category);
               if(is_array($categoryTree)){
                
                    $childCategory = implode(' > ',$categoryTree);
               }
              
               $cat = Category::findOrFail($request->category[0]);
               $parentCategory = $cat->title;
               
               if($product->composition != null){
                    if($request->group == 'on'){
                        $composition = strip_tags($product->composition);
                        $compositions[] = str_replace(['&nbsp;','/span>'],' ',$composition);
                    }else{
                       if($product->isGroupExist($product->category,$product->composition,$parentCategory,$childCategory)){
                            $composition = strip_tags($product->composition);
                            $compositions[] = str_replace(['&nbsp;','/span>'],' ',$composition);

                        } 
                    }
                    
               }
              
            }
               
        }
        if(!isset($compositions)){
            $compositions = [];
            $childCategory = '';
            $parentCategory = '';
        }
         $keyword = $request->keyword;
         $groupSelected = $request->group;   
        
        }else{
            $keyword = '';
            $compositions = [];
            $childCategory = '';
            $parentCategory = '';
            $groupSelected = '';
        }
        $selected_categories = $request->category ? $request->category : 1;

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2','id' => 'category_value'])
            ->selected($selected_categories)
            ->renderAsDropdown();
        $hscodes = SimplyDutyCategory::all();    
        $categories = Category::all();
        $groups = HsCodeGroup::all();
        $cate = HsCodeGroupsCategoriesComposition::groupBy('category_id')->pluck('category_id')->toArray();
        $pendingCategory = Category::all()->except($cate);
        $pendingCategoryCount = $pendingCategory->count();
        $setting = HsCodeSetting::first();
        
        return view('products.hscode', compact('keyword','compositions','childCategory','parentCategory','category_selection','hscodes','categories','groups','groupSelected','pendingCategoryCount','setting'));
    }

    public function saveGroupHsCode(Request $request)
    {
        $name = $request->name;
        $compositions = $request->compositions; 
        $key = HsCodeSetting::first();
        $api = $key->key;

        $category = Category::select('id','title')->where('id',$request->category)->first();
        $categoryId = $category->id;


        $hscodeSearchString = str_replace(['&gt;','>'],'', $name.' '.$category->title.' '.$request->composition);

        $hscodeSearchString = urlencode($hscodeSearchString);

        $searchString = 'https://www.api.simplyduty.com/api/classification/get-hscode?APIKey='.$api.'&fullDescription='.$hscodeSearchString.'&originCountry=AE&destinationCountry=IN&getduty=false';
        
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $searchString);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch); 

        $categories = json_decode($output);
        
        if(!isset($categories->HSCode)){

            return response()->json(['error' => 'something wrong with api'], 400);

        }else{


            if($categories->HSCode != null){

                $hscode = new HsCode();
                $hscode->code = $categories->HSCode;
                $hscode->description = urldecode($hscodeSearchString);
                $hscode->save();

                if($request->existing_group != null){
                    $group = HsCodeGroup::find($request->existing_group);
                }else{
                    $group = new HsCodeGroup();
                    $group->hs_code_id = $hscode->id;
                    $group->name = $name.' > '.$category->title;
                    $group->composition = $request->composition;
                    $group->save();
                }
                
                $id = $group->id;

                foreach ($compositions as $composition) {
                    $comp = new HsCodeGroupsCategoriesComposition();
                    $comp->hs_code_group_id = $id;
                    $comp->category_id = $categoryId;
                    $comp->composition = $composition;
                    $comp->save();
                }
            }
        }
        

        return response()->json(['success' => 'success'], 200);
    }

    public function editGroup(Request $request)
    {

        $group = HsCodeGroup::find($request->id);
        $group->hs_code_id = $request->hscode;
        $group->name = $request->name;
        $group->composition = $request->composition;
        $group->save();

        return response()->json(['success' => 'success'], 200);
    }
}
