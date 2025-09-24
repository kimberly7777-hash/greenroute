<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-green-600">Welcome, {{ auth()->user()->name }}</h1>
                            <p class="text-gray-600">Manage your waste collection services</p>
                        </div>
                        <div class="text-right">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Notifications: 1</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- My Schedule -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-green-600 mb-4">My Schedule</h2>
                        <div class="space-y-3">
                            <div class="border-l-4 border-green-500 pl-4 py-2">
                                <p class="font-medium">Waste Collection</p>
                                <p class="text-sm text-gray-600">Next: Monday, January 20, 2025 - 9:00 AM</p>
                            </div>
                            <div class="border-l-4 border-yellow-500 pl-4 py-2">
                                <p class="font-medium">Recycling Collection</p>
                                <p class="text-sm text-gray-600">Next: Wednesday, January 22, 2025 - 10:00 AM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Invoices -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-green-600 mb-4">My Invoices</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium">Invoice #001</p>
                                    <p class="text-sm text-gray-600">Amount: $150.00</p>
                                </div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Paid</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium">Invoice #002</p>
                                    <p class="text-sm text-gray-600">Amount: $150.00</p>
                                </div>
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">Pending</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback Form & Help Center -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-green-600 mb-4">Feedback Form</h2>
                        <form>
                            <textarea class="w-full p-3 border border-gray-300 rounded-lg" rows="4" placeholder="Share your feedback..."></textarea>
                            <button type="submit" class="mt-3 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Submit Feedback</button>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-green-600 mb-4">Help Center</h2>
                        <div class="space-y-2">
                            <a href="#" class="block text-blue-600 hover:underline">How to schedule a pickup</a>
                            <a href="#" class="block text-blue-600 hover:underline">Payment methods</a>
                            <a href="#" class="block text-blue-600 hover:underline">Contact support</a>
                        </div>
                        <button class="mt-4 bg-white border border-green-600 text-green-600 px-4 py-2 rounded hover:bg-green-50">Policy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>