@props(["text" => "Submit", "id", "wireclick" => "\$js.none", "width" => 'w-1/2', "customStyle" => ""])
<button wire:click="{{ $wireclick }}" class="bg-[#46c0e5] rounded-full {{ $width }} pt-2 pb-2 text-white hover cursor-pointer hover:bg-[#399ebd] {{ $customStyle }}" type="button" id="{{ $id }}">{{ $text }}</button>
