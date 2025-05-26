<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
class ModalAlert extends Component
{
    public function render()
    {
        return view('livewire.modal-alert');
    }
    public function mount($open, $message, $buttonText ,$title){
        $this->open = $open;
        $this->message = $message;
        $this->title = $title;
        $this->buttonText = $buttonText;
    }
    public $open = "hide";

    public $message = "";

    public $title = "";

    public $buttonText = "";

    public function setOpen($open){
        $this->open = $open;
    }
    public function setMessage($message){
        $this->message = $message;
    }
    public function setTitle($title){
        $this->title = $title;
    }
    public function setButtonText($ButtonText){
        $this->buttonText = $ButtonText;
    }

}
