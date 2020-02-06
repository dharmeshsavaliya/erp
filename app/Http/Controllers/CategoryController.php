<?php

namespace App\Http\Controllers;

use App\Category;
use App\BrandCategoryPriceRange;
use Illuminate\Http\Request;

class CategoryController extends Controller
{


    public function __construct()
    {
       // $this->middleware( 'permission:category-edit', [ 'only' => [ 'addCategory', 'edit', 'manageCategory', 'remove' ] ] );
    }
    //

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function manageCategory( Request $request )
    {
        $categories = Category::where( 'parent_id', '=', 0 )->get();
        $allCategories = Category::pluck( 'title', 'id' )->all();

        $old = $request->old( 'parent_id' );

        $allCategoriesDropdown = Category::attr( [ 'name' => 'parent_id', 'class' => 'form-control' ] )
            ->selected( $old ? $old : 1 )
            ->renderAsDropdown();


        $allCategoriesDropdownEdit = Category::attr( [ 'name' => 'edit_cat', 'class' => 'form-control' ] )
            ->selected( $old ? $old : 1 )
            ->renderAsDropdown();

        return view( 'category.treeview', compact( 'categories', 'allCategories', 'allCategoriesDropdown', 'allCategoriesDropdownEdit' ) );
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function addCategory( Request $request )
    {
        $this->validate( $request, [
            'title' => 'required',
            'magento_id' => 'required|numeric',
            'show_all_id' => 'numeric|nullable',
        ] );
        $input = $request->all();
        $input[ 'parent_id' ] = empty( $input[ 'parent_id' ] ) ? 0 : $input[ 'parent_id' ];

        Category::create( $input );
        return back()->with( 'success', 'New Category added successfully.' );
    }

    public function edit( Category $category, Request $request )
    {

        $data = [];
        $data[ 'id' ] = $category->id;
        $data[ 'title' ] = $category->title;
        $data[ 'magento_id' ] = $category->magento_id;
        $data[ 'show_all_id' ] = $category->show_all_id;

        if ( $request->method() === 'POST' ) {
            $this->validate( $request, [
                'title' => 'required',
                'magento_id' => 'required|numeric',
                'show_all_id' => 'numeric|nullable',
            ] );

            $category->title = $request->input( 'title' );
            $category->magento_id = $request->input( 'magento_id' );
            $category->show_all_id = $request->input( 'show_all_id' );
            $category->save();

            return redirect()->route( 'category' )
                ->with( 'success-remove', 'Category updated successfully' );
        }

        return view( 'category.edit', $data );
    }

    public function remove( Request $request )
    {

        $category_instance = new Category();
        $category = $category_instance->find( $request->input( 'edit_cat' ) );

        if ( Category::isParent( $category->id ) )
            return back()->with( 'error-remove', 'Can\'t delete Parent category. Please delete all the childs first' );

        if ( Category::hasProducts( $category->id ) )
            return back()->with( 'error-remove', 'Can\'t delete category is associated with products. Please remove all the association first' );

        if ( $category->id == 1 )
            return back()->with( 'error-remove', 'Can\'t be delete' );

        $title = $category->title;
        $category->delete();

        return back()->with( 'success-remove', $title . ' category Deleted' );
    }

    public static function getCategoryTree( $id )
    {

        $category = new Category();
        $category_instance = $category->find( $id );
        $categoryTree = [];
        
        if($category_instance == null){
            return false;
        }

        $parent_id = $category_instance->parent_id;

        while ( $parent_id != 0 ) {

            $category_instance = $category->find( $parent_id );
            $categoryTree[] = $category_instance->title;
            $parent_id = $category_instance->parent_id;
        }

        return array_reverse( $categoryTree );
    }

    public static function brandMinMaxPricing()
    {
        // Get all data
        $results = \Illuminate\Support\Facades\DB::select( "
            SELECT
                categories.title,
                categories.id as cat_id,
                MIN(price*1) AS minimumPrice,
                MAX(price*1) AS maximumPrice
            FROM
                products
            JOIN
                categories
            ON
                products.category=categories.id
            GROUP BY
                products.category
            ORDER BY
                categories.title
        " );

        // Get all form data
        $resultsBrandCategoryPriceRange = BrandCategoryPriceRange::all();

        // Create array with brand segments
        $brandSegments = ['A', 'B', 'C'];

        // Create empty array
        $formResults = [];

        // Loop over results
        foreach ( $resultsBrandCategoryPriceRange as $result ) {
            $formResults[ $result->brand_segment ][ $result->category_id ][ 'min' ] = $result->min_price;
            $formResults[ $result->brand_segment ][ $result->category_id ][ 'max' ] = $result->max_price;
        }

        return view( 'category.minmaxpricing', compact( 'results', 'brandSegments', 'formResults' ) );
    }

    public static function updateBrandMinMaxPricing( Request $request )
    {
        // Check minimum price first
        if ( $request->ajax() && $request->type == 'min' && (int) $request->price > 0 ) {
            return BrandCategoryPriceRange::updateOrCreate(
                [ 'brand_segment' => $request->brand_segment, 'category_id' => $request->category_id ],
                [ 'min_price' => $request->price ]
            );
        }

        // Check minimum price first
        if ( $request->ajax() && $request->type == 'max' && (int) $request->price > 0 ) {
            return BrandCategoryPriceRange::updateOrCreate(
                [ 'brand_segment' => $request->brand_segment, 'category_id' => $request->category_id ],
                [ 'max_price' => $request->price ]
            );
        }
    }

    public static function getCategoryTreeMagentoIds( $id )
    {

        $category = new Category();
        $category_instance = $category->find( $id );
        $categoryTree = [];

        $categoryTree[] = $category_instance->magento_id;
        $parent_id = $category_instance->parent_id;

        while ( $parent_id != 0 ) {

            $category_instance = $category->find( $parent_id );
            $categoryTree[] = $category_instance->magento_id;

            if ( !empty( $category_instance->show_all_id ) )
                $categoryTree[] = $category_instance->show_all_id;

            $parent_id = $category_instance->parent_id;
        }

        //Adding root category.
//		array_push($categoryTree,'2');

        return array_reverse( $categoryTree );
    }

    public static function getCategoryIdByName( $term )
    {
        $category = Category::where( 'title', '=', $term )->first();

        return $category ? $category->id : 0;
    }

    public function mapCategory()
    {
        $fillerCategories = Category::where( 'id', '>', 1 )->where('parent_id', 0)->whereIn('id', [143,144])->get();

        $categories = Category::where( 'id', '>', 1 )->where('parent_id', 0)->whereNotIn('id', [143,144])->get();

        $allStatus = ["" => "N/A"] + \App\Helpers\StatusHelper::getStatus();

        $allCategoriesDropdown = Category::attr([ 'name' => 'new_cat_id', 'class' => 'form-control' , 'style' => "width:100%"])->renderAsDropdown();

        return view( 'category.references', compact( 'fillerCategories', 'categories','allStatus','allCategoriesDropdown') );
    }

    public function saveReferences( Request $request )
    {

        $categories = $request->get( 'category' );
        $info = $request->get( 'info' );

        if(!empty($info)) {
            foreach ( $info as $catId => $reference ) {
                list($catId,$reference) = explode("#",$reference);
                $catId = str_replace("cat_", "", $catId);
                $category = Category::find( $catId );
                $category->references = $reference;
                $category->save();
            }

        }else{
            foreach ( $categories as $catId => $reference ) {
                $catId = str_replace("cat_", "", $catId);
                $category = Category::find( $catId );
                $category->references = implode( ',', $reference );
                $category->save();
            }
        }



        //if(request()->is("ajax")) {
            return response()->json(["code" => 200]);
        //}    

        return redirect()->back()->with( 'message', 'Category updated successfully!' );
    }

    public function saveReference(Request $request)
    {
        $oldCatId = $request->get("old_cat_id");
        $newcatId = $request->get("new_cat_id");
        $catName  = strtolower($request->get("cat_name"));

        // assigned new category
        $newCategory = null;
        $oldCategory = null;

        // checking category id
        if(!empty($oldCatId) && !empty($newcatId))  {
            $oldCategory = Category::find($oldCatId);
            if(!empty($oldCategory)) {
                $catArray = explode(",",$oldCategory->references);
                $catArray = array_map('strtolower', $catArray);

                // check matched array we got
                $findMe = array_search($catName, $catArray);
                if($findMe !== false) {
                    unset($catArray[$findMe]);
                }

                // update new category
                $oldCategory->references = implode(",", array_unique(array_filter($catArray)));
                $oldCategory->save();
            }
            // update with new category id
            $newCategory = Category::find($newcatId);

            if($newCategory)  {
                
                $newCatArr = explode(",", $newCategory->references);
                $newCatArr = array_map('strtolower', $newCatArr);
                $newCatArr[] = strtolower($catName);
                $newCategory->references = implode(",", array_unique(array_filter($newCatArr)));
                $newCategory->save();

                // once we have new category id then we need to update all product from that old category
                $products = \App\Product::where("category",$oldCatId)->select(["products.id","products.sku"])->get();
                if(!$products->isEmpty()) {
                    foreach($products as $product) {
                        $scraped_products = $product->many_scraped_products;
                        if(!$scraped_products->isEmpty()) {
                            foreach($scraped_products as $scraped_product) {
                                if(isset($scraped_product->properties['category'])) {
                                    if(is_array($scraped_product->properties['category'])) {
                                        $namesList = array_map('strtolower', $scraped_product->properties['category']);
                                        if(in_array(strtolower($catName), $namesList)) {
                                            $product->category = $newcatId;
                                            $product->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

        return response()->json(["code" => 200 , "data" => $newCategory]);

    }

    public function updateField(Request $request) 
    {
        $id = $request->get("id");
        $field = $request->get("_f");
        $value = $request->get("_v");

        $category = Category::where("id" , $id)->first();
        if($category) {
            $category->{$field} = $value;
            $category->save();

            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500]);

    }
}
