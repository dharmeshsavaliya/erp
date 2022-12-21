<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class Password extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="website",type="strng")
     * @SWG\Property(property="url",type="string")
     * @SWG\Property(property="username",type="string")
     * @SWG\Property(property="password",type="string")
     * @SWG\Property(property="registered_with",type="string")
     */
    protected $fillable = [
        'website', 'url', 'username', 'password', 'registered_with',
    ];
}
