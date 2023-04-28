<?php

namespace Modules\BookStack\Uploads;

use Illuminate\Support\Str;
use Modules\BookStack\Ownable;
use Modules\BookStack\Entities\Page;

class Attachment extends Ownable
{
    protected $fillable = ['name', 'order'];

    /**
     * Get the downloadable file name for this upload.
     *
     * @return mixed|string
     */
    public function getFileName()
    {
        if (Str::contains($this->name, '.')) {
            return $this->name;
        }

        return $this->name . '.' . $this->extension;
    }

    /**
     * Get the page this file was uploaded to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function page()
    {
        return $this->belongsTo(Page::class, 'uploaded_to');
    }

    /**
     * Get the url of this file.
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->external && strpos($this->path, 'http') !== 0) {
            return $this->path;
        }

        return url('/attachments/' . $this->id);
    }
}
