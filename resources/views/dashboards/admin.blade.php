<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Administrator Dashboard</title>

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
                <a href="{{ route('dashboard.admin') }}" class="flex items-center px-4 py-3 text-gray-800 dark:text-gray-200 bg-gray-200 dark:bg-gray-700">
                    <span>Administrator Dashboard</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Verification</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Client Information</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Billing & Payments</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Schedules</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Users</span>
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
                        Home / Administrator / Dashboard / {{ Auth::user()->name }}
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
                                <span class="text-gray-600 dark:text-gray-400">Contractors:</span>
                                <span class="ml-2 font-medium">12</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Clients:</span>
                                <span class="ml-2 font-medium">48</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Active Routes:</span>
                                <span class="ml-2 font-medium">24</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Tasks Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Pending Tasks</h2>
                    <div class="space-y-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Verify Contractor:</span>
                                    <span class="ml-2 font-medium">3</span>
                                </div>
                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm">View</button>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Update Route:</span>
                                    <span class="ml-2 font-medium">5</span>
                                </div>
                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm">View</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>