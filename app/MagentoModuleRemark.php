<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoModuleRemark extends Model
{
     /**
     * @var string
     * @SWG\Property(property="magento_module_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="send_to",type="string")
     * @SWG\Property(property="remark",type="string")
     */
    protected $fillable = ['magento_module_id', 'user_id', 'send_to', 'remark'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function magento_module()
    {
        return $this->belongsTo(MagentoModule::class);
    }
}
