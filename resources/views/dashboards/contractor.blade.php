<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Contractor Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
        <!-- Sidebar Navigation -->
        <div class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 min-h-screen">
            <!-- Logo -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>
            </div>

            <!-- Menu -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <span class="text-gray-800 dark:text-gray-200 font-medium">Menu</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-2">
                <a href="{{ route('dashboard.contractor') }}" class="flex items-center px-4 py-3 text-gray-800 dark:text-gray-200 bg-gray-200 dark:bg-gray-700">
                    <span>Dashboard</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Client Database</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Billing & Payments</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Collection Schedules</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Disposal Schedules</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>SMS Manager</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Route Optimization</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>GPS Tracker</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Reports & Analytics</span>
                    <span class="ml-auto">&gt;</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Navigation -->
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4">
                <div class="flex justify-between items-center">
                    <div class="text-gray-800 dark:text-gray-200">
                        Home / Waste Contractor / Dashboard / {{ Auth::user()->name }}
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button class="text-gray-600 dark:text-gray-400 focus:outline-none">
                                <span class="sr-only">Notifications</span>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </button>
                        </div>
                        <div>
                            <button class="flex items-center text-gray-600 dark:text-gray-400 focus:outline-none">
                                <span class="sr-only">User Profile</span>
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-700">{{ substr(Auth::user()->name, 0, 1) }}</div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="p-6">
                <!-- System Performance Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">System Performance</h2>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Active Clients:</span>
                                <span class="ml-2 font-medium">24</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Total Routes:</span>
                                <span class="ml-2 font-medium">12</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Completed Jobs:</span>
                                <span class="ml-2 font-medium">36</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Routes Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Active Routes</h2>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Route</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clients</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap">Route A</td>
                                        <td class="px-4 py-3 whitespace-nowrap">15</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap">Route B</td>
                                        <td class="px-4 py-3 whitespace-nowrap">9</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Pending</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Performance Graph Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Performance</h2>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                        <div class="h-64 flex items-end justify-between space-x-2">
                            <div class="w-1/7 bg-blue-500 h-1/4 rounded-t"></div>
                            <div class="w-1/7 bg-blue-500 h-2/4 rounded-t"></div>
                            <div class="w-1/7 bg-blue-500 h-3/4 rounded-t"></div>
                            <div class="w-1/7 bg-blue-500 h-1/2 rounded-t"></div>
                            <div class="w-1/7 bg-blue-500 h-3/5 rounded-t"></div>
                            <div class="w-1/7 bg-blue-500 h-4/5 rounded-t"></div>
                            <div class="w-1/7 bg-blue-500 h-2/3 rounded-t"></div>
                        </div>
                        <div class="flex justify-between mt-2 text-xs text-gray-600 dark:text-gray-400">
                            <div>Mon</div>
                            <div>Tue</div>
                            <div>Wed</div>
                            <div>Thu</div>
                            <div>Fri</div>
                            <div>Sat</div>
                            <div>Sun</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>