<?php

namespace App\Models;

use App\User;
use App\MagentoModule;
use App\PostmanRequestCreate;
use App\SiteDevelopmentCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoBackendDocumentation extends Model
{
    use HasFactory;

    public $table = 'magento_backend_docs';

    public function siteDevelopementCategory()
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'site_development_category_id', 'id');
    }

    public function postmamRequest()
    {
        return $this->belongsTo(PostmanRequestCreate::class, 'post_man_api_id', 'id');
    }

    public function magentoModule()
    {
        return $this->belongsTo(MagentoModule::class, 'mageneto_module_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
