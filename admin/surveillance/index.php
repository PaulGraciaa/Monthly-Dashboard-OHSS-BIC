<?php
$page_title = 'Surveillance Management';
require_once 'template_header.php';
?>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Surveillance Management Modules</h2>
            <p class="text-gray-600">Pilih modul yang ingin dikelola:</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Overall Performance Module -->
            <a href="overall_performance.php" class="group block bg-white rounded-xl shadow-sm p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="p-4 rounded-xl bg-green-100 text-green-600 shadow-sm group-hover:shadow group-hover:bg-green-200 transition-all duration-300">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mt-1">Overall Performance</h3>
                        <p class="text-sm text-gray-500 mt-1">Kelola data Surveillance System Overall Performance</p>
                    </div>
                </div>
            </a>

            <!-- Improvements Project Progress Module -->
            <a href="improvements_progress.php" class="group block bg-white rounded-xl shadow-sm p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="p-4 rounded-xl bg-blue-100 text-blue-600 shadow-sm group-hover:shadow group-hover:bg-blue-200 transition-all duration-300">
                        <i class="fas fa-project-diagram text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mt-1">Improvements Progress</h3>
                        <p class="text-sm text-gray-500 mt-1">Kelola data Improvements Project Progress</p>
                    </div>
                </div>
            </a>

                                    <!-- CCTV System Module -->
                        <a href="cctv_system.php" class="group block bg-white rounded-xl shadow-sm p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                            <div class="flex items-center space-x-4">
                                <div class="p-4 rounded-xl bg-purple-100 text-purple-600 shadow-sm group-hover:shadow group-hover:bg-purple-200 transition-all duration-300">
                                    <i class="fas fa-video text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 mt-1">CCTV System</h3>
                                    <p class="text-sm text-gray-500 mt-1">Kelola data CCTV System</p>
                                </div>
                            </div>
                        </a>

                        <!-- ISSS Software Module -->
            <a href="isss_software.php" class="group block bg-white rounded-xl shadow-sm p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                 <div class="flex items-center space-x-4">
                     <div class="p-4 rounded-xl bg-blue-100 text-blue-600 shadow-sm group-hover:shadow group-hover:bg-blue-200 transition-all duration-300">
                         <i class="fas fa-laptop text-2xl"></i>
                     </div>
                     <div>
                         <h3 class="text-lg font-bold text-gray-900 mt-1">ISSS Software</h3>
                         <p class="text-sm text-gray-500 mt-1">Kelola data ISSS Software Utilization</p>
                     </div>
                 </div>
             </a>

            <!-- Security Team Patrol Performance Module -->
            <a href="security_patrol.php" class="group block bg-white rounded-xl shadow-sm p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                 <div class="flex items-center space-x-4">
                     <div class="p-4 rounded-xl bg-green-100 text-green-600 shadow-sm group-hover:shadow group-hover:bg-green-200 transition-all duration-300">
                         <i class="fas fa-shield-alt text-2xl"></i>
                     </div>
                     <div>
                         <h3 class="text-lg font-bold text-gray-900 mt-1">Security Team Patrol</h3>
                         <p class="text-sm text-gray-500 mt-1">Kelola data Security Team Patrol Performance</p>
                     </div>
                 </div>
             </a>

            <!-- Security Team Performance on QR Scanned Module -->
            <a href="qr_scanned.php" class="group block bg-white rounded-xl shadow-sm p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                 <div class="flex items-center space-x-4">
                     <div class="p-4 rounded-xl bg-purple-100 text-purple-600 shadow-sm group-hover:shadow group-hover:bg-purple-200 transition-all duration-300">
                         <i class="fas fa-qrcode text-2xl"></i>
                     </div>
                     <div>
                         <h3 class="text-lg font-bold text-gray-900 mt-1">QR Scanned Performance</h3>
                         <p class="text-sm text-gray-500 mt-1">Kelola data Security Team Performance on QR Scanned</p>
                     </div>
                 </div>
             </a>

            <!-- Road Map CCTV & Surveillance Mapping Module -->
            <a href="roadmap_mapping.php" class="group block bg-white rounded-xl shadow-sm p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                 <div class="flex items-center space-x-4">
                     <div class="p-4 rounded-xl bg-indigo-100 text-indigo-600 shadow-sm group-hover:shadow group-hover:bg-indigo-200 transition-all duration-300">
                         <i class="fas fa-map-marked-alt text-2xl"></i>
                     </div>
                     <div>
                         <h3 class="text-lg font-bold text-gray-900 mt-1">Roadmap Mapping</h3>
                         <p class="text-sm text-gray-500 mt-1">Kelola data Road Map CCTV & Surveillance Mapping</p>
                     </div>
                 </div>
             </a>
        </div>

        <!-- Quick Stats -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php
                // Ambil statistik dari database
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM surveillance_overall_performance");
                $totalOverall = $stmt->fetch()['total'];
                
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM surveillance_improvements_progress");
                $totalImprovements = $stmt->fetch()['total'];
                
                $totalRecords = $totalOverall + $totalImprovements;
                
                $stmt = $pdo->query("SELECT COUNT(*) as completed FROM surveillance_overall_performance WHERE current_month LIKE '%100%'");
                $completedOverall = $stmt->fetch()['completed'];
                
                $stmt = $pdo->query("SELECT COUNT(*) as completed FROM surveillance_improvements_progress WHERE status = 'Done'");
                $completedImprovements = $stmt->fetch()['completed'];
                
                $completedRecords = $completedOverall + $completedImprovements;
                
                $stmt = $pdo->query("SELECT COUNT(*) as in_progress FROM surveillance_overall_performance WHERE current_month NOT LIKE '%100%' AND current_month != ''");
                $inProgressOverall = $stmt->fetch()['in_progress'];
                
                                            $stmt = $pdo->query("SELECT COUNT(*) as in_progress FROM surveillance_improvements_progress WHERE status IN ('In Progress', 'Pending')");
                            $inProgressImprovements = $stmt->fetch()['in_progress'];
                            
                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM surveillance_cctv_system");
                            $totalCCTV = $stmt->fetch()['total'];
                            
                            $stmt = $pdo->query("SELECT COUNT(*) as operational FROM surveillance_cctv_system WHERE readiness_percentage LIKE '%100%'");
                            $operationalCCTV = $stmt->fetch()['operational'];
                            
                                        $stmt = $pdo->query("SELECT COUNT(*) as total FROM surveillance_isss_software");
            $totalISSS = $stmt->fetch()['total'];

            $stmt = $pdo->query("SELECT COUNT(*) as completed FROM surveillance_isss_software WHERE jun != '' AND jun IS NOT NULL");
            $completedISSS = $stmt->fetch()['completed'];

            $stmt = $pdo->query("SELECT COUNT(*) as total FROM surveillance_security_patrol");
            $totalSecurityPatrol = $stmt->fetch()['total'];

            $stmt = $pdo->query("SELECT COUNT(*) as completed FROM surveillance_security_patrol WHERE jun != '' AND jun IS NOT NULL");
            $completedSecurityPatrol = $stmt->fetch()['completed'];

            $stmt = $pdo->query("SELECT COUNT(*) as total FROM surveillance_qr_scanned");
            $totalQRScanned = $stmt->fetch()['total'];

            $stmt = $pdo->query("SELECT COUNT(*) as completed FROM surveillance_qr_scanned WHERE jun != '' AND jun IS NOT NULL");
            $completedQRScanned = $stmt->fetch()['completed'];

            $stmt = $pdo->query("SELECT COUNT(*) as total FROM surveillance_roadmap_mapping");
            $totalRoadmapMapping = $stmt->fetch()['total'];

            $stmt = $pdo->query("SELECT COUNT(*) as completed FROM surveillance_roadmap_mapping WHERE status = 'Active'");
            $completedRoadmapMapping = $stmt->fetch()['completed'];

            $totalRecords = $totalOverall + $totalImprovements + $totalCCTV + $totalISSS + $totalSecurityPatrol + $totalQRScanned + $totalRoadmapMapping;
            $completedRecords = $completedOverall + $completedImprovements + $operationalCCTV + $completedISSS + $completedSecurityPatrol + $completedQRScanned + $completedRoadmapMapping;
            $inProgressRecords = $inProgressOverall + $inProgressImprovements;
                ?>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-database text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-600">Total Records</p>
                            <p class="text-lg font-semibold text-blue-900"><?php echo $totalRecords; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-600">Completed</p>
                            <p class="text-lg font-semibold text-green-900"><?php echo $completedRecords; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-600">In Progress</p>
                            <p class="text-lg font-semibold text-yellow-900"><?php echo $inProgressRecords; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'template_footer.php'; ?>
