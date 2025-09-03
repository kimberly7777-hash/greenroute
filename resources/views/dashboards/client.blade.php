<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Client Dashboard</title>

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
                <a href="{{ route('dashboard.client') }}" class="flex items-center px-4 py-3 text-gray-800 dark:text-gray-200 bg-gray-200 dark:bg-gray-700">
                    <span>Client Dashboard</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Profile</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Scheduler</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Invoices</span>
                    <span class="ml-auto">&gt;</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span>Support/Help</span>
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
                        Home / Client / Dashboard / {{ Auth::user()->name }}
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
                <!-- Welcome Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">WELCOME, {{ Auth::user()->name }}</h2>
                </div>

                <!-- Schedule Section -->
                <div class="mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">My Schedule</h3>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm text-gray-600 dark:text-gray-400">Waste collection: Monthly, Date: 15</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoices Section -->
                <div class="mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">My Invoices</h3>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium">Invoice #1001</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Amount: $250.00</div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Paid</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium">Invoice #1002</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Amount: $250.00</div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feedback Form -->
                <div class="mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Feedback Form</h3>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                            <form>
                                <div class="mb-4">
                                    <textarea class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none" rows="4" placeholder="Enter your feedback here..."></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>