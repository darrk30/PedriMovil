<?php

namespace App\Livewire;
use App\Livewire\Actions\Logout;
use Livewire\Component;

class NavigationMenu extends Component
{
    public $isOpen = false;

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function toggleMenu()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        return view('livewire.navigation-menu');
    }
}
