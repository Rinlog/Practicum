@props(["options"=>"", "id"=>""])
<div class="mt-6 w-[90%] border-b-2 border-[#32a3cf] ">
    <select id="{{ $id }}" class="w-full pl-2">
        @foreach ($options as $option)
            <option class="bg-gray-500">{{ $option->sensor_type }}</option>
        @endforeach
    </select>
</div>  