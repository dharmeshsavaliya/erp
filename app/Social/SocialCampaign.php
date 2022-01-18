<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;

class SocialCampaign extends Model
{
    /**
     * @var string
     * @SWG\Property(property="config_id",type="integer")
     * @SWG\Property(property="caption",type="string")
     * @SWG\Property(property="post_body",type="string")
     * @SWG\Property(property="post_by",type="string")
     * @SWG\Property(property="posted_on",type="datetime")
     * @SWG\Property(property="status",type="string")
     */

    use Mediable;

    protected $fillable = [
        'config_id',
        'caption',
        'post_body',
        'post_by',
        'posted_on',
        'status'
    ];
    public function account()
    {
        return $this->belongsTo('App\Social\SocialConfig');
    }
}
