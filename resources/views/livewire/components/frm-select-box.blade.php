
<div class="mt-6 w-[90%] border-b-2 border-[#32a3cf] ">
    <label class="pl-2 text-lg">{{ $selectMessage }}</label>
    <select onchange="{{ $onChange }}" id="{{ $id }}" class="w-full pl-2">
        @foreach ($options as $option)
            <option class="bg-gray-500" id="{{ $option->{ $optionId } }}" >{{ $option->{$optionName} }}</option>
        @endforeach
    </select>
</div> 
