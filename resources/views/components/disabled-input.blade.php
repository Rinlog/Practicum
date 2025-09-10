@props(["placeholder" => "", "id", "src"=>"", "type" => "text", 'customStyle' => ""])
<div>
    <input wire:model="{{ $src }}" id="{{ $id }}" type="{{$type}}" disabled placeholder={{ $placeholder }} class=" p-3 rounded-lg  w-full {{ $customStyle }}">
</div>