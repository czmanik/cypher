<?php

namespace App\Traits;

use App\Models\SeoDetail;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeo
{
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoDetail::class, 'seoable');
    }
}
