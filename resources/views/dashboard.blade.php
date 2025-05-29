@extends('layouts.app')

@section('head')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Dashboard</h1>
        <div class="d-flex gap-3">
            <div class="search-container">
                <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                <div id="searchResults" class="search-results"></div>
            </div>
            <button class="btn btn-primary" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-users" style="font-size: 2.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">Total Students</h5>
                            <h2 class="fw-bold mb-0" id="studentCount">{{ \App\Models\User::where('role', 'student')->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-chalkboard-teacher" style="font-size: 2.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">Total Teachers</h5>
                            <h2 class="fw-bold mb-0" id="teacherCount">{{ \App\Models\User::where('role', 'teacher')->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-book" style="font-size: 2.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">Total Courses</h5>
                            <h2 class="fw-bold mb-0" id="courseCount">{{ \App\Models\Course::count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-calendar-check" style="font-size: 2.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">Active Sessions</h5>
                            <h2 class="fw-bold mb-0" id="sessionCount">{{ \App\Models\Course::where('status', 'active')->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">User Roles Distribution</span>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary active" data-chart-type="pie">Pie</button>
                        <button class="btn btn-sm btn-outline-primary" data-chart-type="bar">Bar</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="rolesChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Recent Activities</span>
                    <button class="btn btn-sm btn-outline-primary" onclick="loadMoreActivities()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div id="activitiesList" class="list-group list-group-flush">
                        @foreach(\App\Models\Activity::latest()->take(5)->get() as $activity)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-circle text-{{ $activity->type }} me-2"></i>
                                        {{ $activity->description }}
                                    </div>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Course Statistics</span>
                    <select class="form-select form-select-sm w-auto" id="timeRange">
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <div class="card-body">
                    <div id="courseStatsChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Chart Configuration
let rolesChart;
const chartData = {
    labels: ['Students', 'Teachers', 'Principals'],
    datasets: [{
        data: [
            {{ \App\Models\User::where('role', 'student')->count() }},
            {{ \App\Models\User::where('role', 'teacher')->count() }},
            {{ \App\Models\User::where('role', 'principal')->count() }}
        ],
        backgroundColor: [
            'rgba(13, 110, 253, 0.8)',
            'rgba(25, 135, 84, 0.8)',
            'rgba(13, 202, 240, 0.8)'
        ],
        borderColor: [
            'rgba(13, 110, 253, 1)',
            'rgba(25, 135, 84, 1)',
            'rgba(13, 202, 240, 1)'
        ],
        borderWidth: 2
    }]
};

function initChart(type = 'pie') {
    const ctx = document.getElementById('rolesChart').getContext('2d');
    if (rolesChart) {
        rolesChart.destroy();
    }
    rolesChart = new Chart(ctx, {
        type: type,
        data: chartData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
}

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    initChart();
    initCourseStatsChart();
});

// Chart type toggle
document.querySelectorAll('[data-chart-type]').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('[data-chart-type]').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        initChart(this.dataset.chartType);
    });
});

// Course Statistics Chart
function initCourseStatsChart() {
    const options = {
        series: [{
            name: 'Enrollments',
            data: [30, 40, 35, 50, 49, 60, 70]
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#courseStatsChart"), options);
    chart.render();
}

// Search functionality
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');

searchInput.addEventListener('input', debounce(function(e) {
    const query = e.target.value;
    if (query.length < 2) {
        searchResults.style.display = 'none';
        return;
    }

    fetch(`/api/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            searchResults.innerHTML = '';
            data.forEach(result => {
                const div = document.createElement('div');
                div.className = 'search-result-item';
                div.innerHTML = `
                    <a href="${result.url}" class="d-flex align-items-center p-2">
                        <i class="fas ${result.icon} me-2"></i>
                        <div>
                            <div>${result.title}</div>
                            <small class="text-muted">${result.description}</small>
                        </div>
                    </a>
                `;
                searchResults.appendChild(div);
            });
            searchResults.style.display = 'block';
        });
}, 300));

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Refresh dashboard
function refreshDashboard() {
    showLoading();
    fetch('/api/dashboard/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('studentCount').textContent = data.students;
            document.getElementById('teacherCount').textContent = data.teachers;
            document.getElementById('courseCount').textContent = data.courses;
            document.getElementById('sessionCount').textContent = data.sessions;
            
            // Update chart data
            chartData.datasets[0].data = [data.students, data.teachers, data.principals];
            initChart(document.querySelector('[data-chart-type].active').dataset.chartType);
            
            hideLoading();
            toastr.success('Dashboard refreshed successfully');
        })
        .catch(error => {
            hideLoading();
            toastr.error('Failed to refresh dashboard');
        });
}

// Load more activities
function loadMoreActivities() {
    const activitiesList = document.getElementById('activitiesList');
    showLoading();
    
    fetch('/api/activities')
        .then(response => response.json())
        .then(data => {
            activitiesList.innerHTML = data.map(activity => `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-circle text-${activity.type} me-2"></i>
                            ${activity.description}
                        </div>
                        <small class="text-muted">${activity.time}</small>
                    </div>
                </div>
            `).join('');
            
            hideLoading();
            toastr.success('Activities updated');
        })
        .catch(error => {
            hideLoading();
            toastr.error('Failed to load activities');
        });
}

// Time range change handler
document.getElementById('timeRange').addEventListener('change', function(e) {
    showLoading();
    fetch(`/api/course-stats?range=${e.target.value}`)
        .then(response => response.json())
        .then(data => {
            // Update course stats chart
            chart.updateSeries([{
                data: data.enrollments
            }]);
            chart.updateOptions({
                xaxis: {
                    categories: data.dates
                }
            });
            hideLoading();
        })
        .catch(error => {
            hideLoading();
            toastr.error('Failed to update course statistics');
        });
});
</script>

<style>
.search-container {
    position: relative;
    width: 300px;
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--bs-body-bg);
    border: 1px solid var(--bs-border-color);
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    display: none;
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
}

.search-result-item a {
    text-decoration: none;
    color: var(--bs-body-color);
}

.search-result-item:hover {
    background: var(--bs-light);
}

.text-primary { color: var(--bs-primary) !important; }
.text-success { color: var(--bs-success) !important; }
.text-info { color: var(--bs-info) !important; }
.text-warning { color: var(--bs-warning) !important; }
</style>
@endsection 