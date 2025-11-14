<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FolderSection extends Component
{
    public $provinces;

    /**
     * Create a new component instance.
     *
     * @param array $provinces
     * @param string|null $theme
     */
    public function __construct($provinces, $theme = null)
    {
        $this->provinces = $provinces;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.folder-section');
    }
}
