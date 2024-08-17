<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AlertaMaps extends Component
{
    public $type;
    public $message;
    public $visible;

    public function __construct($type = 'success', $message = '', $visible = true)
    {
        $this->type = $type;
        $this->message = $message;
        $this->visible = $visible;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.alerta-maps');
    }
}
