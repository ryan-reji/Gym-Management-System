<!-- header.php -->
<header class="ml-64 bg-white shadow-md p-4 flex justify-between items-center w-[calc(100vw-16rem)] relative">
    <h1 class="text-2xl font-bold">Dashboard</h1>
    
    <!-- Profile Dropdown -->
    <div class="relative">
        <!-- Profile Picture -->
        <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center text-white cursor-pointer" id="profileBtn">
            <span>👤</span>
        </div>

        <!-- Dropdown Menu -->
        <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-md hidden">
            <a href="trainer_details.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-200">Trainer Details</a>
        </div>
    </div>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const profileBtn = document.getElementById("profileBtn");
        const dropdown = document.getElementById("profileDropdown");

        profileBtn.addEventListener("click", function () {
            dropdown.classList.toggle("hidden");
        });

        document.addEventListener("click", function (event) {
            if (!profileBtn.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add("hidden");
            }
        });
    });
</script>
