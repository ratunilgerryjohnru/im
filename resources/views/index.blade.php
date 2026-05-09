<x-app-layout>
    <div class="py-12 bg-gray-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Module Header -->
            <div class="mb-6 inline-block bg-gray-300 px-6 py-2 shadow-sm">
                <h1 class="text-xl font-bold text-gray-700">Module 1</h1>
            </div>

            <!-- Main Container -->
            <div class="bg-white shadow-lg overflow-hidden rounded-lg">
                <!-- Banner Header -->
                <div class="bg-green-500 p-6">
                    <h2 class="text-2xl text-white">Patient Management System</h2>
                </div>

                <div class="p-8 bg-gray-100 space-y-8">
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white p-4 text-center shadow-sm border border-gray-200">
                            <p class="text-sm text-gray-600">Total Patients : 0</p>
                        </div>
                        <div class="bg-white p-4 text-center shadow-sm border border-gray-200">
                            <p class="text-sm text-gray-600">Active Admissions : 0</p>
                        </div>
                        <div class="bg-white p-4 text-center shadow-sm border border-gray-200">
                            <p class="text-sm text-gray-600">Occupied Beds : 0</p>
                        </div>
                        <div class="bg-white p-4 text-center shadow-sm border border-gray-200">
                            <p class="text-sm text-gray-600">Medical Records : 0</p>
                        </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <div class="flex space-x-2">
                        <button class="bg-green-400 text-white px-4 py-1 text-sm hover:bg-green-500">Register Patient</button>
                        <button class="bg-green-400 text-white px-8 py-1 text-sm hover:bg-green-500">Patients</button>
                        <button class="bg-green-400 text-white px-4 py-1 text-sm hover:bg-green-500">Medical Records</button>
                    </div>

                    <!-- Registration Form Section -->
                    <div class="bg-white p-6 border border-gray-200 shadow-sm">
                        <h3 class="text-gray-700 mb-4 border-b pb-2 font-bold">Patient Registration</h3>
                        <form action="#" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            @csrf
                            <div>
                                <label class="block text-xs mb-1">First Name *</label>
                                <input type="text" name="first_name" class="w-full border border-gray-400 p-1 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-xs mb-1">Date of Birth *</label>
                                <input type="date" name="dob" class="w-full border border-gray-400 p-1 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-xs mb-1">Phone Number *</label>
                                <input type="text" name="phone" class="w-full border border-gray-400 p-1 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-xs mb-1">Emergency Contact Name *</label>
                                <input type="text" name="e_name" class="w-full border border-gray-400 p-1 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-xs mb-1">Blood Group *</label>
                                <input type="text" name="blood_group" class="w-full border border-gray-400 p-1 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs mb-1">Last Name *</label>
                                <input type="text" name="last_name" class="w-full border border-gray-400 p-1 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-xs mb-1">Gender *</label>
                                <input type="text" name="gender" class="w-full border border-gray-400 p-1 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-xs mb-1">Email *</label>
                                <input type="email" name="email" class="w-full border border-gray-400 p-1 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-xs mb-1">Emergency Contact Number *</label>
                                <input type="text" name="e_phone" class="w-full border border-gray-400 p-1 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-xs mb-1">Allergies *</label>
                                <input type="text" name="allergies" class="w-full border border-gray-400 p-1 focus:ring-green-500">
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-xs mb-1">Address *</label>
                                <input type="text" name="address" class="w-full border border-gray-400 p-1 focus:ring-green-500">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-green-500 text-white py-2 shadow-md hover:bg-green-600 transition">Register Patient</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Registered Patients List -->
            <div class="mt-8 bg-white p-6 shadow-lg rounded-lg border border-gray-300">
                <h3 class="text-green-600 font-bold mb-4">Registered Patients</h3>
                <input type="text" placeholder="Search by name, ID, or phone..." class="w-full border border-gray-300 p-2 mb-4 italic text-sm rounded">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-800">
                                <th class="py-2 text-sm">Patient ID</th>
                                <th class="py-2 text-sm">Name</th>
                                <th class="py-2 text-sm">Age</th>
                                <th class="py-2 text-sm">Gender</th>
                                <th class="py-2 text-sm">Phone</th>
                                <th class="py-2 text-sm">Blood Group</th>
                                <th class="py-2 text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will go here -->
                            <tr>
                                <td colspan="7" class="py-4 text-center text-gray-500">No patients registered yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>