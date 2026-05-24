<x-app-layout>
    <div class="min-h-screen bg-gray-100">
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Hospital Name Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-black" style="color: #83D475;">WELMEADOWS HOSPITAL</h1>
                    <p class="text-gray-500 mt-1">Dashboard Overview</p>
                </div>

                <!-- SEARCH BAR -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label class="text-sm font-bold text-gray-700 mb-2 block">Search Patient</label>
                            <div class="flex gap-2">
                                <input type="text" id="searchInput"
                                    placeholder="Search by name, patient ID, or phone..."
                                    class="flex-1 form-input rounded-xl border-gray-200 px-4 py-3 focus:ring-2 focus:ring-[#83D475]">
                                <button onclick="searchPatient()"
                                    class="bg-[#83D475] text-white px-6 py-3 rounded-xl font-bold hover:opacity-90 transition">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <button onclick="clearSearch()"
                                    class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-bold hover:bg-gray-300 transition">
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Results -->
                <div id="searchResults" class="mb-8" style="display: none;">
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                        <div class="card-header bg-gradient-to-r from-blue-50 to-white p-4 border-b">
                            <h3 class="text-lg font-black text-gray-800">🔍 Search Results</h3>
                        </div>
                        <div id="resultsContent" class="p-6">
                            <!-- Results will be displayed here -->
                        </div>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #83D475;">
                        <p class="text-sm text-gray-500 uppercase tracking-wider">TOTAL PATIENTS</p>
                        <p class="text-3xl font-black text-gray-800 mt-2" id="statTotalPatients">0</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #3b82f6;">
                        <p class="text-sm text-gray-500 uppercase tracking-wider">ACTIVE ADMISSIONS</p>
                        <p class="text-3xl font-black text-gray-800 mt-2" id="statActiveAdmissions">0</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #f59e0b;">
                        <p class="text-sm text-gray-500 uppercase tracking-wider">OCCUPIED BEDS</p>
                        <p class="text-3xl font-black text-gray-800 mt-2" id="statOccupiedBeds">0</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #8b5cf6;">
                        <p class="text-sm text-gray-500 uppercase tracking-wider">MEDICAL RECORDS</p>
                        <p class="text-3xl font-black text-gray-800 mt-2" id="statMedicalRecords">0</p>
                    </div>
                </div>

                <!-- Patient Information Section (when a patient is selected) -->
                <div id="patientInfoSection" class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8"
                    style="display: none;">
                    <div class="card-header bg-gradient-to-r from-blue-50 to-white p-4 border-b">
                        <h3 class="text-lg font-black text-gray-800">👤 Patient Information</h3>
                    </div>
                    <div class="p-6" id="patientInfoContent">
                        <!-- Patient info will be displayed here -->
                    </div>
                </div>

                <!-- Medical Records Section -->
                <div id="medicalRecordsSection" class="bg-white rounded-2xl shadow-sm overflow-hidden"
                    style="display: none;">
                    <div class="card-header bg-gradient-to-r from-green-50 to-white p-4 border-b">
                        <h3 class="text-lg font-black text-gray-800">📋 Medical Records</h3>
                    </div>
                    <div class="p-6" id="medicalRecordsContent">
                        <!-- Medical records will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-input:focus {
            outline: none;
            ring: 2px solid #83D475;
        }

        .result-card {
            transition: all 0.3s ease;
        }

        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const supabaseUrl = '{{ env("SUPABASE_URL") }}';
        const supabaseKey = '{{ env("SUPABASE_KEY") }}';

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // Load dashboard stats
        async function loadStats() {
            try {
                const patientsRes = await fetch('/stats/total-patients');
                const patientsData = await patientsRes.json();
                document.getElementById('statTotalPatients').textContent = patientsData.count || 0;

                const admissionsRes = await fetch('/stats/active-admissions');
                const admissionsData = await admissionsRes.json();
                document.getElementById('statActiveAdmissions').textContent = admissionsData.count || 0;

                const bedsRes = await fetch('/stats/occupied-beds');
                const bedsData = await bedsRes.json();
                document.getElementById('statOccupiedBeds').textContent = bedsData.count || 0;

                const recordsRes = await fetch('/stats/medical-records');
                const recordsData = await recordsRes.json();
                document.getElementById('statMedicalRecords').textContent = recordsData.count || 0;
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Search patient using dedicated search endpoint
        async function searchPatient() {
            const searchTerm = document.getElementById('searchInput').value.trim();
            if (!searchTerm) {
                alert('Please enter a search term');
                return;
            }

            try {
                const response = await fetch(`/patients/search?q=${encodeURIComponent(searchTerm)}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const patients = await response.json();
                displaySearchResults(patients);

            } catch (error) {
                console.error('Error searching patients:', error);
                document.getElementById('searchResults').style.display = 'block';
                document.getElementById('resultsContent').innerHTML = '<div class="text-center py-8 text-red-500">Error searching patients. Please try again.</div>';
            }
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchResults').style.display = 'none';
            document.getElementById('patientInfoSection').style.display = 'none';
            document.getElementById('medicalRecordsSection').style.display = 'none';
        }

        function displaySearchResults(patients) {
            const resultsDiv = document.getElementById('searchResults');
            const contentDiv = document.getElementById('resultsContent');

            if (!patients || patients.length === 0) {
                resultsDiv.style.display = 'block';
                contentDiv.innerHTML = '<div class="text-center py-8 text-gray-500">No patients found matching your search.</div>';
                return;
            }

            resultsDiv.style.display = 'block';
            contentDiv.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    ${patients.map(patient => `
                        <div class="result-card bg-gray-50 rounded-xl p-4 cursor-pointer hover:bg-gray-100 transition border border-gray-200" onclick="loadPatientDetails(${patient.patient_id})">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-black text-gray-800">${escapeHtml(patient.first_name)} ${escapeHtml(patient.last_name)}</h4>
                                <span class="text-xs text-gray-500">ID: ${patient.patient_id}</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                <div>📞 ${escapeHtml(patient.phone) || 'No phone'}</div>
                                <div>🎂 ${patient.dob ? new Date(patient.dob).toLocaleDateString() : 'N/A'}</div>
                                <div>⚥ ${escapeHtml(patient.sex) || 'N/A'}</div>
                            </div>
                            <div class="mt-3">
                                <button onclick="event.stopPropagation(); loadPatientDetails(${patient.patient_id})" 
                                    class="w-full bg-[#83D475] text-white py-2 rounded-lg text-sm font-bold hover:opacity-90 transition">
                                    View Details
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        async function loadPatientDetails(patientId) {
            try {
                const response = await fetch(`/patients/${patientId}/full-details`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                displayPatientDetails(data);

            } catch (error) {
                console.error('Error loading patient details:', error);
                alert('Error loading patient details');
            }
        }

        function displayPatientDetails(data) {
            const patient = data.patient;
            const admission = data.current_admission;
            const medicalRecords = data.medical_records || [];

            // Patient Info Section
            const patientInfoDiv = document.getElementById('patientInfoSection');
            const patientInfoContent = document.getElementById('patientInfoContent');

            patientInfoDiv.style.display = 'block';

            let admissionHtml = '';
            if (admission && admission.bed_number) {
                admissionHtml = `
                    <div class="bg-blue-50 rounded-xl p-4 mb-4">
                        <h4 class="font-black text-blue-800 mb-3">🏥 Current Admission</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div><span class="font-bold">🛏️ Bed:</span> ${escapeHtml(admission.bed_number) || 'N/A'}</div>
                            <div><span class="font-bold">🏥 Ward:</span> ${escapeHtml(admission.ward_name) || 'N/A'}</div>
                            <div><span class="font-bold">📅 Admitted:</span> ${admission.date_admitted ? new Date(admission.date_admitted).toLocaleDateString() : 'N/A'}</div>
                            <div><span class="font-bold">🩺 Diagnosis:</span> ${escapeHtml(admission.primary_diagnosis) || 'N/A'}</div>
                            <div><span class="font-bold">💊 Condition:</span> ${escapeHtml(admission.condition) || 'Stable'}</div>
                        </div>
                    </div>
                `;
            } else {
                admissionHtml = `
                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <p class="text-gray-500 text-center">🏥 This patient is currently not admitted.</p>
                    </div>
                `;
            }

            patientInfoContent.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-black text-gray-800 mb-3">📋 Personal Information</h4>
                        <div class="space-y-2">
                            <div><span class="font-bold">Full Name:</span> ${escapeHtml(patient.first_name)} ${escapeHtml(patient.last_name)}</div>
                            <div><span class="font-bold">Patient ID:</span> ${patient.patient_id}</div>
                            <div><span class="font-bold">Date of Birth:</span> ${patient.dob ? new Date(patient.dob).toLocaleDateString() : 'N/A'}</div>
                            <div><span class="font-bold">Age:</span> ${calculateAge(patient.dob)}</div>
                            <div><span class="font-bold">Gender:</span> ${escapeHtml(patient.sex) || 'N/A'}</div>
                            <div><span class="font-bold">Phone:</span> ${escapeHtml(patient.phone) || 'N/A'}</div>
                            <div><span class="font-bold">Marital Status:</span> ${escapeHtml(patient.marital_status) || 'N/A'}</div>
                            <div><span class="font-bold">Address:</span> ${escapeHtml(patient.address) || 'N/A'}</div>
                            <div><span class="font-bold">Registered:</span> ${patient.date_registered ? new Date(patient.date_registered).toLocaleDateString() : 'N/A'}</div>
                        </div>
                    </div>
                    <div>
                        ${admissionHtml}
                    </div>
                </div>
            `;

            // Medical Records Section
            const medicalRecordsDiv = document.getElementById('medicalRecordsSection');
            const medicalRecordsContent = document.getElementById('medicalRecordsContent');

            if (medicalRecords && medicalRecords.length > 0) {
                medicalRecordsDiv.style.display = 'block';
                medicalRecordsContent.innerHTML = `
                    <div class="grid grid-cols-1 gap-4">
                        ${medicalRecords.map(record => `
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-xs text-gray-500">Record #${record.record_id}</span>
                                    <span class="text-xs text-gray-500">${record.created_date || new Date(record.created_at).toLocaleDateString()}</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div><span class="font-bold">🩸 Blood Type:</span> ${escapeHtml(record.blood_type) || 'Not recorded'}</div>
                                    <div><span class="font-bold">📋 Diagnosis:</span> ${escapeHtml(record.diagnosis) || 'Not recorded'}</div>
                                    <div><span class="font-bold">⚠️ Allergies:</span> ${escapeHtml(record.allergies) || 'None'}</div>
                                    <div><span class="font-bold">🏥 Chronic Conditions:</span> ${escapeHtml(record.chronic_conditions) || 'None'}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                medicalRecordsDiv.style.display = 'block';
                medicalRecordsContent.innerHTML = '<div class="text-center py-8 text-gray-500">No medical records found for this patient.</div>';
            }

            // Scroll to patient info
            patientInfoDiv.scrollIntoView({ behavior: 'smooth' });
        }

        function calculateAge(dob) {
            if (!dob) return 'N/A';
            const birthDate = new Date(dob);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age + ' years';
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            loadStats();
        });
    </script>
</x-app-layout>