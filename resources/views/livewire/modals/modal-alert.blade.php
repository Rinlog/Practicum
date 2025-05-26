<!-- Main modal -->
<div id="AlertModal" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <!--
    Background backdrop, show/hide based on modal state.

    Entering: "ease-out duration-300"
      From: "opacity-0"
      To: "opacity-100"
    Leaving: "ease-in duration-200"
      From: "opacity-100"
      To: "opacity-0"
  -->
  @if ($open == "show")
    <div class="fixed inset-0 bg-gray-500/75 transition duration-200 fadein" aria-hidden="true"></div>
  @elseif ($open == "hide")
    <div class="fixed inset-0 bg-gray-500/75 transition duration-200 fadeout" aria-hidden="true"></div>
  @endif
  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
      <!--
        Modal panel, show/hide based on modal state.

        Entering: "ease-out duration-300"
          From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          To: "opacity-100 translate-y-0 sm:scale-100"
        Leaving: "ease-in duration-200"
          From: "opacity-100 translate-y-0 sm:scale-100"
          To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      -->
      @if ($open == "show")
        <div id="AlertModalMain" hasEventListener="false" class="fadein-full relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all my-8 w-full max-w-sm">
      @elseif ($open == "hide")
        <div id="AlertModalMain" hasEventListener="false" class="fadeout relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all my-8 w-full max-w-sm">
      @endif
        <!--Header-->
        <div class="bg-white px-4 pb-4 pt-2 flex justify-center items-center flex-col">
            <button wire:click="setOpen('hide')"id="closebutton" type="button" class="self-end text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
            </button>
            <h1 class="text-[#00719d]"><b>{{ $title }}</b></h1>
        </div>
        <!--Content-->
        <div class="flex justify-center pb-4">
        <h3>{{ $message }}</h3>
        </div>
        <!--Footer-->
        <div class="bg-gray-50 px-4 py-4 justify-center items-center flex">
          <x-bigbutton width="w-1/3" wireclick="setOpen('hide')" id="OKButton" text="{{ $buttonText }}"></x-bigbutton>
        </div>
      </div>
    </div>
  </div>
</div>