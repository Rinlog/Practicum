<?php

namespace App\Livewire\Modals;

use Livewire\Component;

class DisplayDetailsModal extends Component
{
    public $application = "false";
    public $apiPage = "false";

    public function render()
    {
        return view('livewire.modals.display-details-modal');
    }
}
