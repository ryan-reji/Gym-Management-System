<!-- sidebar.php -->
<div class="w-64 bg-white shadow-md h-screen fixed top-0 left-0 flex flex-col">
    <div class="p-4 border-b">
        <!-- Logo section -->
        <div class="py-2 text-red-600 font-bold text-xl">GYM SHARK</div>
    </div>
    
    <!-- Navigation Menu - Scrollable -->
    <nav class="px-4 py-2 overflow-y-auto flex-1">
        <ul class="space-y-1">
            <li class="py-2">
                <div class="flex items-center text-gray-700">
                    <span class="mr-2 text-blue-500">▶</span>
                    <a href="dashboard.php" class="font-medium hover:text-blue-600">Home</a>
                </div>
            </li>
            <li class="py-2">
                <div class="flex items-center text-gray-700">
                    <span class="mr-2 text-blue-500">▶</span>
                    <a href="sessions.php" class="font-medium hover:text-blue-600">Sessions</a>
                </div>
            </li>
            <li class="py-2">
                <div class="flex items-center text-gray-700">
                    <span class="mr-2 text-blue-500">▶</span>
                    <a href="reschedule.php" class="font-medium hover:text-blue-600">Reschedule</a>
                </div>
            </li>
        </ul>
    </nav>
    
    <!-- Logout button at bottom -->
    <div class="p-4 border-t">
        <a href="logout.php" class="w-full py-2 px-4 bg-red-600 text-white rounded hover:bg-red-700 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7z" clip-rule="evenodd" />
                <path d="M4.293 8.293a1 1 0 011.414 0L8 10.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" />
            </svg>
            Logout
        </a>
    </div>
</div>
