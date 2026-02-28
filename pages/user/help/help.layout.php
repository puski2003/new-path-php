<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/../common/user.html.head.php'; ?>

<body>
    <main class="main-container">
        <?php $activePage = 'help-center';
        require_once __DIR__ . '/../common/user.sidebar.php'; ?>

        <section class="main-content">
            <img src="/assets/img/main-content-head.svg"
                alt="Main Content Head background"
                class="main-header-bg-image" />

            <div class="main-content-header">
                <div class="main-content-header-text">
                    <h2>Help Center</h2>
                    <p>Find support and get answers to your questions.</p>
                </div>

                <div style="width: 25%"></div>
                <img src="/assets/img/recovery-head.svg"
                    alt="Help Center"
                    class="help-image" />
            </div>

            <div class="main-content-body">
                <div class="help-container">
                    <div class="help-search-section">
                        <div class="search-container">
                            <div class="search-bar">
                                <i data-lucide="search" class="search-icon" stroke-width="1"></i>
                                <input type="text" class="search-input" placeholder="Search for topics or services..." id="helpSearch">
                            </div>
                            <div class="filter-controls">
                                <select class="filter-dropdown" id="categoryFilter">
                                    <option value="all">All Categories</option>
                                    <option value="emergency">Emergency</option>
                                    <option value="counseling">Counseling</option>
                                    <option value="recovery">Recovery Plans</option>
                                    <option value="community">Community</option>
                                    <option value="technical">Technical Support</option>
                                </select>
                                <select class="filter-dropdown" id="typeFilter">
                                    <option value="all">All Types</option>
                                    <option value="hotline">Hotlines</option>
                                    <option value="chat">Live Chat</option>
                                    <option value="appointment">Appointments</option>
                                    <option value="resources">Resources</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="help-section emergency-section">
                        <div class="section-header">
                            <i data-lucide="alert-triangle" class="section-icon emergency-icon" stroke-width="1"></i>
                            <h3 class="section-title">Emergency Services</h3>
                        </div>
                        <div class="emergency-grid">
                            <div class="emergency-card">
                                <div class="emergency-info">
                                    <h4>Crisis Hotline</h4>
                                    <p>24/7 immediate crisis support</p>
                                    <span class="emergency-number">1-800-273-8255</span>
                                </div>
                                <button class="btn btn-primary emergency-btn" type="button">Call Now</button>
                            </div>
                            <div class="emergency-card">
                                <div class="emergency-info">
                                    <h4>Text Crisis Line</h4>
                                    <p>Text support available 24/7</p>
                                    <span class="emergency-number">Text "HOME" to 741741</span>
                                </div>
                                <button class="btn btn-primary emergency-btn" type="button">Text Now</button>
                            </div>
                            <div class="emergency-card">
                                <div class="emergency-info">
                                    <h4>Emergency Chat</h4>
                                    <p>Immediate online counselor</p>
                                    <span class="emergency-status">Available Now</span>
                                </div>
                                <button class="btn btn-primary emergency-btn" type="button">Start Chat</button>
                            </div>
                        </div>
                    </div>

                    <div class="help-section support-services">
                        <h3 class="section-title">Support Services</h3>
                        <div class="services-grid" id="servicesGrid"></div>
                    </div>

                    <div class="help-section quick-actions">
                        <h3 class="section-title">Quick Actions</h3>
                        <div class="actions-grid">
                            <div class="action-card">
                                <i data-lucide="calendar" class="action-icon" stroke-width="1"></i>
                                <h4>Schedule Session</h4>
                                <p>Book a counseling session</p>
                                <button class="btn btn-secondary" type="button">Schedule</button>
                            </div>
                            <div class="action-card">
                                <i data-lucide="users" class="action-icon" stroke-width="1"></i>
                                <h4>Join Support Group</h4>
                                <p>Connect with peer groups</p>
                                <button class="btn btn-secondary" type="button">Join Group</button>
                            </div>
                            <div class="action-card">
                                <i data-lucide="book-open" class="action-icon" stroke-width="1"></i>
                                <h4>Recovery Resources</h4>
                                <p>Access helpful materials</p>
                                <button class="btn btn-secondary" type="button">Browse</button>
                            </div>
                            <div class="action-card">
                                <i data-lucide="headphones" class="action-icon" stroke-width="1"></i>
                                <h4>Technical Support</h4>
                                <p>Get help with the platform</p>
                                <button class="btn btn-secondary" type="button">Get Help</button>
                            </div>
                        </div>
                    </div>

                    <div class="pagination" id="helpPagination" style="display: none;">
                        <button class="pagination-btn pagination-prev" id="prevBtn" disabled>
                            <i data-lucide="chevron-left" stroke-width="1.8"></i>
                        </button>
                        <div class="pagination-numbers" id="paginationNumbers"></div>
                        <button class="pagination-btn pagination-next" id="nextBtn">
                            <i data-lucide="chevron-right" stroke-width="1.8"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div id="centerDetailsModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalCenterName">Center Details</h3>
                <span class="close" onclick="window.closeCenterModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalCenterContent"></div>
        </div>
    </div>

    <script>
        window.supportServices = <?= json_encode(array_map(function ($center) {
                                    $availability = (string)($center['availability'] ?? 'Available');
                                    $status = 'available';
                                    if (stripos($availability, 'weekend') !== false || stripos($availability, 'busy') !== false) {
                                        $status = 'busy';
                                    }
                                    if (stripos($availability, 'offline') !== false || stripos($availability, 'closed') !== false) {
                                        $status = 'offline';
                                    }

                                    $type = strtolower((string)($center['type'] ?? 'resources'));
                                    $category = strtolower((string)($center['category'] ?? 'community'));

                                    $contactLabel = 'Contact';
                                    if (!empty($center['phoneNumber'])) $contactLabel = 'Call: ' . $center['phoneNumber'];
                                    elseif ($type === 'chat') $contactLabel = 'Start Chat';
                                    elseif ($type === 'appointment') $contactLabel = 'Schedule';
                                    elseif ($type === 'resources') $contactLabel = 'Browse';

                                    return [
                                        'id' => (int)$center['helpCenterId'],
                                        'title' => (string)$center['name'],
                                        'organization' => (string)($center['organization'] ?? ''),
                                        'type' => $type !== '' ? $type : 'resources',
                                        'category' => $category !== '' ? $category : 'community',
                                        'phoneNumber' => (string)($center['phoneNumber'] ?? ''),
                                        'email' => (string)($center['email'] ?? ''),
                                        'website' => (string)($center['website'] ?? ''),
                                        'address' => (string)($center['address'] ?? ''),
                                        'city' => (string)($center['city'] ?? ''),
                                        'state' => (string)($center['state'] ?? ''),
                                        'zipCode' => (string)($center['zipCode'] ?? ''),
                                        'availability' => $availability !== '' ? $availability : 'Available',
                                        'description' => (string)($center['description'] ?? ''),
                                        'specialties' => (string)($center['specialties'] ?? ''),
                                        'status' => $status,
                                        'contact' => $contactLabel,
                                    ];
                                }, $helpCenters), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    </script>
    <script src="/assets/js/user/helpCenter.js"></script>
    <script src="/assets/js/auth/user-profile.js"></script>
    <script src="/assets/js/user/log-urge-popup.js"></script>
    <script>
        if (typeof lucide !== 'undefined') lucide.createIcons();
    </script>
</body>

</html>
