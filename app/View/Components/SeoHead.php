<?php

namespace App\View\Components;

use App\Settings\SeoSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class SeoHead extends Component
{
    public string $title;
    public string $description;
    public ?string $image;
    public string $robots;
    public string $url;

    public function __construct(
        SeoSettings $settings,
        public ?Model $model = null,
        ?string $title = null
    ) {
        // 1. Resolve SEO Object from Model
        // We load 'seo' relationship to avoid N+1 if not loaded, though here it's single model.
        $seo = $model?->seo;

        // 2. Resolve Title
        $metaTitle = $seo?->title;
        // Event/Page uses 'title'.
        $modelTitle = $model?->title;
        $globalTitle = $settings->site_name;

        if ($metaTitle) {
            $this->title = $metaTitle . ' ' . $settings->title_separator . ' ' . $settings->title_suffix;
        } elseif ($modelTitle) {
            $this->title = $modelTitle . ' ' . $settings->title_separator . ' ' . $settings->title_suffix;
        } elseif ($title) {
            $this->title = $title;
        } else {
            $this->title = $globalTitle;
        }

        // 3. Resolve Description
        $metaDesc = $seo?->description;
        $modelDesc = null;
        if ($model) {
            if (isset($model->perex)) {
                $modelDesc = $model->perex;
            }
            // For Page, content is JSON builder, we skip parsing it for now.
        }

        $this->description = $metaDesc ?? $modelDesc ?? $settings->site_description ?? '';

        // 4. Resolve Image
        $metaImage = $seo?->image;
        $modelImage = $model?->image_path;

        $this->image = $metaImage ?? $modelImage ?? $settings->site_image;

        // Ensure full URL for image
        if ($this->image && !str_starts_with($this->image, 'http')) {
            $this->image = asset('storage/' . $this->image);
        }

        // 5. Resolve Robots
        $this->robots = $seo?->robots ?? $settings->robots_default;

        // 6. URL
        $this->url = url()->current();
    }

    public function render(): View|Closure|string
    {
        return view('components.seo-head');
    }
}
