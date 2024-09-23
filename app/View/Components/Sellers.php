<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sellers extends Component
{
    public $sellers;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $user = User::find(session()->get('auth'));
        $this->sellers = $user->sellers;
        if (!session()->has('sellerId')) {
            foreach ($this->sellers as $seller) {
                session()->put('sellerId', $seller->id);
                session()->put('sellerName', $seller->name);
                break;
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sellers');
    }
}
