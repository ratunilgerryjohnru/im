<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Patient Details</h2>
                        <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            ← Back to Patients List
                        </a>
                    </div>

                    @if(isset($patient))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div class="border rounded-lg p-4">
                                <h3 class="text-lg font-semibold mb-4 text-blue-600">Personal Information</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Patient ID</label>
                                        <p class="text-lg font-semibold">{{ $patient->patient_id }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Full Name</label>
                                        <p class="text-lg">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Date of Birth</label>
                                        <p>{{ $patient->dob ? date('F j, Y', strtotime($patient->dob)) : 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Gender</label>
                                        <p>{{ $patient->sex ?? 'Not specified' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Marital Status</label>
                                        <p>{{ $patient->marital_status ?? 'Not specified' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="border rounded-lg p-4">
                                <h3 class="text-lg font-semibold mb-4 text-green-600">Contact Information</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Phone</label>
                                        <p>{{ $patient->phone ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Address</label>
                                        <p>{{ $patient->address ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Registered Date</label>
                                        <p>{{ $patient->date_registered ? date('F j, Y', strtotime($patient->date_registered)) : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-red-500">Patient not found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>