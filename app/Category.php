<?php

namespace App;

use App\SupplierCategoryCount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Nestable\NestableTrait;


class Category extends Model
{

    CONST UNKNOWN_CATEGORIES = 143;

    use NestableTrait;

    protected $parent = 'parent_id';

    public $fillable = [ 'id','title', 'parent_id','status_after_autocrop','magento_id', 'show_all_id' ];

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function childs()
    {
        return $this->hasMany( __CLASS__, 'parent_id', 'id' );
    }

    public function parent()
    {
        return $this->hasOne( 'App\Category', 'id', 'parent_id' );
    }

    public static function isParent( $id )
    {

        $child_count = DB::table( 'categories as c' )
            ->where( 'parent_id', $id )
            ->count();

        return $child_count ? true : false;
    }


    public static function hasProducts( $id )
    {

        $products_count = DB::table( 'products as p' )
            ->where( 'category', $id )
            ->count();

        return $products_count ? true : false;

    }

     public static function getCategoryIdByKeyword( $keyword, $gender=null, $genderAlternative=null )
    {
        // Set gender
        if ( empty( $gender ) ) {
            $gender = $genderAlternative;
        }

        // Check database for result
        $dbResult = self::where( 'title', $keyword )->get();

        // No result? Try where like
        if ( $dbResult->count() == 0 ) {
            $dbResult = self::where( 'references', 'like', '%' . $keyword . '%' )->whereNotIn("id",[self::UNKNOWN_CATEGORIES,1])->get();
            $matchIds = [];
            foreach ($dbResult as $db) {
                if($db->references){
                    $referenceArrays = explode(',', $db->references);
                    foreach ($referenceArrays as $referenceArray) {
                        //reference 
                        $referenceArray = preg_replace('/\s+/', '', $referenceArray);
                        $referenceArray = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $referenceArray);

                        //category
                        $input = $keyword;
                        $input = preg_replace('/\s+/', '', $input);
                        $input = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $input);
                        similar_text(strtolower($input), strtolower($referenceArray), $percent);
                        if ($percent >= 80) {
                            $matchIds[] = $db->id;
                            break;
                        }
                    }
                }
            }
            $dbResult = self::whereIn('id',$matchIds)->get();
        }
        

        // Still no result
        if ( $dbResult === NULL ) {
            return 0;
        }

        // Just one result
        if ( $dbResult->count() == 1 ) {
            // Check if the category has subcategories
            $dbSubResult = Category::where('parent_id', $dbResult->first()->id)->first();
            // No results?
            if ( $dbSubResult == null ) {
                // Return
                return $dbResult->first()->id;
            }
        }

        // Checking the result by gender only works if the gender is set
        if ( empty( $gender ) ) {
            return 0;
        }

        // Check results
        foreach ( $dbResult as $result ) {
            // Get parent Id
            $parentId = $result->parent_id;

            // Return 0 for a top category
            if ( $parentId == 0 ) {
                return $result->id;
            }

            // Category directly under women? We don't want this - return 0
            if ( $parentId == 2 && strtolower( $gender ) == 'women' ) {
                return 0;
            }

            // Category directly under men? We don't want this - return 0
            if ( $parentId == 3 && strtolower( $gender ) == 'men' ) {
                return 0;
            }

            if ( $parentId == 146 && strtolower( $gender ) == 'kids' ) {
                return 0;
            }

            // Other
            if ( $parentId > 0 ) {
                // Store category ID
                $categoryId = $result->id;

                // Get parent
                $dbParentResult = Category::find( $result->parent_id );

                // No result
                if ( $dbParentResult->count() == 0 ) {
                    return 0;
                }

                // Return correct result for women
                if ( $dbParentResult->parent_id == 2 && strtolower( $gender ) == 'women' ) {
                    return $categoryId;
                }

                // Return correct result for men
                if ( $dbParentResult->parent_id == 3 && strtolower( $gender ) == 'men' ) {
                    return $categoryId;
                }

                // Return correct result for kids
                if ( $dbParentResult->parent_id == 146 && strtolower( $gender ) == 'kids' ) {
                    return $categoryId;
                }
            }
        }
    }

    public static function getCategoryPathById($categoryId = '')
    {
        // If we don't have an ID, return an empty string
        if (empty($categoryId)) {
            return '';
        }

        // Set empty category path
        $categoryPath = '';

        // Get category from database
        $category = Category::find($categoryId);

        // Do we have data?
        if ($category !== null) {
            // Set initial title
            $categoryPath = $category->title;

            // Loop while we haven't reached the top category
            while ($category && $category->parent_id > 0) {
                // Get next category from database
                $category = Category::find($category->parent_id);

                // Update category path
                if ($category !== null) {
                    $categoryPath = $category->title . ' > ' . $categoryPath;
                }
            }
        }

        // Return category path
        return $categoryPath;
    }

    public static function getCategoryTreeMagento( $id )
    {
        // Load new category model
        $category = new Category();

        // Create category instance
        $categoryInstance = $category->find( $id );

        // Set empty category tree for holding categories
        $categoryTree = [];

        // Continue only if category is not null
        if ( $categoryInstance !== NULL ) {

            // Load initial category
            $categoryTree[] = $categoryInstance->magento_id;

            // Set parent ID
            $parentId = $categoryInstance->parent_id;

            // Loop until we found the top category
            while ( $parentId != 0 ) {
                // find next category
                $categoryInstance = $category->find( $parentId );

                // Add category to tree
                $categoryTree[] = $categoryInstance->magento_id;

                // Add additional category to tree
                if ( !empty( $categoryInstance->show_all_id ) )
                    $categoryTree[] = $categoryInstance->show_all_id;

                // Set new parent ID
                $parentId = $categoryInstance->parent_id;
            }
        }

        // Return reverse array
        return array_reverse( $categoryTree );
    }

    public static function getCategoryTreeMagentoWithPosition( $id , $website , $needOrigin =  false)
    {

        $categoryMulti = StoreWebsiteCategory::where('category_id',$id)->where('store_website_id',$website->id)->first();
        // Load new category model
        $category = new Category();

        // Create category instance
        $categoryInstance = $category->find( $id );

        // Set empty category tree for holding categories
        $categoryTree = [];

        // Continue only if category is not null
        if ( $categoryInstance !== NULL ) {

            // Load initial category
            if($needOrigin) {
                $categoryTree[] =   ['position' => 1 , 'category_id' => $categoryMulti->remote_id,'org_id'=>$categoryMulti->category_id];
            }else{
                $categoryTree[] =   ['position' => 1 , 'category_id' => $categoryMulti->remote_id];
            }

            // Set parent ID
            $parentId = $categoryInstance->parent_id;

            // Loop until we found the top category
            while ( $parentId != 0 ) {
                // find next category
                $categoryInstance = $category->find( $parentId );

                $categoryMultiChild = StoreWebsiteCategory::where('category_id',$parentId)->where('store_website_id',$website->id)->first();
                if($categoryMultiChild){
                    if($categoryInstance->parent_id == 0){
                        if($needOrigin) {
                            $categoryTree[] = ['position' => 2, 'category_id' => $categoryMultiChild->remote_id,'org_id'=>$categoryMultiChild->category_id];
                        }else{
                            $categoryTree[] = ['position' => 2, 'category_id' => $categoryMultiChild->remote_id];
                        }
                    }else{
                        if($categoryInstance->parent_id == 0){
                            if($needOrigin) {
                                $categoryTree[] = ['position' => 3, 'category_id' => $categoryMultiChild->remote_id,'org_id'=>$categoryMultiChild->category_id];
                            }else{
                                $categoryTree[] = ['position' => 3, 'category_id' => $categoryMultiChild->remote_id];
                            }
                        }else{
                            if($needOrigin) {
                                $categoryTree[] = ['position' => 4, 'category_id' => $categoryMultiChild->remote_id,'org_id'=>$categoryMultiChild->category_id];
                            }else{
                                $categoryTree[] = ['position' => 4, 'category_id' => $categoryMultiChild->remote_id];
                            }
                        }
                    }
                }else{
                    // Add additional category to tree
                    /*if ( !empty( $categoryInstance->show_all_id ) )
                        $categoryTree[] = $categoryInstance->show_all_id;*/
                }

                // Set new parent ID
                $parentId = $categoryInstance->parent_id;
            }
        }

        // Return reverse array
        return array_reverse( $categoryTree );
    }

    public static function getCroppingGridImageByCategoryId($categoryId)
    {
        $imagesForGrid = [
            'Shoes' => 'shoes_grid.png',
            'Backpacks' => 'Backpack.png',
            'Bags' => 'Backpack.png',
            'Beach' => 'Backpack.png',
            'Travel' => 'Backpack.png',
            'Travel Bag' => 'Backpack.png',
            'Travel Bags' => 'Backpack.png',
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
            'Clutches & Slings' => 'Clutch.png',
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
            'Watches' => 'Keychains.png',
            'Watch' => 'Keychains.png',
        ];

        $category = Category::find($categoryId);
        if ( isset($category->title) ) {
            $catName = $category->title;

            if (array_key_exists($catName, $imagesForGrid)) {
                return $imagesForGrid[ $catName ];
            }

            if ($category->parent_id > 1) {
                $category = Category::find($category->parent_id);
                return $imagesForGrid[ trim($category->title) ] ?? '';
            }
        }

        return '';
    }

    public function suppliercategorycount(){
        return $this->hasOne(SupplierCategoryCount::class,'category_id','id');
    }

    public static function list()
    {
        return self::pluck("title","id")->toArray();
    }

}
