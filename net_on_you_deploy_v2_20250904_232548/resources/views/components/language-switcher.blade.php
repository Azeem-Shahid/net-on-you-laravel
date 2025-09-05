<div class="language-switcher" x-data="{ open: false }" class="relative">
    <button @click="open = !open" 
            class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-white hover:text-action transition-colors rounded-md hover:bg-action/10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
        </svg>
        <span>{{ strtoupper(getCurrentLanguage()) }}</span>
        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
        @foreach(getAvailableLanguages() as $code => $name)
            <a href="{{ route('language.switch', $code) }}" 
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors {{ getCurrentLanguage() === $code ? 'bg-action/10 text-action font-medium' : '' }}">
                <div class="flex items-center space-x-3">
                    <span class="w-6 h-4 rounded border border-gray-300 overflow-hidden">
                        <img src="https://flagcdn.com/{{ strtolower($code) }}.svg" alt="{{ $name }}" class="w-full h-full object-cover">
                    </span>
                    <span>{{ $name }}</span>
                    @if(getCurrentLanguage() === $code)
                        <svg class="w-4 h-4 text-action ml-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</div>

