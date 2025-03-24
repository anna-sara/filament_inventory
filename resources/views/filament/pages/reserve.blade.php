

<div id="reserve-form-container">
 <nav class="py-2 flex h-16 items-center align-items gap-x-4 bg-white px-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:px-6 lg:px-8">
    <img class=" w-auto h-full" src="/img/logo.png" alt="logo">
    <h1 class="fi-logo flex text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white">vBytes</h1>
 </nav>
 <div class="py-12 mx-auto h-full w-full px-4 md:px-6 lg:px-8 max-w-7xl">
    <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">Reservera spel</h1>
    <p class="py-4">Välkommen! Här kan du reservera spel som du gärna vill låna.</p>
 </div>
    <div class="mx-auto h-full w-full px-4 md:px-6 lg:px-8 max-w-7xl">
        {{ $this->table }}
    </div>
</div>
