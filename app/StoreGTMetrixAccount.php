<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class StoreGTMetrixAccount extends Model
{

    use Mediable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'store_gt_metrix_account';

    protected $fillable = [
        'email', 
        'password',
        'account_id',
        'status'
    ];

     protected $casts = [
        'resources' => 'array',
    ];
}
