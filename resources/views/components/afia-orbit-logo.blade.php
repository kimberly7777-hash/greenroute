@props(['class' => 'h-12 w-auto'])

<div class="flex items-center {{ $class }}">
    <!-- Logo Icon -->
    <div class="relative">
        <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-full flex items-center justify-center shadow-lg">
            <!-- Stylized Y pattern -->
            <div class="relative w-6 h-6">
                <!-- Main vertical line -->
                <div class="absolute left-1/2 top-0 w-0.5 h-4 bg-white transform -translate-x-1/2"></div>
                <!-- Left branch -->
                <div class="absolute left-1/2 top-2 w-2 h-0.5 bg-white transform -translate-x-1/2 -rotate-45 origin-left"></div>
                <!-- Right branch -->
                <div class="absolute left-1/2 top-2 w-2 h-0.5 bg-white transform -translate-x-1/2 rotate-45 origin-right"></div>
                <!-- Bottom right branch -->
                <div class="absolute left-1/2 top-3 w-1.5 h-0.5 bg-white transform -translate-x-1/2 rotate-12 origin-right"></div>
                <!-- Red accent dot -->
                <div class="absolute left-1 top-1 w-1 h-1 bg-red-500 rounded-full"></div>
            </div>
        </div>
    </div>
</div>
