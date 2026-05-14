<x-app-layout>
    <div class="bg-gray-100 min-h-screen">
        <!-- Green Top Bar - Matching Slide 3 -->
        <div class="bg-green-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h1 class="text-2xl font-bold">WELMEADOWS</h1>
                            <p class="text-sm text-green-100">Patient Management System</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div class="flex space-x-4">
                            <a href="{{ route('dashboard') }}" class="bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="{{ route('patients.index') }}" class="text-white hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium">Patients</a>
                            <a href="#" onclick="loadMedicalRecords()" class="text-white hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium">Medical Records</a>
                            <a href="#" class="text-white hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium">Appointments</a>
                            <a href="#" class="text-white hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium">Reports</a>
                            <a href="#" class="text-white hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium">Settings</a>
                        </div>
                        <div class="flex items-center space-x-2 border-l border-green-500 pl-4">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span class="text-sm font-medium">admin</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-green-200 hover:text-white ml-2">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub Header -->
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
                    <p class="text-sm text-gray-500">Overview of hospital statistics and ward occupancy</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-green-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Total Patients</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1" id="totalPatients">0</p>
                        </div>
                        <i class="fas fa-users text-3xl text-green-300"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Active Admissions</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1" id="activeAdmissions">0</p>
                        </div>
                        <i class="fas fa-procedures text-3xl text-blue-300"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-yellow-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Occupied Beds</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1" id="occupiedBeds">0</p>
                        </div>
                        <i class="fas fa-bed text-3xl text-yellow-300"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-purple-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Medical Records</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1" id="medicalRecords">0</p>
                        </div>
                        <i class="fas fa-file-medical text-3xl text-purple-300"></i>
                    </div>
                </div>
            </div>

            <!-- Critical Alert - Matching Slide 3 -->
            <div class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-xl mr-3"></i>
                    <div>
                        <p class="font-medium text-orange-800">Critical Occupancy Alert</p>
                        <p class="text-sm text-orange-700">7 ward(s) are at or above 90% occupancy. Please review bed allocation.</p>
                    </div>
                </div>
            </div>

            <!-- Ward Overview Section -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ward Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Isolation Ward -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-red-600 px-4 py-3 flex justify-between items-center">
                        <div>
                            <i class="fas fa-shield-alt text-white mr-2"></i>
                            <span class="text-white font-semibold">Isolation Ward</span>
                        </div>
                        <span class="bg-red-800 text-white text-xs px-2 py-1 rounded">CRITICAL</span>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-3 text-center mb-3">
                            <div>
                                <p class="text-2xl font-bold text-gray-800">12</p>
                                <p class="text-xs text-gray-500">TOTAL BEDS</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800">10</p>
                                <p class="text-xs text-gray-500">OCCUPIED</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800">2</p>
                                <p class="text-xs text-gray-500">VACANT</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 83%"></div>
                        </div>
                        <p class="text-center text-sm text-red-600 font-medium">Occupancy Rate: 83%</p>
                        <div class="flex justify-center space-x-2 mt-3 pt-3 border-t">
                            <button class="text-green-600 text-sm hover:text-green-800">View Beds</button>
                            <button class="text-gray-500 text-sm hover:text-gray-700">Settings</button>
                        </div>
                    </div>
                </div>

                <!-- Medical Ward -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-red-600 px-4 py-3 flex justify-between items-center">
                        <div>
                            <i class="fas fa-stethoscope text-white mr-2"></i>
                            <span class="text-white font-semibold">Medical Ward</span>
                        </div>
                        <span class="bg-red-800 text-white text-xs px-2 py-1 rounded">CRITICAL</span>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-3 text-center mb-3">
                            <div>
                                <p class="text-2xl font-bold text-gray-800">20</p>
                                <p class="text-xs text-gray-500">TOTAL BEDS</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800">18</p>
                                <p class="text-xs text-gray-500">OCCUPIED</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800">2</p>
                                <p class="text-xs text-gray-500">VACANT</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 90%"></div>
                        </div>
                        <p class="text-center text-sm text-red-600 font-medium">Occupancy Rate: 90%</p>
                        <div class="flex justify-center space-x-2 mt-3 pt-3 border-t">
                            <button class="text-green-600 text-sm hover:text-green-800">View Beds</button>
                            <button class="text-gray-500 text-sm hover:text-gray-700">Settings</button>
                        </div>
                    </div>
                </div>

                <!-- Pediatric Ward -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-red-600 px-4 py-3 flex justify-between items-center">
                        <div>
                            <i class="fas fa-child text-white mr-2"></i>
                            <span class="text-white font-semibold">Pediatric Ward</span>
                        </div>
                        <span class="bg-red-800 text-white text-xs px-2 py-1 rounded">CRITICAL</span>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-3 text-center mb-3">
                            <div>
                                <p class="text-2xl font-bold text-gray-800">15</p>
                                <p class="text-xs text-gray-500">TOTAL BEDS</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800">14</p>
                                <p class="text-xs text-gray-500">OCCUPIED</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800">1</p>
                                <p class="text-xs text-gray-500">VACANT</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 93%"></div>
                        </div>
                        <p class="text-center text-sm text-red-600 font-medium">Occupancy Rate: 93%</p>
                        <div class="flex justify-center space-x-2 mt-3 pt-3 border-t">
                            <button class="text-green-600 text-sm hover:text-green-800">View Beds</button>
                            <button class="text-gray-500 text-sm hover:text-gray-700">Settings</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Records Modal -->
    <div class="modal fade" id="medicalRecordsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white">
                    <h5 class="modal-title"><i class="fas fa-notes-medical"></i> Medical Records</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="medicalRecordsContent">
                    Loading...
                </div>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a54d2cbf95.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';

        function loadMedicalRecords() {
            $('#medicalRecordsModal').modal('show');
            $.ajax({
                url: '/medical-records',
                method: 'GET',
                success: function(records) {
                    let html = '<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Patient</th><th>Record Type</th><th>Date</th><th>Doctor</th><th>Description</th></tr></thead><tbody>';
                    records.forEach(record => {
                        html += `<tr>
                            <td>${record.patient ? record.patient.first_name + ' ' + record.patient.last_name : 'N/A'}</td>
                            <td><span class="badge bg-info">${record.record_type}</span></td>
                            <td>${record.record_date}</td>
                            <td>${record.recorded_by || '-'}</td>
                            <td>${record.description ? record.description.substring(0, 50) + '...' : '-'}</td>
                        </tr>`;
                    });
                    html += '</tbody></table></div>';
                    $('#medicalRecordsContent').html(html);
                },
                error: function() {
                    $('#medicalRecordsContent').html('<div class="alert alert-danger">Error loading records</div>');
                }
            });
        }

        // Fetch statistics
        async function fetchStats() {
            try {
                const response = await fetch('/stats');
                const stats = await response.json();
                document.getElementById('totalPatients').textContent = stats.total_patients || 0;
                document.getElementById('activeAdmissions').textContent = stats.active_admissions || 0;
                document.getElementById('occupiedBeds').textContent = stats.occupied_beds || 0;
                document.getElementById('medicalRecords').textContent = stats.medical_records || 0;
            } catch (error) {
                console.error('Error fetching stats:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetchStats();
        });
    </script>
</x-app-layout>