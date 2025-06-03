<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Taxonomy;

class TaxonomyOption extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $level;
    public function __construct(public Taxonomy $taxonomy, $level=0)
    {
        $this->level = $level+1;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.taxonomy-option', ['level'=>$this->level]);
    }
}
