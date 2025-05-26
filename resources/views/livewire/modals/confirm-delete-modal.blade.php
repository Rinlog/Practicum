<!-- Main modal -->
<div id="DeleteModal" class="hide relative z-10 shadow-md transition-all duration-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div id="DeleteModalFrame" class="fixed inset-0 bg-gray-500/75 transition-all opacity-0 ease-in duration-200" aria-hidden="true"></div>
  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div id="DeleteModalMain" hasEventListener="false" class="transition-all opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 ease-in duration-200 relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl my-8 w-full max-w-sm">
        <!--Header-->
        <div class="bg-white px-4 pb-4 pt-2 flex justify-center items-center flex-col">
            <button wire:click="$js.closeDelete"id="closebutton" type="button" class="self-end text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
            </button>
            <h1 class="text-[#00719d]"><b>Delete</b></h1>
        </div>
        <!--Content-->
        <div class="flex flex-col items-center justify-center pb-2 text-[#737373] flex-wrap text-center">
          <h3 id="DeleteMessage" class="self-center"></h3>
          <h3><b id="ItemToDelete"></b></h3>
        </div>
        <!--Footer-->
        <div class="bg-gray-50 px-4 py-4 justify-center items-center flex">
          <x-bigbutton width="w-2/5" id="ConfirmDelete" text="CONFIRM" customStyle="text-lg font-semibold"></x-bigbutton>
        </div>
      </div>
    </div>
  </div>
</div>
@script
<script>
  if (0){}

  function CloseModal(){
    $("#DeleteModalFrame").addClass("opacity-0 ease-in duration-200");
    $("#DeleteModalFrame").removeClass("opacity-100 ease-out duration-300");
    $("#DeleteModalMain").addClass("opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 ease-in duration-200");
    $("#DeleteModalMain").removeClass("opacity-100 translate-y-0 sm:translate-y-0 sm:scale-95");
  }
  $js("closeDelete",function(){
    CloseModal();
    setTimeout(() => {
      $("#DeleteModal").addClass("hide");
    }, 200);
  })
</script>
@endscript