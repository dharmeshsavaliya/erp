<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\SiteDevelopmentCategory;
use App\User;

class MagentoFrontendDocumentation extends Model
{
    use HasFactory;

    public $table = 'magento_frontend_docs';


    public function storeWebsiteCategory()
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'store_website_category_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
