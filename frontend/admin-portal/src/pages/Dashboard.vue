<template>
  <div class="admin-shell">
    <header class="admin-dock">
      <div class="admin-dock-profile">
        <div class="admin-avatar">{{ adminInitials }}</div>
        <div>
          <p class="admin-name">{{ profile?.name || 'Admin User' }}</p>
          <p class="admin-email">{{ profile?.email || 'admin@barangay.local' }}</p>
        </div>
      </div>
      <div class="admin-dock-divider"></div>
      <nav class="admin-dock-nav">
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'dashboard' }" href="#dashboard">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M4 4h7v7H4V4Zm9 0h7v5h-7V4ZM4 13h7v7H4v-7Zm9 7v-9h7v9h-7Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
            </svg>
          </span>
          Dashboard
        </a>
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'queue-control' }" href="#queue-control">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M5 7h14M5 12h10M5 17h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
              <path d="M17 14v6l3-3-3-3Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
          Queue
        </a>
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'resident-verification' }" href="#resident-verification">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M4 12l8-7 8 7v8a2 2 0 0 1-2 2h-4v-6H10v6H6a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
          Resident
        </a>
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'services' }" href="#services">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M12 3v5m0 8v5m9-9h-5M8 12H3m14.95-6.95-3.54 3.54m-4.82 4.82-3.54 3.54m0-8.36-3.54-3.54m12.3 12.3-3.54-3.54" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
          </span>
          Services
        </a>
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'transactions' }" href="#transactions">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M4 7h16M4 12h12M4 17h8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
              <path d="M18 14h2v6h-6v-2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
          Transactions
        </a>
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'kiosk-devices' }" href="#kiosk-devices">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <rect x="4" y="3" width="16" height="18" rx="3" stroke="currentColor" stroke-width="1.6" />
              <path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
          </span>
          Kiosks
        </a>
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'audit-logs' }" href="#audit-logs">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M6 3h9l3 3v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.6" />
              <path d="M9 10h6M9 14h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
          </span>
          Logs
        </a>
        <a v-if="isSuperAdmin" class="admin-nav-item" :class="{ 'is-active': activeSection === 'admin-users' }" href="#admin-users">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" stroke="currentColor" stroke-width="1.6" />
              <path d="M4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
          </span>
          Admins
        </a>
      </nav>
      <div class="admin-dock-divider"></div>
      <div class="admin-time">
        <span class="admin-time-label">PH Time</span>
        <span class="admin-time-value">{{ phTimeLabel }}</span>
      </div>
      <div class="admin-dock-divider"></div>
      <button class="admin-logout" type="button" @click="openLogoutModal">
        Sign Out
      </button>
    </header>

    <div class="admin-content">
      <div class="admin-page">
        <section id="dashboard" class="admin-dashboard" v-show="activeSection === 'dashboard'">
          <div class="dashboard-hero">
            <div class="dashboard-hero-main">
              <p class="dashboard-kicker">Operations Snapshot</p>
              <h2 class="dashboard-title">Admin Analytics Dashboard</h2>
              <p class="dashboard-subtitle">
                Live signals from registrations, queue flow, services, and kiosk activity to guide daily decisions.
              </p>
              <div class="dashboard-actions">
                <button class="dashboard-refresh" type="button" @click="refreshAll">Refresh Data</button>
                <a class="dashboard-link" href="#queue-control">Jump to Queue</a>
              </div>
              <p class="dashboard-updated">Updated {{ lastUpdatedLabel }}</p>
            </div>
            <div class="dashboard-hero-card">
              <div class="hero-stat">
                <p class="hero-stat-label">Active Queue</p>
                <div class="hero-stat-value">
                  <span>{{ todayActiveQueueCount }}</span>
                  <span class="hero-stat-unit">tickets</span>
                </div>
                <p class="hero-stat-note">{{ todayQueuePressureNote }}</p>
              </div>
              <div class="hero-mini-grid">
                <div class="hero-mini">
                  <span>Waiting</span>
                  <strong>{{ todayQueueStatusCounts.waiting }}</strong>
                </div>
                <div class="hero-mini">
                  <span>Serving</span>
                  <strong>{{ todayQueueStatusCounts.serving }}</strong>
                </div>
                <div class="hero-mini">
                  <span>Avg Wait</span>
                  <strong>{{ todayAverageWaitLabel }}</strong>
                </div>
              </div>
              <div class="hero-meter">
                <div class="hero-meter-track">
                  <span class="hero-meter-fill" :style="{ width: `${todayQueuePressurePercent}%` }"></span>
                </div>
                <div class="hero-meter-labels">
                  <span>Low</span>
                  <span>High</span>
                </div>
              </div>
            </div>
          </div>

          <div class="dashboard-stat-grid">
            <div class="dashboard-stat">
              <p class="dashboard-stat-label">Residents</p>
              <p class="dashboard-stat-value">{{ residentStats.total }}</p>
              <p class="dashboard-stat-meta">{{ residentStats.approved }} approved</p>
            </div>
            <div class="dashboard-stat">
              <p class="dashboard-stat-label">Pending Review</p>
              <p class="dashboard-stat-value">{{ residentStats.pending }}</p>
              <p class="dashboard-stat-meta">{{ approvalRateLabel }} approval rate</p>
            </div>
            <div class="dashboard-stat">
              <p class="dashboard-stat-label">Active Services</p>
              <p class="dashboard-stat-value">{{ activeServiceCount }}</p>
              <p class="dashboard-stat-meta">{{ inactiveServiceCount }} inactive</p>
            </div>
            <div class="dashboard-stat">
              <p class="dashboard-stat-label">Kiosk Health</p>
              <p class="dashboard-stat-value">{{ onlineKioskCount }}</p>
              <p class="dashboard-stat-meta">{{ offlineKioskCount }} offline</p>
            </div>
            <div class="dashboard-stat">
              <p class="dashboard-stat-label">Total Tickets</p>
              <p class="dashboard-stat-value">{{ queueStatusCounts.total }}</p>
              <p class="dashboard-stat-meta">{{ queueStatusCounts.done }} completed</p>
            </div>
            <div class="dashboard-stat">
              <p class="dashboard-stat-label">Service Focus</p>
              <p class="dashboard-stat-value">{{ topServiceLabel }}</p>
              <p class="dashboard-stat-meta">{{ topServiceCountLabel }}</p>
            </div>
          </div>

          <div class="dashboard-grid">
            <div class="dashboard-panel span-7">
              <div class="dashboard-panel-header">
                <div>
                  <h3>Queue Flow</h3>
                  <p>Live composition of queue tickets with status distribution.</p>
                </div>
                <span class="panel-badge">Real-Time</span>
              </div>
              <div class="queue-flow">
                <div class="queue-bar">
                  <span class="queue-segment is-waiting" :style="{ width: `${queueStatusPercentages.waiting}%` }"></span>
                  <span class="queue-segment is-serving" :style="{ width: `${queueStatusPercentages.serving}%` }"></span>
                  <span class="queue-segment is-done" :style="{ width: `${queueStatusPercentages.done}%` }"></span>
                  <span class="queue-segment is-cancelled" :style="{ width: `${queueStatusPercentages.cancelled}%` }"></span>
                </div>
                <div class="queue-legend">
                  <div class="legend-item">
                    <span class="legend-dot is-waiting"></span>
                    <span>Waiting</span>
                    <strong>{{ queueStatusCounts.waiting }}</strong>
                  </div>
                  <div class="legend-item">
                    <span class="legend-dot is-serving"></span>
                    <span>Serving</span>
                    <strong>{{ queueStatusCounts.serving }}</strong>
                  </div>
                  <div class="legend-item">
                    <span class="legend-dot is-done"></span>
                    <span>Done</span>
                    <strong>{{ queueStatusCounts.done }}</strong>
                  </div>
                  <div class="legend-item">
                    <span class="legend-dot is-cancelled"></span>
                    <span>Cancelled</span>
                    <strong>{{ queueStatusCounts.cancelled }}</strong>
                  </div>
                </div>
                <p class="queue-insight">{{ queueInsight }}</p>
              </div>
            </div>

            <div class="dashboard-panel span-5">
              <div class="dashboard-panel-header">
                <div>
                  <h3>Resident Pipeline</h3>
                  <p>Approval progress across registration statuses.</p>
                </div>
              </div>
              <div class="pipeline-list">
                <div class="pipeline-row">
                  <div class="pipeline-label">
                    <span>Approved</span>
                    <strong>{{ residentStats.approved }}</strong>
                  </div>
                  <div class="pipeline-track">
                    <span class="pipeline-fill is-success" :style="{ width: `${residentPercentages.approved}%` }"></span>
                  </div>
                </div>
                <div class="pipeline-row">
                  <div class="pipeline-label">
                    <span>Pending</span>
                    <strong>{{ residentStats.pending }}</strong>
                  </div>
                  <div class="pipeline-track">
                    <span class="pipeline-fill is-warning" :style="{ width: `${residentPercentages.pending}%` }"></span>
                  </div>
                </div>
                <div class="pipeline-row">
                  <div class="pipeline-label">
                    <span>Rejected</span>
                    <strong>{{ residentStats.rejected }}</strong>
                  </div>
                  <div class="pipeline-track">
                    <span class="pipeline-fill is-danger" :style="{ width: `${residentPercentages.rejected}%` }"></span>
                  </div>
                </div>
              </div>
              <p class="pipeline-note">{{ residentInsight }}</p>
            </div>

            <div class="dashboard-panel span-6">
              <div class="dashboard-panel-header">
                <div>
                  <h3>Kiosk Health</h3>
                  <p>Heartbeat status from registered devices.</p>
                </div>
              </div>
              <div v-if="kioskStatusList.length" class="kiosk-grid">
                <div v-for="kiosk in kioskStatusList" :key="kiosk.id" class="kiosk-card" :class="{ 'is-online': kiosk.online }">
                  <div class="kiosk-card-header">
                    <span class="kiosk-dot"></span>
                    <div>
                      <p class="kiosk-name">{{ kiosk.name }}</p>
                      <p class="kiosk-id">{{ kiosk.device_id }}</p>
                    </div>
                  </div>
                  <p class="kiosk-time">{{ kiosk.lastSeenLabel }}</p>
                </div>
              </div>
              <p v-else class="empty-state">No kiosk devices registered yet.</p>
            </div>

            <div class="dashboard-panel span-6">
              <div class="dashboard-panel-header">
                <div>
                  <h3>Insights</h3>
                  <p>Quick takeaways based on current data.</p>
                </div>
              </div>
              <ul class="insight-list">
                <li>{{ queueInsight }}</li>
                <li>{{ serviceInsight }}</li>
                <li>{{ residentInsight }}</li>
                <li>{{ kioskInsight }}</li>
              </ul>
            </div>

          </div>

          <div class="analytics-zone">
            <div class="analytics-header">
              <div>
                <p class="analytics-kicker">Deep Analytics</p>
                <h3 class="analytics-title">Operational Intelligence</h3>
                <p class="analytics-subtitle">
                  Visualize demand, approvals, and traffic patterns. Filters below apply to every chart.
                </p>
                <p class="analytics-range">Showing: {{ analyticsRangeLabel }}</p>
              </div>
              <div class="analytics-controls">
                <div class="analytics-actions">
                  <button class="analytics-export is-csv" type="button" @click="downloadAnalyticsCsv">
                    Download CSV
                  </button>
                  <button class="analytics-export is-pdf" type="button" @click="downloadAnalyticsPdf">
                    Download PDF
                  </button>
                </div>
                <div class="analytics-filters">
                  <div class="filter-field">
                    <label class="filter-label">Range</label>
                    <select v-model="analyticsRange" class="filter-input">
                      <option value="today">Today</option>
                      <option value="7d">Last 7 Days</option>
                      <option value="30d">Last 30 Days</option>
                      <option value="90d">Last 90 Days</option>
                      <option value="all">All Time</option>
                      <option value="custom">Custom Range</option>
                    </select>
                  </div>
                  <div v-if="analyticsRange === 'custom'" class="filter-field">
                    <label class="filter-label">Start</label>
                    <input v-model="analyticsStartDate" type="date" class="filter-input" />
                  </div>
                  <div v-if="analyticsRange === 'custom'" class="filter-field">
                    <label class="filter-label">End</label>
                    <input v-model="analyticsEndDate" type="date" class="filter-input" />
                  </div>
                  <div class="filter-field">
                    <label class="filter-label">Service</label>
                    <select v-model="analyticsServiceId" class="filter-input">
                      <option value="">All Services</option>
                      <option v-for="service in services" :key="service.id" :value="service.id">
                        {{ service.name }}
                      </option>
                    </select>
                  </div>
                  <div class="filter-field">
                    <label class="filter-label">Status</label>
                    <select v-model="analyticsStatus" class="filter-input">
                      <option value="all">All Statuses</option>
                      <option value="waiting">Waiting</option>
                      <option value="serving">Serving</option>
                      <option value="done">Done</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </div>
                  <button class="filter-reset" type="button" @click="resetAnalyticsFilters">
                    Reset
                  </button>
                </div>
              </div>
            </div>

            <div class="analytics-grid">
              <div class="analytics-card span-12 timing-card">
                <div class="chart-header">
                  <div>
                    <h4>Experience Timing</h4>
                    <p>Measure kiosk completion speed and ticket turnaround times.</p>
                  </div>
                  <span class="timing-badge">Timing</span>
                </div>
                <p class="timing-definition">
                  Kiosk completion starts when a QR is validated (scan/manual) and ends when a ticket is issued.
                  Sessions reset when users return to the kiosk landing screen. Ticket cycle metrics use completed tickets only.
                </p>
                <p class="timing-filter-note">Filtered By: {{ analyticsFilterNote }}</p>
                <div v-if="timingError" class="timing-error">{{ timingError }}</div>
                <div v-else-if="timingLoading" class="timing-loading">Loading timing analytics...</div>
                <div v-else>
                  <div class="timing-grid">
                    <div class="timing-tile timing-total">
                      <p class="timing-label">End-to-End Completion</p>
                      <p class="timing-value">{{ experienceTotalLabel }}</p>
                      <div class="timing-meta">
                        <span>Kiosk {{ kioskAvgLabel }}</span>
                        <span>Ticket Cycle {{ ticketCycleAvgLabel }}</span>
                      </div>
                    </div>
                    <div class="timing-tile">
                      <p class="timing-label">Kiosk Completion</p>
                      <p class="timing-value">{{ kioskAvgLabel }}</p>
                      <div class="timing-meta">
                        <span>Median {{ kioskMedianLabel }}</span>
                        <span>P90 {{ kioskP90Label }}</span>
                      </div>
                    </div>
                    <div class="timing-tile is-gold">
                      <p class="timing-label">Ticket Cycle</p>
                      <p class="timing-value">{{ ticketCycleAvgLabel }}</p>
                      <div class="timing-meta">
                        <span>Median {{ ticketCycleMedianLabel }}</span>
                        <span>P90 {{ ticketCycleP90Label }}</span>
                      </div>
                    </div>
                    <div class="timing-tile is-slate">
                      <p class="timing-label">Queue Split</p>
                      <p class="timing-value">{{ waitToServeLabel }}</p>
                      <div class="timing-meta">
                        <span>Wait to Serve</span>
                        <span>Service {{ serviceDurationLabel }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="timing-chart-grid">
                    <div class="timing-chart">
                      <div class="timing-chart-header">
                        <div>
                          <h5>Kiosk Completion Trend</h5>
                          <p>Average Time per Day</p>
                        </div>
                      </div>
                      <div v-if="kioskTimingSeries.length" class="mini-line-chart">
                        <svg class="mini-line-svg" viewBox="0 0 640 220" preserveAspectRatio="none" aria-hidden="true">
                          <defs>
                            <linearGradient id="kioskTimingFill" x1="0" x2="0" y1="0" y2="1">
                              <stop offset="0%" stop-color="#0b2c6f" stop-opacity="0.2" />
                              <stop offset="100%" stop-color="#0b2c6f" stop-opacity="0" />
                            </linearGradient>
                          </defs>
                          <g class="line-grid">
                            <line v-for="grid in lineGrid" :key="`kiosk-timing-grid-${grid}`" :x1="24" :x2="616" :y1="grid" :y2="grid" />
                          </g>
                          <path class="line-area" :d="kioskTimingTrend.areaPath" fill="url(#kioskTimingFill)" />
                          <path class="line-path" :d="kioskTimingTrend.linePath" />
                        </svg>
                        <div class="line-axis">
                          <span v-for="label in kioskTimingAxis" :key="`kiosk-timing-axis-${label}`">{{ label }}</span>
                        </div>
                      </div>
                      <p v-else class="empty-state">No kiosk completion data yet.</p>
                      <p class="analysis-note">{{ kioskTimingInsight }}</p>
                    </div>

                    <div class="timing-chart">
                      <div class="timing-chart-header">
                        <div>
                          <h5>Ticket Cycle Trend</h5>
                          <p>Issued to Done Over Time</p>
                        </div>
                      </div>
                      <div v-if="ticketCycleSeries.length" class="mini-line-chart">
                        <svg class="mini-line-svg" viewBox="0 0 640 220" preserveAspectRatio="none" aria-hidden="true">
                          <defs>
                            <linearGradient id="ticketCycleFill" x1="0" x2="0" y1="0" y2="1">
                              <stop offset="0%" stop-color="#f2c300" stop-opacity="0.25" />
                              <stop offset="100%" stop-color="#f2c300" stop-opacity="0" />
                            </linearGradient>
                          </defs>
                          <g class="line-grid">
                            <line v-for="grid in lineGrid" :key="`ticket-cycle-grid-${grid}`" :x1="24" :x2="616" :y1="grid" :y2="grid" />
                          </g>
                          <path class="line-area is-gold" :d="ticketCycleTrend.areaPath" fill="url(#ticketCycleFill)" />
                          <path class="line-path is-gold" :d="ticketCycleTrend.linePath" />
                        </svg>
                        <div class="line-axis">
                          <span v-for="label in ticketCycleAxis" :key="`ticket-cycle-axis-${label}`">{{ label }}</span>
                        </div>
                      </div>
                      <p v-else class="empty-state">No ticket cycle data yet.</p>
                      <p class="analysis-note">{{ ticketTimingInsight }}</p>
                    </div>

                    <div class="timing-chart timing-journey">
                      <div class="timing-chart-header">
                        <div>
                          <h5>Experience Journey</h5>
                          <p>Average Time Split by Phase</p>
                        </div>
                      </div>
                      <div v-if="experienceHasData" class="journey-body">
                        <div class="journey-bar">
                          <span class="journey-segment is-kiosk" :style="{ width: `${experienceSegments.kiosk}%` }"></span>
                          <span class="journey-segment is-wait" :style="{ width: `${experienceSegments.wait}%` }"></span>
                          <span class="journey-segment is-serve" :style="{ width: `${experienceSegments.serve}%` }"></span>
                        </div>
                        <div class="journey-legend">
                          <div class="journey-item">
                            <span class="journey-swatch is-kiosk"></span>
                            <span>Kiosk</span>
                            <strong>{{ kioskAvgLabel }}</strong>
                          </div>
                          <div class="journey-item">
                            <span class="journey-swatch is-wait"></span>
                            <span>Wait</span>
                            <strong>{{ waitToServeLabel }}</strong>
                          </div>
                          <div class="journey-item">
                            <span class="journey-swatch is-serve"></span>
                            <span>Service</span>
                            <strong>{{ serviceDurationLabel }}</strong>
                          </div>
                        </div>
                      </div>
                      <p v-else class="empty-state">No experience timing data yet.</p>
                      <p class="analysis-note">{{ queueBreakdownInsight }}</p>
                    </div>
                  </div>
                  <p v-if="timingScopeNote" class="timing-scope-note">{{ timingScopeNote }}</p>
                </div>
              </div>

              <div class="analytics-card span-7">
                <div class="chart-header">
                  <div>
                    <h4>Daily Ticket Volume</h4>
                    <p>Professional trend view of tickets created per day.</p>
                  </div>
                </div>
                <div v-if="ticketTrendSeries.length" class="line-chart is-volume">
                  <svg class="line-svg" viewBox="0 0 640 220" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                      <linearGradient id="ticketTrendFill" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#0b2c6f" stop-opacity="0.22" />
                        <stop offset="100%" stop-color="#0b2c6f" stop-opacity="0" />
                      </linearGradient>
                    </defs>
                    <g class="line-grid">
                      <line v-for="grid in lineGrid" :key="`ticket-grid-${grid}`" :x1="24" :x2="616" :y1="grid" :y2="grid" />
                    </g>
                    <path class="line-area is-volume" :d="ticketTrend.areaPath" fill="url(#ticketTrendFill)" />
                    <path class="line-path is-volume" :d="ticketTrend.linePath" />
                    <circle
                      v-for="point in ticketTrend.points"
                      :key="`ticket-point-${point.index}`"
                      class="line-point is-volume"
                      :cx="point.x"
                      :cy="point.y"
                      r="4"
                    />
                  </svg>
                  <div class="line-axis">
                    <span v-for="label in ticketTrendAxis" :key="`ticket-axis-${label}`">{{ label }}</span>
                  </div>
                </div>
                <p v-else class="empty-state">No ticket data in this range yet.</p>
                <p class="analysis-note">{{ ticketTrendInsight }}</p>
              </div>

              <div class="analytics-card span-5">
                <div class="chart-header">
                  <div>
                    <h4>Ticket Status Mix</h4>
                    <p>Distribution of queue outcomes.</p>
                  </div>
                </div>
                <div v-if="ticketStatusMix.length" class="donut-chart">
                  <div class="donut-figure">
                    <svg viewBox="0 0 120 120" class="donut-svg" aria-hidden="true">
                      <circle class="donut-bg" cx="60" cy="60" r="46" />
                      <circle
                        v-for="segment in ticketStatusSegments"
                        :key="segment.label"
                        class="donut-segment"
                        cx="60"
                        cy="60"
                        r="46"
                        :stroke="segment.color"
                        :stroke-dasharray="segment.dasharray"
                        :stroke-dashoffset="segment.dashoffset"
                      />
                    </svg>
                    <div class="donut-center">
                      <span class="donut-value">{{ ticketStatusTotal }}</span>
                      <span class="donut-label">Tickets</span>
                    </div>
                  </div>
                  <div class="chart-legend">
                    <div v-for="segment in ticketStatusMix" :key="segment.label" class="legend-item">
                      <span class="legend-swatch" :style="{ background: segment.color }"></span>
                      <span>{{ segment.label }}</span>
                      <strong>{{ segment.percent }}%</strong>
                    </div>
                  </div>
                </div>
                <p v-else class="empty-state">No ticket status data available.</p>
                <p class="analysis-note">{{ statusMixInsight }}</p>
              </div>

              <div class="analytics-card span-6">
                <div class="chart-header">
                  <div>
                    <h4>Most Booked Service</h4>
                    <p>Isometric skyline of service demand by rank.</p>
                  </div>
                </div>
                <div v-if="serviceVolumeBars.length" class="service-chart">
                  <div class="iso-bar-chart">
                    <div
                      v-for="(service, index) in serviceVolumeBars"
                      :key="service.id"
                      class="iso-bar"
                      :style="{
                        '--bar-height': `${Math.max(service.percent, 8)}%`,
                        '--bar-hue': `${(index * 55) % 360}`,
                      }"
                    >
                      <div class="iso-bar-stack">
                        <div class="iso-bar-body"></div>
                      </div>
                      <span class="iso-bar-label">{{ formatServiceName(service.name) }}</span>
                      <span class="iso-bar-value">{{ service.count }}</span>
                    </div>
                  </div>
                  <div class="service-chart-summary">
                    <p class="summary-title">{{ analyticsTopServiceTitle }}</p>
                    <p class="summary-subtitle">{{ analyticsTopServiceCountLabel }}</p>
                    <p class="summary-note">{{ analyticsFilterNote }}</p>
                  </div>
                </div>
                <p v-else class="empty-state">No service demand yet. Queue tickets will appear here.</p>
                <p class="analysis-note">{{ analyticsServiceInsight }}</p>
              </div>

              <div class="analytics-card span-6">
                <div class="chart-header">
                  <div>
                    <h4>Service Mix</h4>
                    <p>Pie view of service shares.</p>
                  </div>
                </div>
                <div v-if="serviceMix.length" class="pie-chart">
                  <div class="pie-figure">
                    <svg viewBox="0 0 120 120" class="pie-svg" aria-hidden="true">
                      <circle
                        v-for="segment in serviceMixSegments"
                        :key="segment.label"
                        class="pie-segment"
                        cx="60"
                        cy="60"
                        r="28"
                        :stroke="segment.color"
                        :stroke-dasharray="segment.dasharray"
                        :stroke-dashoffset="segment.dashoffset"
                      />
                    </svg>
                  </div>
                  <div class="chart-legend">
                    <div v-for="segment in serviceMix" :key="segment.label" class="legend-item">
                      <span class="legend-swatch" :style="{ background: segment.color }"></span>
                      <span>{{ segment.label }}</span>
                      <strong>{{ segment.percent }}%</strong>
                    </div>
                  </div>
                </div>
                <p v-else class="empty-state">No service mix data available.</p>
                <p class="analysis-note">{{ serviceMixInsight }}</p>
              </div>

              <div class="analytics-card span-6">
                <div class="chart-header">
                  <div>
                    <h4>Traffic by Time</h4>
                    <p>Mountain view of ticket volume by 3-hour window.</p>
                  </div>
                </div>
                <div v-if="trafficSeries.length" class="line-chart is-traffic">
                  <svg class="line-svg" viewBox="0 0 640 220" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                      <linearGradient id="trafficTrendFill" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#0ea5e9" stop-opacity="0.4" />
                        <stop offset="100%" stop-color="#0ea5e9" stop-opacity="0" />
                      </linearGradient>
                    </defs>
                    <g class="line-grid">
                      <line v-for="grid in lineGrid" :key="`traffic-grid-${grid}`" :x1="24" :x2="616" :y1="grid" :y2="grid" />
                    </g>
                    <path class="line-area is-traffic" :d="trafficTrend.areaPath" fill="url(#trafficTrendFill)" />
                    <path class="line-path is-traffic" :d="trafficTrend.linePath" />
                    <circle
                      v-for="point in trafficTrend.points"
                      :key="`traffic-point-${point.index}`"
                      class="line-point is-traffic"
                      :cx="point.x"
                      :cy="point.y"
                      r="4"
                    />
                  </svg>
                  <div class="line-axis">
                    <span v-for="label in trafficAxis" :key="`traffic-axis-${label}`">{{ label }}</span>
                  </div>
                </div>
                <p v-else class="empty-state">No traffic data yet. Ticket activity will build this view.</p>
                <p class="analysis-note">{{ trafficInsight }}</p>
              </div>

              <div class="analytics-card span-6">
                <div class="chart-header">
                  <div>
                    <h4>Resident Registrations</h4>
                    <p>Daily registration activity.</p>
                  </div>
                </div>
                <div v-if="residentTrendSeries.length" class="line-chart">
                  <svg class="line-svg" viewBox="0 0 640 220" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                      <linearGradient id="residentTrendFill" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#f2c300" stop-opacity="0.3" />
                        <stop offset="100%" stop-color="#f2c300" stop-opacity="0" />
                      </linearGradient>
                    </defs>
                    <g class="line-grid">
                      <line v-for="grid in lineGrid" :key="`resident-grid-${grid}`" :x1="24" :x2="616" :y1="grid" :y2="grid" />
                    </g>
                    <path class="line-area is-gold" :d="residentTrend.areaPath" fill="url(#residentTrendFill)" />
                    <path class="line-path is-gold" :d="residentTrend.linePath" />
                    <circle
                      v-for="point in residentTrend.points"
                      :key="`resident-point-${point.index}`"
                      class="line-point is-gold"
                      :cx="point.x"
                      :cy="point.y"
                      r="4"
                    />
                  </svg>
                  <div class="line-axis">
                    <span v-for="label in residentTrendAxis" :key="`resident-axis-${label}`">{{ label }}</span>
                  </div>
                </div>
                <p v-else class="empty-state">No registrations in this range yet.</p>
                <p class="analysis-note">{{ residentTrendInsight }}</p>
              </div>
            </div>
          </div>
        </section>

        <div id="resident-verification" class="admin-card mt-6" v-show="activeSection === 'resident-verification'">
          <div class="tool-header">
            <div class="tool-title">
              <span class="tool-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M4 12l8-7 8 7v8a2 2 0 0 1-2 2h-4v-6H10v6H6a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </span>
              <h2 class="tool-heading">Resident Management</h2>
            </div>
            <div class="tool-accent" aria-hidden="true">
              <span class="tool-accent-bar is-primary"></span>
              <span class="tool-accent-bar is-gold"></span>
              <span class="tool-accent-bar is-neutral"></span>
            </div>
          </div>

          <div class="resident-layout mt-6">
            <div class="resident-side">
              <div class="resident-overview">
                <p class="resident-kicker">Resident operations</p>
                <h3 class="resident-title">Verification Command Center</h3>
                <p class="resident-subtitle">
                  Track registration flow, manage profiles, and keep the community directory accurate.
                </p>
                <div class="resident-metric-grid">
                  <div class="resident-metric-card">
                    <span>Total residents</span>
                    <strong>{{ residentStats.total }}</strong>
                    <small>{{ approvalRateLabel }} approval rate</small>
                  </div>
                  <div class="resident-metric-card is-warning">
                    <span>Pending review</span>
                    <strong>{{ residentStats.pending }}</strong>
                    <small>Awaiting action</small>
                  </div>
                  <div class="resident-metric-card is-success">
                    <span>Approved</span>
                    <strong>{{ residentStats.approved }}</strong>
                    <small>Ready for kiosks</small>
                  </div>
                  <div class="resident-metric-card is-danger">
                    <span>Rejected</span>
                    <strong>{{ residentStats.rejected }}</strong>
                    <small>Needs follow-up</small>
                  </div>
                </div>
              </div>

              <div class="resident-filters-card">
                <div class="resident-filters-header">
                  <div>
                    <h4>Directory Filters</h4>
                    <p>Refine the list before taking action.</p>
                  </div>
                  <button class="resident-primary" type="button" @click="openCreateResidentModal">New Resident</button>
                </div>
                <div class="resident-filter-grid">
                  <label class="resident-field">
                    <span>Search</span>
                    <input
                      v-model="search"
                      type="text"
                      placeholder="Name or email"
                      class="resident-input"
                    />
                  </label>
                  <label class="resident-field">
                    <span>Status</span>
                    <select v-model="statusFilter" class="resident-input">
                      <option value="">All Statuses</option>
                      <option value="pending">Pending</option>
                      <option value="approved">Approved</option>
                      <option value="rejected">Rejected</option>
                    </select>
                  </label>
                </div>
                <div class="resident-filter-actions">
                  <button class="resident-secondary" type="button" @click="refreshResidents">Refresh List</button>
                  <button class="resident-tertiary" type="button" @click="resetResidentFilters">Clear Filters</button>
                </div>
              </div>
            </div>

            <div class="resident-table-card">
              <div class="resident-table-header">
                <div>
                  <h3>Resident Directory</h3>
                  <p>{{ residentRangeLabel }}</p>
                </div>
                <div class="resident-table-meta">
                  <span class="resident-chip">Results {{ residents.length }}</span>
                  <span class="resident-chip is-muted">Total {{ residentStats.total }}</span>
                  <span v-if="statusFilter" class="resident-chip is-muted">{{ statusFilter }}</span>
                  <span v-if="search" class="resident-chip is-muted">Search: "{{ search }}"</span>
                </div>
              </div>
              <div class="resident-table-wrapper">
                <table class="w-full text-base">
                  <thead class="text-left text-[#6B7280]">
                    <tr class="border-b border-[#E5E7EB]">
                      <th class="py-2">Resident</th>
                      <th class="py-2">Email</th>
                      <th class="py-2">Status</th>
                      <th class="py-2">Valid ID</th>
                      <th class="py-2">Registered</th>
                      <th class="py-2 text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="isLoadingResidents">
                      <td colspan="6" class="py-6">
                        <div class="table-state">
                          <span class="table-state-icon"></span>
                          <span>Loading residents...</span>
                        </div>
                      </td>
                    </tr>
                    <tr v-else-if="residents.length === 0">
                      <td colspan="6" class="py-6">
                        <div class="table-state">
                          <span class="table-state-icon"></span>
                          <span>No residents found.</span>
                        </div>
                      </td>
                    </tr>
                    <tr
                      v-for="resident in residentPageRows"
                      :key="resident.id"
                      class="border-b border-[#F3F4F6] resident-row"
                      :class="rowClass(resident.status)"
                    >
                      <td class="py-4">
                        <div class="resident-identity">
                          <div class="resident-avatar">{{ residentInitials(resident) }}</div>
                          <div>
                            <p class="resident-name">{{ resident.first_name }} {{ resident.last_name }}</p>
                            <p class="resident-id">
                              ID #{{ resident.id }}
                              <span v-if="resident.username">- {{ resident.username }}</span>
                            </p>
                            <p v-if="resident.mobile_number" class="resident-meta">{{ resident.mobile_number }}</p>
                          </div>
                        </div>
                      </td>
                      <td class="py-4">
                        <span class="resident-email">{{ resident.email }}</span>
                      </td>
                      <td class="py-4">
                        <span class="status-pill" :class="statusClass(resident.status)">
                          {{ resident.status }}
                        </span>
                      </td>
                      <td class="py-4">
                        <div class="resident-id-link">
                          <a
                            v-if="resident.valid_id_url"
                            :href="resolveIdUrl(resident.valid_id_url)"
                            target="_blank"
                            rel="noopener"
                          >
                            View ID
                          </a>
                          <span v-else class="resident-id-missing">Not uploaded</span>
                          <small v-if="resident.verification_status" class="resident-id-status">
                            {{ resident.verification_status }}
                          </small>
                        </div>
                      </td>
                      <td class="py-4">{{ formatDate(resident.created_at) }}</td>
                      <td class="py-4 text-right">
                        <div class="resident-action-group">
                          <button class="resident-action is-outline" type="button" @click="openEditResidentModal(resident)">
                            Manage
                          </button>
                          <button class="resident-action is-ghost" type="button" @click="openDeleteResidentModal(resident)">
                            Delete
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <p v-if="residentError" class="mt-4 text-base text-[#C0392B]">{{ residentError }}</p>
              </div>
              <div class="resident-pagination">
                <span class="resident-range">{{ residentRangeLabel }}</span>
                <div class="resident-page-controls">
                  <button
                    class="resident-tertiary"
                    type="button"
                    :disabled="residentPage === 1"
                    @click="previousResidentPage"
                  >
                    Prev
                  </button>
                  <span class="resident-page-label">Page {{ residentPage }} of {{ residentTotalPages }}</span>
                  <button
                    class="resident-tertiary"
                    type="button"
                    :disabled="residentPage === residentTotalPages"
                    @click="nextResidentPage"
                  >
                    Next
                  </button>
                </div>
              </div>
            </div>

          </div>

          <div v-if="isResidentModalOpen" class="modal-overlay" @click.self="closeResidentModal">
            <div class="modal-card is-wide" role="dialog" aria-modal="true">
              <div class="modal-header">
                <div>
                  <h3>{{ residentModalTitle }}</h3>
                  <p>{{ residentModalSubtitle }}</p>
                </div>
                <button class="modal-close" type="button" @click="closeResidentModal">Close</button>
              </div>
              <form class="modal-body" @submit.prevent="submitResidentForm">
                <div class="modal-summary">
                  <div class="modal-summary-profile">
                    <img :src="residentModalPhoto" alt="Resident photo" />
                    <div>
                      <p class="modal-summary-name">{{ residentModalName || 'Resident profile' }}</p>
                      <p class="modal-summary-meta">{{ residentForm.email || 'No email on file' }}</p>
                      <p v-if="residentForm.username" class="modal-summary-meta">@{{ residentForm.username }}</p>
                    </div>
                  </div>
                  <div class="modal-summary-status">
                    <span class="status-pill" :class="statusClass(residentForm.status)">
                      {{ residentForm.status || 'pending' }}
                    </span>
                    <p class="modal-summary-id">{{ residentModalId }}</p>
                  </div>
                </div>

                <div class="modal-grid">
                  <label class="modal-field">
                    <span>Username</span>
                    <input v-model="residentForm.username" type="text" autocomplete="username" />
                  </label>
                  <label class="modal-field">
                    <span>Email</span>
                    <input v-model="residentForm.email" type="email" autocomplete="email" required />
                  </label>
                  <label class="modal-field">
                    <span>First name</span>
                    <input v-model="residentForm.first_name" type="text" autocomplete="given-name" required />
                  </label>
                  <label class="modal-field">
                    <span>Middle name</span>
                    <input v-model="residentForm.middle_name" type="text" autocomplete="additional-name" />
                  </label>
                  <label class="modal-field">
                    <span>Last name</span>
                    <input v-model="residentForm.last_name" type="text" autocomplete="family-name" required />
                  </label>
                  <label class="modal-field">
                    <span>Mobile number</span>
                    <input v-model="residentForm.mobile_number" type="tel" autocomplete="tel" />
                  </label>
                  <label class="modal-field">
                    <span>Date of birth</span>
                    <input v-model="residentForm.date_of_birth" type="date" autocomplete="bday" />
                  </label>
                  <label class="modal-field">
                    <span>Gender</span>
                    <select v-model="residentForm.gender">
                      <option value="">Select gender</option>
                      <option v-for="option in genderOptions" :key="option" :value="option">{{ option }}</option>
                    </select>
                  </label>
                  <label class="modal-field">
                    <span>Civil status</span>
                    <select v-model="residentForm.civil_status">
                      <option value="">Select status</option>
                      <option v-for="option in civilStatusOptions" :key="option" :value="option">{{ option }}</option>
                    </select>
                  </label>
                  <label v-if="residentModalMode === 'create'" class="modal-field">
                    <span>Status</span>
                    <select v-model="residentForm.status">
                      <option value="pending">Pending</option>
                      <option value="approved">Approved</option>
                      <option value="rejected">Rejected</option>
                    </select>
                  </label>
                  <label class="modal-field modal-full">
                    <span>Barangay address</span>
                    <textarea v-model="residentForm.address" rows="3"></textarea>
                  </label>
                  <div class="modal-field modal-full">
                    <span>Valid ID upload</span>
                    <div class="modal-id-card">
                      <div class="modal-id-meta">
                        <span>Verification status</span>
                        <strong>{{ residentTarget?.verification_status || 'pending' }}</strong>
                        <a
                          v-if="residentTarget?.valid_id_url"
                          class="modal-id-link"
                          :href="resolveIdUrl(residentTarget.valid_id_url)"
                          target="_blank"
                          rel="noopener"
                        >
                          Open file
                        </a>
                      </div>
                      <div v-if="residentTarget?.valid_id_url" class="modal-id-preview">
                        <img
                          v-if="isImageUrl(residentTarget.valid_id_url)"
                          :src="resolveIdUrl(residentTarget.valid_id_url)"
                          alt="Valid ID preview"
                        />
                        <div v-else class="modal-id-file">PDF uploaded</div>
                      </div>
                      <p v-else class="modal-id-empty">No ID uploaded yet.</p>
                    </div>
                  </div>
                  <label class="modal-field modal-full">
                    <span>Password</span>
                    <input
                      v-model="residentForm.password"
                      type="password"
                      autocomplete="new-password"
                      :required="residentModalMode === 'create'"
                    />
                    <small v-if="residentModalMode === 'edit'">Leave blank to keep the existing password.</small>
                  </label>
                </div>

                <div v-if="residentModalMode === 'edit'" class="modal-status-actions">
                  <button
                    class="resident-primary"
                    type="button"
                    :disabled="isResidentSaving || residentForm.status === 'approved'"
                    @click="applyResidentStatus('approved')"
                  >
                    Approve resident
                  </button>
                  <button
                    class="resident-secondary"
                    type="button"
                    :disabled="isResidentSaving || residentForm.status === 'pending'"
                    @click="applyResidentStatus('pending')"
                  >
                    Mark pending
                  </button>
                  <button
                    class="resident-danger"
                    type="button"
                    :disabled="isResidentSaving || residentForm.status === 'rejected'"
                    @click="applyResidentStatus('rejected')"
                  >
                    Reject Resident
                  </button>
                </div>

                <p v-if="residentFormError" class="modal-error">{{ residentFormError }}</p>
                <div class="modal-actions">
                  <button class="resident-tertiary" type="button" @click="closeResidentModal">Cancel</button>
                  <button class="resident-secondary" type="submit" :disabled="isResidentSaving">
                    {{ isResidentSaving ? 'Saving...' : residentSubmitLabel }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <div v-if="isDeleteResidentOpen" class="modal-overlay" @click.self="closeDeleteResidentModal">
            <div class="modal-card is-danger" role="dialog" aria-modal="true">
              <div class="modal-header">
                <div>
                  <h3>Delete Resident</h3>
                  <p>This action cannot be undone.</p>
                </div>
                <button class="modal-close" type="button" @click="closeDeleteResidentModal">Close</button>
              </div>
              <div class="modal-body">
                <p>
                  You are about to delete
                  <strong>{{ residentDeleteTarget?.first_name }} {{ residentDeleteTarget?.last_name }}</strong>.
                </p>
                <p class="modal-muted">Make sure there are no queue records linked to this resident.</p>
                <p v-if="deleteResidentError" class="modal-error">{{ deleteResidentError }}</p>
                <div class="modal-actions">
                  <button class="resident-tertiary" type="button" @click="closeDeleteResidentModal">Cancel</button>
                  <button class="resident-danger" type="button" :disabled="isDeletingResident" @click="confirmDeleteResident">
                    {{ isDeletingResident ? 'Deleting...' : 'Delete Resident' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

          <section id="services" class="admin-card mt-6" v-show="activeSection === 'services'">
            <div class="tool-header">
              <div class="tool-title">
                <span class="tool-icon">
                  <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3v5m0 8v5m9-9h-5M8 12H3m14.95-6.95-3.54 3.54m-4.82 4.82-3.54 3.54m0-8.36-3.54-3.54m12.3 12.3-3.54-3.54" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                  </svg>
                </span>
                <h2 class="tool-heading">Services</h2>
              </div>
              <div class="tool-accent" aria-hidden="true">
                <span class="tool-accent-bar is-primary"></span>
                <span class="tool-accent-bar is-gold"></span>
                <span class="tool-accent-bar is-neutral"></span>
              </div>
            </div>

            <div class="service-layout mt-6">
              <div class="service-side">
                <div class="service-overview">
                  <p class="service-kicker">Service operations</p>
                  <h3 class="service-title">Catalog Control Hub</h3>
                  <p class="service-subtitle">
                    Manage offerings, keep codes consistent, and highlight demand leaders at a glance.
                  </p>
                  <div class="service-metric-grid">
                    <div class="service-metric-card">
                      <span>Total services</span>
                      <strong>{{ services.length }}</strong>
                      <small>Across all counters</small>
                    </div>
                    <div class="service-metric-card is-success">
                      <span>Active services</span>
                      <strong>{{ activeServiceCount }}</strong>
                      <small>Visible to kiosks</small>
                    </div>
                    <div class="service-metric-card is-danger">
                      <span>Inactive</span>
                      <strong>{{ inactiveServiceCount }}</strong>
                      <small>Hidden from residents</small>
                    </div>
                    <div class="service-metric-card is-accent">
                      <span>Demand leader</span>
                      <strong>{{ topServiceLabel }}</strong>
                      <small>{{ topServiceCountLabel }}</small>
                    </div>
                  </div>
                </div>

                <div class="service-action-card">
                  <div class="service-action-header">
                    <div>
                      <h4>Service Controls</h4>
                      <p>Search and filter before editing.</p>
                    </div>
                    <button class="resident-primary" type="button" @click="openCreateServiceModal">New Service</button>
                  </div>
                  <div class="service-action-grid">
                    <label class="service-field">
                      <span>Search</span>
                      <input
                        v-model="serviceSearch"
                        type="text"
                        placeholder="Name or code"
                        class="service-input"
                        @input="servicePage = 1"
                      />
                    </label>
                    <label class="service-field">
                      <span>Status</span>
                      <select v-model="serviceStatusFilter" class="service-input" @change="servicePage = 1">
                        <option value="all">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                      </select>
                    </label>
                  </div>
                  <div class="service-action-footer">
                    <button class="resident-secondary" type="button" @click="loadServices">Refresh List</button>
                    <button class="resident-tertiary" type="button" @click="resetServiceFilters">Clear Filters</button>
                  </div>
                </div>
              </div>

              <div class="service-table-card">
                <div class="service-table-header">
                  <div>
                    <h3>Service Directory</h3>
                    <p>{{ serviceRangeLabel }}</p>
                  </div>
                  <div class="service-table-meta">
                    <span class="service-chip">Total {{ services.length }}</span>
                    <span class="service-chip is-muted">Active {{ activeServiceCount }}</span>
                    <span class="service-chip is-muted">Inactive {{ inactiveServiceCount }}</span>
                    <span v-if="serviceStatusFilter !== 'all'" class="service-chip is-muted">{{ serviceStatusFilter }}</span>
                    <span v-if="serviceSearch" class="service-chip is-muted">Search: "{{ serviceSearch }}"</span>
                  </div>
                </div>
                <div class="service-table-wrapper">
                  <table class="w-full text-base">
                    <thead class="text-left text-[#6B7280]">
                      <tr class="border-b border-[#E5E7EB]">
                        <th class="py-2">Service</th>
                        <th class="py-2">Code</th>
                        <th class="py-2">Status</th>
                        <th class="py-2 text-right">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="isLoadingServices">
                        <td colspan="4" class="py-4">
                          <div class="table-state">
                            <span class="table-state-icon"></span>
                            <span>Loading services...</span>
                          </div>
                        </td>
                      </tr>
                      <tr v-else-if="serviceFiltered.length === 0">
                        <td colspan="4" class="py-4">
                          <div class="table-state">
                            <span class="table-state-icon"></span>
                            <span>No services match the filters.</span>
                          </div>
                        </td>
                      </tr>
                      <tr v-for="service in servicePageRows" :key="service.id" class="border-b border-[#F3F4F6]" :class="rowClass(service.active ? 'active' : 'inactive')">
                        <td class="py-3">
                          <div class="service-identity">
                            <div class="service-badge">{{ service.code }}</div>
                            <div>
                              <p class="service-name">{{ service.name }}</p>
                              <p class="service-id">ID #{{ service.id }}</p>
                            </div>
                          </div>
                        </td>
                        <td class="py-3">
                          <span class="service-code">{{ service.code }}</span>
                        </td>
                        <td class="py-3">
                          <span class="status-pill" :class="statusClass(service.active ? 'active' : 'inactive')">
                            {{ service.active ? 'Active' : 'Inactive' }}
                          </span>
                        </td>
                        <td class="py-3 text-right">
                          <div class="service-action-group">
                            <button class="service-action is-outline" type="button" @click="openEditServiceModal(service)">
                              Edit
                            </button>
                            <button
                              :class="['service-action', service.active ? 'is-danger' : 'is-success']"
                              type="button"
                              @click="toggleService(service)"
                            >
                              {{ service.active ? 'Deactivate' : 'Activate' }}
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <p v-if="serviceError" class="mt-3 text-base text-[#C0392B]">{{ serviceError }}</p>
                </div>
                <div class="service-pagination">
                  <span class="service-range">{{ serviceRangeLabel }}</span>
                  <div class="service-page-controls">
                    <button class="resident-tertiary" type="button" :disabled="servicePage === 1" @click="previousServicePage">
                      Prev
                    </button>
                    <span class="service-page-label">Page {{ servicePage }} of {{ serviceTotalPages }}</span>
                    <button
                      class="resident-tertiary"
                      type="button"
                      :disabled="servicePage === serviceTotalPages"
                      @click="nextServicePage"
                    >
                      Next
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="isServiceModalOpen" class="modal-overlay" @click.self="closeServiceModal">
              <div class="modal-card" role="dialog" aria-modal="true">
                <div class="modal-header">
                  <div>
                    <h3>{{ serviceModalTitle }}</h3>
                    <p>Keep the catalog consistent across kiosks and portals.</p>
                  </div>
                  <button class="modal-close" type="button" @click="closeServiceModal">Close</button>
                </div>
                <form class="modal-body" @submit.prevent="submitServiceForm">
                  <div class="modal-grid">
                    <label class="modal-field">
                      <span>Service name</span>
                      <input v-model="serviceForm.name" type="text" required />
                    </label>
                    <label class="modal-field">
                      <span>Service code</span>
                      <input v-model="serviceForm.code" type="text" required />
                    </label>
                    <label class="modal-field modal-full">
                      <span>Status</span>
                      <select v-model="serviceForm.active">
                        <option :value="1">Active</option>
                        <option :value="0">Inactive</option>
                      </select>
                    </label>
                  </div>
                  <p v-if="serviceFormError" class="modal-error">{{ serviceFormError }}</p>
                  <div class="modal-actions">
                    <button class="resident-tertiary" type="button" @click="closeServiceModal">Cancel</button>
                    <button class="resident-primary" type="submit" :disabled="isServiceSaving">
                      {{ isServiceSaving ? 'Saving...' : serviceSubmitLabel }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </section>

          <section id="queue-control" class="admin-card mt-6" v-show="activeSection === 'queue-control'">
            <div class="tool-header">
              <div class="tool-title">
                <span class="tool-icon is-gold">
                  <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M5 7h14M5 12h10M5 17h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                    <path d="M17 14v6l3-3-3-3Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </span>
                <h2 class="tool-heading">Queue Control</h2>
              </div>
              <div class="tool-accent" aria-hidden="true">
                <span class="tool-accent-bar is-primary"></span>
                <span class="tool-accent-bar is-gold"></span>
                <span class="tool-accent-bar is-neutral"></span>
              </div>
            </div>

            <div class="queue-command-grid mt-6">
              <div class="queue-primary-stack">
                <div class="queue-serving-card">
                  <div class="queue-serving-header">
                    <div>
                      <p class="queue-kicker">Call Station</p>
                      <h3 class="queue-title">Now Serving</h3>
                    </div>
                    <span
                      v-if="activeCallTicket"
                      class="status-pill queue-serving-status"
                      :class="statusClass(activeCallTicket.status)"
                    >
                      {{ activeCallTicket.status }}
                    </span>
                  </div>

                  <div v-if="activeCallTicket" class="queue-serving-body">
                    <div class="queue-serving-main">
                      <p class="queue-serving-ticket">{{ activeCallTicket.ticket_no }}</p>
                      <p class="queue-serving-resident">{{ formatTicketResident(activeCallTicket) }}</p>
                      <p class="queue-serving-service">{{ ticketServiceLabel(activeCallTicket) }}</p>
                      <p v-if="ticketHasMultipleServices(activeCallTicket)" class="queue-serving-service-list">
                        {{ ticketServiceList(activeCallTicket) }}
                      </p>
                    </div>
                    <div class="queue-serving-grid">
                      <div class="queue-serving-detail">
                        <span>Resident ID</span>
                        <strong>{{ activeCallTicket.resident_id ? `BSM-RES-${String(activeCallTicket.resident_id).padStart(6, '0')}` : '--' }}</strong>
                      </div>
                      <div class="queue-serving-detail">
                        <span>Issued</span>
                        <strong>{{ formatTime(activeCallTicket.issued_at) }}</strong>
                      </div>
                      <div class="queue-serving-detail">
                        <span>Email</span>
                        <strong>{{ activeCallTicket.resident_email || '--' }}</strong>
                      </div>
                      <div class="queue-serving-detail">
                        <span>Mobile</span>
                        <strong>{{ activeCallTicket.resident_mobile_number || '--' }}</strong>
                      </div>
                    </div>
                    <div class="queue-serving-actions">
                      <button class="resident-secondary" type="button" @click="printCallTicket(activeCallTicket)">
                        Print Form
                      </button>
                      <button class="resident-primary" type="button" @click="serveTicket(activeCallTicket)">
                        Mark Done
                      </button>
                      <button class="resident-danger" type="button" @click="cancelTicket(activeCallTicket)">
                        Cancel
                      </button>
                    </div>
                  </div>
                  <div v-else class="queue-serving-empty">
                    <p class="queue-serving-empty-text">
                      No active ticket yet. Use the suggested next call or pick a ticket manually.
                    </p>
                    <div v-if="queueNextCandidate" class="queue-serving-suggest-card">
                      <div>
                        <span class="queue-serving-suggest-label">Suggested next</span>
                        <div class="queue-serving-suggest-ticket">{{ queueNextCandidate.ticket_no }}</div>
                        <div class="queue-serving-suggest-resident">{{ formatTicketResident(queueNextCandidate) }}</div>
                        <div class="queue-serving-suggest-service">{{ ticketServiceLabel(queueNextCandidate) }}</div>
                      </div>
                      <button class="resident-primary queue-serving-suggest-cta" type="button" @click="callNext">
                        Call next
                      </button>
                    </div>
                    <div v-else class="queue-serving-empty-state">
                      No waiting tickets right now. The call station will update once a new ticket arrives.
                    </div>
                  </div>
                </div>
              </div>

              <div class="queue-side-stack">
                <div class="queue-next-card is-inline">
                  <p class="queue-card-label">Next in Line</p>
                  <div v-if="queueWaitingTotal === 0 && !isLoadingQueue" class="queue-empty-notice">
                    <div class="queue-empty-icon" aria-hidden="true">
                      <svg viewBox="0 0 24 24" fill="none">
                        <path d="M5 12h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                        <path d="M9 16h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                      </svg>
                    </div>
                    <div>
                      <strong>All caught up</strong>
                      <span>No waiting tickets right now.</span>
                    </div>
                  </div>
                  <label class="queue-field">
                    <span>Service for Next Call</span>
                    <select v-model="callNextServiceId" class="queue-input">
                      <option value="auto">Auto (Earliest Ticket)</option>
                      <option v-for="service in services" :key="service.id" :value="service.id">
                        {{ service.name }} ({{ service.code }})
                      </option>
                    </select>
                  </label>
                  <div class="queue-next-block">
                    <p class="queue-next-label">Suggested next</p>
                    <div class="queue-next-main">
                      <div>
                        <p class="queue-next-ticket">{{ queueNextCandidate ? queueNextCandidate.ticket_no : '--' }}</p>
                        <p class="queue-next-resident">
                          {{ queueNextCandidate ? formatTicketResident(queueNextCandidate) : 'No waiting ticket' }}
                        </p>
                      </div>
                      <button class="resident-primary queue-call-cta" type="button" :disabled="!queueNextCandidate" @click="callNext">
                        Call next
                      </button>
                    </div>
                    <div class="queue-next-meta">
                      <span>Waiting: {{ queueWaitingList.length }}</span>
                      <span>Issued: {{ queueNextCandidate ? formatTime(queueNextCandidate.issued_at) : '--' }}</span>
                    </div>
                    <p class="queue-call-note">{{ queueDecisionNote }}</p>
                  </div>
                  <div class="queue-next-block">
                    <p class="queue-next-label">Manual pick</p>
                    <label class="queue-field">
                      <span>Select waiting ticket</span>
                      <select v-model="manualNextTicketId" class="queue-input">
                        <option value="">Choose from waiting list</option>
                        <option v-for="ticket in queueWaitingList" :key="ticket.id" :value="ticket.id">
                          {{ ticket.ticket_no }} — {{ formatTicketResident(ticket) }} · {{ ticketServiceCodeLabel(ticket) }}
                        </option>
                      </select>
                    </label>
                    <button
                      class="resident-secondary queue-manual-cta"
                      type="button"
                      :disabled="!manualNextTicket"
                      @click="callTicket(manualNextTicket)"
                    >
                      Call selected
                    </button>
                  </div>
                </div>

                <div class="queue-monitor-card">
                  <div class="queue-monitor-header">
                    <div>
                      <p class="queue-card-label">Queue Monitor</p>
                      <p class="queue-monitor-subtitle">Live preview of the public display.</p>
                    </div>
                    <span class="queue-monitor-pill">Preview</span>
                  </div>
                  <div class="queue-monitor-frame" ref="queueMonitorFrame">
                    <iframe
                      :src="queueMonitorUrl"
                      :style="queueMonitorIframeStyle"
                      title="Queue monitor preview"
                      loading="lazy"
                      referrerpolicy="no-referrer"
                    ></iframe>
                  </div>
                </div>

                <div class="queue-metric-card is-summary">
                  <p class="queue-card-label">Queue snapshot</p>
                  <div class="queue-snapshot-grid">
                    <div class="queue-snapshot-card is-waiting">
                      <span>Waiting</span>
                      <strong>{{ queueStatusCounts.waiting }}</strong>
                      <small>Queued</small>
                    </div>
                    <div class="queue-snapshot-card is-serving">
                      <span>Serving</span>
                      <strong>{{ queueStatusCounts.serving }}</strong>
                      <small>On desk</small>
                    </div>
                    <div class="queue-snapshot-card is-done">
                      <span>Done</span>
                      <strong>{{ queueStatusCounts.done }}</strong>
                      <small>Completed</small>
                    </div>
                    <div class="queue-snapshot-card is-cancelled">
                      <span>Cancelled</span>
                      <strong>{{ queueStatusCounts.cancelled }}</strong>
                      <small>Stopped</small>
                    </div>
                  </div>
                  <div class="queue-snapshot-footer">
                    <div>
                      <span>Avg wait</span>
                      <strong>{{ averageWaitLabel }}</strong>
                    </div>
                    <div>
                      <span>Active now</span>
                      <strong>{{ activeQueueCount }}</strong>
                    </div>
                  </div>
                  <p class="queue-metric-note">{{ queuePressureNote }}</p>
                  <button class="resident-tertiary" type="button" @click="loadQueue">Refresh Queue</button>
                </div>
              </div>
            </div>

            <div class="queue-board-grid mt-6">
              <div class="queue-table-card">
                <div class="queue-table-header">
                  <div>
                    <h3>Queue Board</h3>
                    <p>{{ queueRangeLabel }}</p>
                  </div>
                  <div class="queue-table-meta">
                    <span class="queue-chip">Total {{ queueTickets.length }}</span>
                    <span class="queue-chip is-muted">Filtered {{ queueSortedTickets.length }}</span>
                  </div>
                </div>
                <div class="queue-table-wrapper">
                  <table class="w-full text-base">
                    <thead class="text-left text-[#6B7280]">
                      <tr class="border-b border-[#E5E7EB]">
                        <th class="py-2">Ticket</th>
                        <th class="py-2">Service</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Issued</th>
                        <th class="py-2 text-right">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="isLoadingQueue">
                        <td colspan="5" class="py-4">
                          <div class="table-state">
                            <span class="table-state-icon"></span>
                            <span>Loading tickets...</span>
                          </div>
                        </td>
                      </tr>
                      <tr v-else-if="queueSortedTickets.length === 0">
                        <td colspan="5" class="py-4">
                          <div class="table-state">
                            <span class="table-state-icon"></span>
                            <span>No tickets match the filters.</span>
                          </div>
                        </td>
                      </tr>
                      <tr v-for="ticket in queuePageRows" :key="ticket.id" class="border-b border-[#F3F4F6]" :class="rowClass(ticket.status)">
                        <td class="py-3">
                          <div class="queue-ticket">
                            <span class="queue-ticket-no">{{ ticket.ticket_no }}</span>
                            <small>{{ formatTicketResident(ticket) }}</small>
                          </div>
                        </td>
                        <td class="py-3">
                          <div class="queue-service">
                            <span class="queue-service-name">
                              {{ ticketServiceLabel(ticket) }}
                            </span>
                            <small>{{ ticketServiceCodeLabel(ticket) }}</small>
                          </div>
                        </td>
                        <td class="py-3">
                          <span class="status-pill" :class="statusClass(ticket.status)">
                            {{ ticket.status }}
                          </span>
                        </td>
                        <td class="py-3">{{ formatTime(ticket.issued_at) }}</td>
                        <td class="py-3 text-right">
                          <div class="queue-action-group">
                            <button
                              v-if="ticket.status === 'waiting'"
                              class="queue-action is-primary"
                              type="button"
                              @click="callTicket(ticket)"
                            >
                              Call
                            </button>
                            <button
                              v-if="ticket.status === 'serving'"
                              class="queue-action is-success"
                              type="button"
                              @click="serveTicket(ticket)"
                            >
                              Complete
                            </button>
                            <button
                              v-if="ticket.status === 'waiting' || ticket.status === 'serving'"
                              class="queue-action is-danger"
                              type="button"
                              @click="cancelTicket(ticket)"
                            >
                              Cancel
                            </button>
                            <span v-if="ticket.status === 'done' || ticket.status === 'cancelled'" class="queue-action-muted">--</span>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <p v-if="queueError" class="mt-3 text-base text-[#C0392B]">{{ queueError }}</p>
                </div>
                <div class="queue-pagination">
                  <span class="queue-range">{{ queueRangeLabel }}</span>
                  <div class="queue-page-controls">
                    <button class="resident-tertiary" type="button" :disabled="queuePage === 1" @click="previousQueuePage">
                      Prev
                    </button>
                    <span class="queue-page-label">Page {{ queuePage }} of {{ queueTotalPages }}</span>
                    <button
                      class="resident-tertiary"
                      type="button"
                      :disabled="queuePage === queueTotalPages"
                      @click="nextQueuePage"
                    >
                      Next
                    </button>
                  </div>
                </div>
              </div>

              <div class="queue-filter-card">
                <div class="queue-filter-header">
                  <div>
                    <h4>Queue Filters</h4>
                    <p>Sort and search before actioning tickets.</p>
                  </div>
                  <button class="resident-secondary" type="button" @click="loadQueue">Refresh List</button>
                </div>
                <div class="queue-filter-grid">
                  <label class="queue-field">
                    <span>Search</span>
                    <input
                      v-model="queueSearch"
                      type="text"
                      placeholder="Ticket, resident, or service"
                      class="queue-input"
                      @input="queuePage = 1"
                    />
                  </label>
                  <label class="queue-field">
                    <span>Service</span>
                    <select v-model="queueServiceId" class="queue-input" @change="queuePage = 1">
                      <option value="">All services</option>
                      <option v-for="service in services" :key="service.id" :value="service.id">
                        {{ service.name }}
                      </option>
                    </select>
                  </label>
                  <label class="queue-field">
                    <span>Status</span>
                    <select v-model="queueStatus" class="queue-input" @change="queuePage = 1">
                      <option value="">All Statuses</option>
                      <option value="waiting">Waiting</option>
                      <option value="serving">Serving</option>
                      <option value="done">Done</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </label>
                  <label class="queue-field">
                    <span>Sort by</span>
                    <select v-model="queueSortKey" class="queue-input" @change="queuePage = 1">
                      <option value="smart">Smart order</option>
                      <option value="issued_at">Issued time</option>
                      <option value="ticket_no">Ticket number</option>
                      <option value="service_id">Service</option>
                      <option value="status">Status</option>
                    </select>
                  </label>
                </div>
                <div class="queue-filter-actions">
                  <button class="resident-tertiary" type="button" @click="toggleQueueSortDirection">
                    Sort: {{ queueSortDirection === 'asc' ? 'Ascending' : 'Descending' }}
                  </button>
                  <button class="resident-tertiary" type="button" @click="resetQueueFilters">Clear Filters</button>
                </div>
              </div>
            </div>
          </section>
        <section id="transactions" class="admin-card mt-6" v-show="activeSection === 'transactions'">
          <div class="tool-header">
            <div class="tool-title">
              <span class="tool-icon is-gold">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M4 7h16M4 12h12M4 17h8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                  <path d="M18 14h2v6h-6v-2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </span>
              <h2 class="tool-heading">Transactions</h2>
            </div>
            <div class="tool-accent" aria-hidden="true">
              <span class="tool-accent-bar is-primary"></span>
              <span class="tool-accent-bar is-gold"></span>
              <span class="tool-accent-bar is-neutral"></span>
            </div>
          </div>

          <div class="transaction-layout mt-6">
            <div class="transaction-side">
              <div class="transaction-overview">
                <p class="transaction-kicker">Ticket outcomes</p>
                <h3 class="transaction-title">Service Completion Tracker</h3>
                <p class="transaction-subtitle">
                  Review completed and cancelled requests with clear sorting and audit-ready filters.
                </p>
                <div class="transaction-metric-grid">
                  <div class="transaction-metric-card">
                    <span>Total records</span>
                    <strong>{{ transactionCounts.total }}</strong>
                    <small>Loaded for review</small>
                  </div>
                  <div class="transaction-metric-card is-success">
                    <span>Completed</span>
                    <strong>{{ transactionCounts.done }}</strong>
                    <small>Done tickets</small>
                  </div>
                  <div class="transaction-metric-card is-danger">
                    <span>Cancelled</span>
                    <strong>{{ transactionCounts.cancelled }}</strong>
                    <small>Cancelled tickets</small>
                  </div>
                  <div class="transaction-metric-card is-accent">
                    <span>Completion rate</span>
                    <strong>
                      {{
                        transactionCounts.total
                          ? Math.round((transactionCounts.done / transactionCounts.total) * 100)
                          : 0
                      }}%
                    </strong>
                    <small>Of loaded records</small>
                  </div>
                </div>
              </div>

              <div class="transaction-filter-card">
                <div class="transaction-filter-header">
                  <div>
                    <h4>Transaction Filters</h4>
                    <p>Limit by service, status, or search term.</p>
                  </div>
                  <button class="resident-secondary" type="button" @click="loadTransactions">Refresh List</button>
                </div>
                <div class="transaction-filter-grid">
                  <label class="transaction-field">
                    <span>Search</span>
                    <input
                      v-model="transactionSearch"
                      type="text"
                      placeholder="Ticket, status, or service"
                      class="transaction-input"
                      @input="transactionPage = 1"
                    />
                  </label>
                  <label class="transaction-field">
                    <span>Service</span>
                    <select
                      v-model="transactionServiceId"
                      class="transaction-input"
                      @change="handleTransactionFilterChange"
                    >
                      <option value="">All services</option>
                      <option v-for="service in services" :key="service.id" :value="service.id">
                        {{ service.name }}
                      </option>
                    </select>
                  </label>
                  <label class="transaction-field">
                    <span>Status</span>
                    <select
                      v-model="transactionStatus"
                      class="transaction-input"
                      @change="handleTransactionFilterChange"
                    >
                      <option value="">All Statuses</option>
                      <option value="done">Done</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </label>
                  <label class="transaction-field">
                    <span>Sort by</span>
                    <select v-model="transactionSortKey" class="transaction-input" @change="transactionPage = 1">
                      <option value="issued_at">Issued time</option>
                      <option value="ticket_no">Ticket number</option>
                      <option value="service_id">Service</option>
                      <option value="status">Status</option>
                    </select>
                  </label>
                </div>
                <div class="transaction-filter-actions">
                  <button class="resident-tertiary" type="button" @click="toggleTransactionSortDirection">
                    Sort: {{ transactionSortDirection === 'asc' ? 'Ascending' : 'Descending' }}
                  </button>
                  <button class="resident-tertiary" type="button" @click="resetTransactionFilters">Clear Filters</button>
                </div>
              </div>
            </div>

            <div class="transaction-table-card">
              <div class="transaction-table-header">
                <div>
                  <h3>Transaction Ledger</h3>
                  <p>{{ transactionRangeLabel }}</p>
                </div>
                <div class="transaction-table-meta">
                  <span class="transaction-chip">Total {{ transactions.length }}</span>
                  <span class="transaction-chip is-muted">Filtered {{ transactionSorted.length }}</span>
                </div>
              </div>
              <div class="transaction-table-wrapper">
                <table class="w-full text-base">
                  <thead class="text-left text-[#6B7280]">
                    <tr class="border-b border-[#E5E7EB]">
                      <th class="py-2">Ticket</th>
                      <th class="py-2">Service</th>
                      <th class="py-2">Status</th>
                      <th class="py-2">Issued</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="isLoadingTransactions">
                      <td colspan="4" class="py-4">
                        <div class="table-state">
                          <span class="table-state-icon"></span>
                          <span>Loading transactions...</span>
                        </div>
                      </td>
                    </tr>
                    <tr v-else-if="transactionSorted.length === 0">
                      <td colspan="4" class="py-4">
                        <div class="table-state">
                          <span class="table-state-icon"></span>
                          <span>No transactions match the filters.</span>
                        </div>
                      </td>
                    </tr>
                    <tr v-for="ticket in transactionPageRows" :key="ticket.id" class="border-b border-[#F3F4F6]" :class="rowClass(ticket.status)">
                      <td class="py-3">
                        <div class="transaction-ticket">
                          <span class="transaction-ticket-no">{{ ticket.ticket_no }}</span>
                          <small>Resident #{{ ticket.resident_id }}</small>
                        </div>
                      </td>
                      <td class="py-3">
                        <div class="transaction-service">
                          <span class="transaction-service-name">
                            {{ services.find((service) => service.id === ticket.service_id)?.name || `Service #${ticket.service_id}` }}
                          </span>
                          <small>{{ services.find((service) => service.id === ticket.service_id)?.code || '' }}</small>
                        </div>
                      </td>
                      <td class="py-3">
                        <span class="status-pill" :class="statusClass(ticket.status)">
                          {{ ticket.status }}
                        </span>
                      </td>
                      <td class="py-3">{{ formatDate(ticket.issued_at) }}</td>
                    </tr>
                  </tbody>
                </table>
                <p v-if="transactionError" class="mt-3 text-base text-[#C0392B]">{{ transactionError }}</p>
              </div>
              <div class="transaction-pagination">
                <span class="transaction-range">{{ transactionRangeLabel }}</span>
                <div class="transaction-page-controls">
                  <button class="resident-tertiary" type="button" :disabled="transactionPage === 1" @click="previousTransactionPage">
                    Prev
                  </button>
                  <span class="transaction-page-label">Page {{ transactionPage }} of {{ transactionTotalPages }}</span>
                  <button
                    class="resident-tertiary"
                    type="button"
                    :disabled="transactionPage === transactionTotalPages"
                    @click="nextTransactionPage"
                  >
                    Next
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

          <section id="kiosk-devices" class="admin-card mt-6" v-show="activeSection === 'kiosk-devices'">
            <div class="tool-header">
              <div class="tool-title">
                <span class="tool-icon">
                  <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="4" y="3" width="16" height="18" rx="3" stroke="currentColor" stroke-width="1.6" />
                    <path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                  </svg>
                </span>
                <h2 class="tool-heading">Kiosk Devices</h2>
              </div>
              <div class="tool-accent" aria-hidden="true">
                <span class="tool-accent-bar is-primary"></span>
                <span class="tool-accent-bar is-gold"></span>
                <span class="tool-accent-bar is-neutral"></span>
              </div>
            </div>

            <div class="kiosk-layout mt-6">
              <div class="kiosk-side">
                <div class="kiosk-overview">
                  <p class="kiosk-kicker">Device readiness</p>
                  <h3 class="kiosk-title">Kiosk Availability Dashboard</h3>
                  <p class="kiosk-subtitle">
                    Track devices in real time and onboard new kiosks with consistent metadata.
                  </p>
                  <div class="kiosk-metric-grid">
                    <div class="kiosk-metric-card">
                      <span>Total kiosks</span>
                      <strong>{{ kiosks.length }}</strong>
                      <small>Registered devices</small>
                    </div>
                    <div class="kiosk-metric-card is-success">
                      <span>Online</span>
                      <strong>{{ onlineKioskCount }}</strong>
                      <small>Reporting within 20 min</small>
                    </div>
                    <div class="kiosk-metric-card is-danger">
                      <span>Offline</span>
                      <strong>{{ offlineKioskCount }}</strong>
                      <small>Needs attention</small>
                    </div>
                  <div class="kiosk-metric-card is-accent">
                    <span>Last update</span>
                    <strong>{{ kioskLatestSeenLabel }}</strong>
                    <small>Most recent check-in</small>
                  </div>
                  </div>
                </div>

                <div class="kiosk-action-card">
                  <div class="kiosk-action-header">
                    <div>
                      <h4>Register a Kiosk</h4>
                      <p>Assign a device ID and friendly name.</p>
                    </div>
                    <button class="resident-secondary" type="button" @click="loadKiosks">Refresh List</button>
                  </div>
                  <div class="kiosk-action-grid">
                    <label class="kiosk-field">
                      <span>Device ID</span>
                      <input v-model="newKiosk.device_id" class="kiosk-input" placeholder="Device ID" />
                    </label>
                    <label class="kiosk-field">
                      <span>Display name</span>
                      <input v-model="newKiosk.name" class="kiosk-input" placeholder="Name" />
                    </label>
                  </div>
                  <div class="kiosk-action-footer">
                    <button class="resident-primary" type="button" @click="createKiosk">Add kiosk</button>
                    <button class="resident-tertiary" type="button" @click="resetKioskFilters">Clear Filters</button>
                  </div>
                </div>
              </div>

              <div class="kiosk-table-card">
                <div class="kiosk-table-header">
                  <div>
                    <h3>Kiosk Directory</h3>
                    <p>{{ kioskRangeLabel }}</p>
                  </div>
                  <div class="kiosk-table-meta">
                    <span class="kiosk-chip">Total {{ kiosks.length }}</span>
                    <span class="kiosk-chip is-muted">Online {{ onlineKioskCount }}</span>
                    <span class="kiosk-chip is-muted">Offline {{ offlineKioskCount }}</span>
                  </div>
                </div>
                <div class="kiosk-filter-row">
                  <label class="kiosk-field">
                    <span>Search</span>
                    <input
                      v-model="kioskSearch"
                      class="kiosk-input"
                      placeholder="Device, name, or token"
                      @input="kioskPage = 1"
                    />
                  </label>
                  <label class="kiosk-field">
                    <span>Status</span>
                    <select v-model="kioskStatusFilter" class="kiosk-input" @change="kioskPage = 1">
                      <option value="all">All Statuses</option>
                      <option value="online">Online</option>
                      <option value="offline">Offline</option>
                    </select>
                  </label>
                  <label class="kiosk-field">
                    <span>Sort by</span>
                    <select v-model="kioskSortKey" class="kiosk-input" @change="kioskPage = 1">
                      <option value="last_seen_at">Last seen</option>
                      <option value="name">Name</option>
                      <option value="device_id">Device ID</option>
                      <option value="status">Status</option>
                    </select>
                  </label>
                  <div class="kiosk-filter-actions">
                    <button class="resident-tertiary" type="button" @click="toggleKioskSortDirection">
                      Sort: {{ kioskSortDirection === 'asc' ? 'Ascending' : 'Descending' }}
                    </button>
                  </div>
                </div>
                <div class="kiosk-table-wrapper">
                  <table class="w-full text-base">
                    <thead class="text-left text-[#6B7280]">
                      <tr class="border-b border-[#E5E7EB]">
                        <th class="py-2">Device</th>
                        <th class="py-2">Name</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Token</th>
                        <th class="py-2">Last seen</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="isLoadingKiosks">
                        <td colspan="5" class="py-4">
                          <div class="table-state">
                            <span class="table-state-icon"></span>
                            <span>Loading kiosks...</span>
                          </div>
                        </td>
                      </tr>
                      <tr v-else-if="kioskSorted.length === 0">
                        <td colspan="5" class="py-4">
                          <div class="table-state">
                            <span class="table-state-icon"></span>
                            <span>No kiosks match the filters.</span>
                          </div>
                        </td>
                      </tr>
                      <tr v-for="kiosk in kioskPageRows" :key="kiosk.id" class="border-b border-[#F3F4F6]" :class="rowClass(kiosk.online ? 'active' : 'inactive')">
                        <td class="py-3">
                          <div class="kiosk-device">
                            <span class="kiosk-device-id">{{ kiosk.device_id }}</span>
                            <small>ID #{{ kiosk.id }}</small>
                          </div>
                        </td>
                        <td class="py-3">
                          <span class="kiosk-name">{{ kiosk.name }}</span>
                        </td>
                        <td class="py-3">
                          <span class="status-pill" :class="statusClass(kiosk.online ? 'active' : 'inactive')">
                            {{ kiosk.online ? 'Online' : 'Offline' }}
                          </span>
                        </td>
                        <td class="py-3">
                          <span class="kiosk-token">{{ kiosk.token }}</span>
                        </td>
                        <td class="py-3">
                          <span class="kiosk-last">{{ kiosk.lastSeenLabel }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <p v-if="kioskError" class="mt-3 text-base text-[#C0392B]">{{ kioskError }}</p>
                </div>
                <div class="kiosk-pagination">
                  <span class="kiosk-range">{{ kioskRangeLabel }}</span>
                  <div class="kiosk-page-controls">
                    <button class="resident-tertiary" type="button" :disabled="kioskPage === 1" @click="previousKioskPage">
                      Prev
                    </button>
                    <span class="kiosk-page-label">Page {{ kioskPage }} of {{ kioskTotalPages }}</span>
                    <button
                      class="resident-tertiary"
                      type="button"
                      :disabled="kioskPage === kioskTotalPages"
                      @click="nextKioskPage"
                    >
                      Next
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <section id="audit-logs" class="admin-card mt-6" v-show="activeSection === 'audit-logs'">
            <div class="tool-header">
              <div class="tool-title">
                <span class="tool-icon">
                  <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 3h9l3 3v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.6" />
                    <path d="M9 10h6M9 14h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                  </svg>
                </span>
                <h2 class="tool-heading">Audit Logs</h2>
              </div>
              <div class="tool-accent" aria-hidden="true">
                <span class="tool-accent-bar is-primary"></span>
                <span class="tool-accent-bar is-gold"></span>
                <span class="tool-accent-bar is-neutral"></span>
              </div>
            </div>

            <div class="audit-layout mt-6">
              <div class="audit-side">
                <div class="audit-overview">
                  <p class="audit-kicker">System trail</p>
                  <h3 class="audit-title">Operational Audit Stream</h3>
                  <p class="audit-subtitle">
                    Track every action across admins, kiosks, and residents with searchable history.
                  </p>
                  <div class="audit-metric-grid">
                    <div class="audit-metric-card">
                      <span>Total logs</span>
                      <strong>{{ auditCounts.total }}</strong>
                      <small>Captured events</small>
                    </div>
                    <div class="audit-metric-card is-success">
                      <span>Admin actions</span>
                      <strong>{{ auditCounts.admin }}</strong>
                      <small>Staff activity</small>
                    </div>
                    <div class="audit-metric-card is-accent">
                      <span>Kiosk actions</span>
                      <strong>{{ auditCounts.kiosk }}</strong>
                      <small>Device events</small>
                    </div>
                    <div class="audit-metric-card is-neutral">
                      <span>Recent log</span>
                      <strong>{{ auditLatestLabel }}</strong>
                      <small>Last activity</small>
                    </div>
                  </div>
                </div>

                <div class="audit-filter-card">
                  <div class="audit-filter-header">
                    <div>
                      <h4>Log Filters</h4>
                      <p>Search by action, actor, or ID.</p>
                    </div>
                    <button class="resident-secondary" type="button" @click="loadAuditLogs">Refresh logs</button>
                  </div>
                  <div class="audit-filter-grid">
                    <label class="audit-field">
                      <span>Search</span>
                      <input
                        v-model="auditSearch"
                        class="audit-input"
                        placeholder="Action or actor"
                        @input="auditPage = 1"
                      />
                    </label>
                    <label class="audit-field">
                      <span>Actor type</span>
                      <select v-model="auditActorFilter" class="audit-input" @change="auditPage = 1">
                        <option value="all">All actors</option>
                        <option value="admin">admin</option>
                        <option value="kiosk">kiosk</option>
                        <option value="resident">resident</option>
                      </select>
                    </label>
                    <label class="audit-field">
                      <span>Sort by</span>
                      <select v-model="auditSortKey" class="audit-input" @change="auditPage = 1">
                        <option value="created_at">Date</option>
                        <option value="action">Action</option>
                        <option value="actor_type">Actor</option>
                      </select>
                    </label>
                  </div>
                  <div class="audit-filter-actions">
                    <button class="resident-tertiary" type="button" @click="toggleAuditSortDirection">
                      Sort: {{ auditSortDirection === 'asc' ? 'Ascending' : 'Descending' }}
                    </button>
                    <button class="resident-tertiary" type="button" @click="resetAuditFilters">Clear Filters</button>
                  </div>
                </div>
              </div>

              <div class="audit-table-card">
                <div class="audit-table-header">
                  <div>
                    <h3>Audit Logbook</h3>
                    <p>{{ auditRangeLabel }}</p>
                  </div>
                  <div class="audit-table-meta">
                    <span class="audit-chip">Total {{ auditCounts.total }}</span>
                    <span class="audit-chip is-muted">Filtered {{ auditSorted.length }}</span>
                  </div>
                </div>
                <div class="audit-table-wrapper">
                  <table class="w-full text-base">
                    <thead class="text-left text-[#6B7280]">
                      <tr class="border-b border-[#E5E7EB]">
                        <th class="py-2">When</th>
                        <th class="py-2">Action</th>
                        <th class="py-2">Actor</th>
                        <th class="py-2">Details</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="isLoadingLogs">
                        <td colspan="4" class="py-4">
                          <div class="table-state">
                            <span class="table-state-icon"></span>
                            <span>Loading logs...</span>
                          </div>
                        </td>
                      </tr>
                      <tr v-else-if="auditSorted.length === 0">
                        <td colspan="4" class="py-4">
                          <div class="table-state">
                            <span class="table-state-icon"></span>
                            <span>No logs match the filters.</span>
                          </div>
                        </td>
                      </tr>
                      <tr v-for="log in auditPageRows" :key="log.id" class="border-b border-[#F3F4F6]" :class="rowClass('neutral')">
                        <td class="py-3">
                          <div class="audit-time">
                            <span>{{ formatDate(log.created_at) }}</span>
                            <small>{{ formatTime(log.created_at) }}</small>
                          </div>
                        </td>
                        <td class="py-3">
                          <span class="audit-action">{{ formatAction(log.action) }}</span>
                        </td>
                        <td class="py-3">
                          <span class="audit-actor">{{ log.actor_type }} #{{ log.actor_id }}</span>
                        </td>
                        <td class="py-3">
                          <span class="audit-meta">{{ log.meta_json ? 'Has metadata' : 'No metadata' }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <p v-if="auditError" class="mt-3 text-base text-[#C0392B]">{{ auditError }}</p>
                </div>
                <div class="audit-pagination">
                  <span class="audit-range">{{ auditRangeLabel }}</span>
                  <div class="audit-page-controls">
                    <button class="resident-tertiary" type="button" :disabled="auditPage === 1" @click="previousAuditPage">
                      Prev
                    </button>
                    <span class="audit-page-label">Page {{ auditPage }} of {{ auditTotalPages }}</span>
                    <button
                      class="resident-tertiary"
                      type="button"
                      :disabled="auditPage === auditTotalPages"
                      @click="nextAuditPage"
                    >
                      Next
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </section>
        <div id="admin-users" class="admin-card mt-6" v-if="isSuperAdmin && activeSection === 'admin-users'">
          <div class="tool-header">
            <div class="tool-title">
              <span class="tool-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" stroke="currentColor" stroke-width="1.6" />
                  <path d="M4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                </svg>
              </span>
              <h2 class="tool-heading">Admin Users</h2>
            </div>
            <div class="tool-accent" aria-hidden="true">
              <span class="tool-accent-bar is-primary"></span>
              <span class="tool-accent-bar is-gold"></span>
              <span class="tool-accent-bar is-neutral"></span>
            </div>
          </div>

          <div class="admin-users-layout mt-6">
            <div class="admin-users-side">
              <div class="admin-users-overview">
                <p class="admin-users-kicker">Access control</p>
                <h3 class="admin-users-title">Admin Roster</h3>
                <p class="admin-users-subtitle">
                  Maintain leadership access and keep staff roles aligned with service assignments.
                </p>
                <div class="admin-users-metric-grid">
                  <div class="admin-users-metric-card">
                    <span>Total admins</span>
                    <strong>{{ adminCounts.total }}</strong>
                    <small>Active accounts</small>
                  </div>
                  <div class="admin-users-metric-card is-accent">
                    <span>Super admins</span>
                    <strong>{{ adminCounts.super }}</strong>
                    <small>Full access</small>
                  </div>
                  <div class="admin-users-metric-card is-success">
                    <span>Staff admins</span>
                    <strong>{{ adminCounts.staff }}</strong>
                    <small>Assigned services</small>
                  </div>
                  <div class="admin-users-metric-card is-neutral">
                    <span>Last updated</span>
                    <strong>{{ lastUpdatedLabel }}</strong>
                    <small>Roster refresh</small>
                  </div>
                </div>
              </div>

              <div class="admin-users-form-card">
                <div class="admin-users-form-header">
                  <div>
                    <h4>Create Admin</h4>
                    <p>Invite a new admin to manage services.</p>
                  </div>
                  <button class="resident-secondary" type="button" @click="loadAdmins">Refresh List</button>
                </div>
                <div class="admin-users-form-grid">
                  <label class="admin-users-field">
                    <span>Name</span>
                    <input v-model="newAdmin.name" class="admin-users-input" placeholder="Name" />
                  </label>
                  <label class="admin-users-field">
                    <span>Email</span>
                    <input v-model="newAdmin.email" class="admin-users-input" placeholder="Email" />
                  </label>
                  <label class="admin-users-field">
                    <span>Password</span>
                    <input v-model="newAdmin.password" class="admin-users-input" placeholder="Password" />
                  </label>
                  <label class="admin-users-field">
                    <span>Role</span>
                    <select v-model="newAdmin.role" class="admin-users-input">
                      <option value="staff_admin">staff_admin</option>
                      <option value="super_admin">super_admin</option>
                    </select>
                  </label>
                  <label class="admin-users-field admin-users-field-full">
                    <span>Service IDs</span>
                    <input
                      v-model="newAdmin.service_ids"
                      class="admin-users-input"
                      placeholder="Comma-separated IDs (staff only)"
                    />
                  </label>
                </div>
                <div class="admin-users-form-actions">
                  <button class="resident-primary" type="button" @click="createAdmin">Add Admin</button>
                  <button class="resident-tertiary" type="button" @click="resetAdminFilters">Clear Filters</button>
                </div>
              </div>

              <div class="admin-users-filter-card">
                <div class="admin-users-filter-header">
                  <div>
                    <h4>Roster Filters</h4>
                    <p>Search and sort the admin list.</p>
                  </div>
                </div>
                <div class="admin-users-filter-grid">
                  <label class="admin-users-field">
                    <span>Search</span>
                    <input
                      v-model="adminSearch"
                      class="admin-users-input"
                      placeholder="Name or email"
                      @input="adminPage = 1"
                    />
                  </label>
                  <label class="admin-users-field">
                    <span>Role</span>
                    <select v-model="adminRoleFilter" class="admin-users-input" @change="adminPage = 1">
                      <option value="all">All roles</option>
                      <option value="super_admin">super_admin</option>
                      <option value="staff_admin">staff_admin</option>
                    </select>
                  </label>
                  <label class="admin-users-field">
                    <span>Sort by</span>
                    <select v-model="adminSortKey" class="admin-users-input" @change="adminPage = 1">
                      <option value="created_at">Created date</option>
                      <option value="name">Name</option>
                      <option value="role">Role</option>
                    </select>
                  </label>
                </div>
                <div class="admin-users-filter-actions">
                  <button class="resident-tertiary" type="button" @click="toggleAdminSortDirection">
                    Sort: {{ adminSortDirection === 'asc' ? 'Ascending' : 'Descending' }}
                  </button>
                </div>
              </div>
            </div>

            <div class="admin-users-table-card">
              <div class="admin-users-table-header">
                <div>
                  <h3>Admin Directory</h3>
                  <p>{{ adminRangeLabel }}</p>
                </div>
                <div class="admin-users-table-meta">
                  <span class="admin-users-chip">Total {{ adminCounts.total }}</span>
                  <span class="admin-users-chip is-muted">Super {{ adminCounts.super }}</span>
                  <span class="admin-users-chip is-muted">Staff {{ adminCounts.staff }}</span>
                </div>
              </div>
              <div class="admin-users-table-wrapper">
                <table class="w-full text-base">
                  <thead class="text-left text-[#6B7280]">
                    <tr class="border-b border-[#E5E7EB]">
                      <th class="py-2">Admin</th>
                      <th class="py-2">Email</th>
                      <th class="py-2">Role</th>
                      <th class="py-2">Services</th>
                      <th class="py-2">Created</th>
                      <th class="py-2 text-right">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="isLoadingAdmins">
                      <td colspan="6" class="py-4">
                        <div class="table-state">
                          <span class="table-state-icon"></span>
                          <span>Loading admins...</span>
                        </div>
                      </td>
                    </tr>
                    <tr v-else-if="adminSorted.length === 0">
                      <td colspan="6" class="py-4">
                        <div class="table-state">
                          <span class="table-state-icon"></span>
                          <span>No admins match the filters.</span>
                        </div>
                      </td>
                    </tr>
                    <tr v-for="admin in adminPageRows" :key="admin.id" class="border-b border-[#F3F4F6]" :class="rowClass('neutral')">
                      <td class="py-3">
                        <div class="admin-users-identity">
                          <div class="admin-users-avatar">{{ getAdminInitials(admin) }}</div>
                          <div>
                            <p class="admin-users-name">{{ admin.name }}</p>
                            <p class="admin-users-id">ID #{{ admin.id }}</p>
                          </div>
                        </div>
                      </td>
                      <td class="py-3">{{ admin.email }}</td>
                      <td class="py-3">
                        <span class="status-pill" :class="admin.role === 'super_admin' ? 'is-info' : 'is-neutral'">
                          {{ admin.role }}
                        </span>
                      </td>
                      <td class="py-3 text-base">{{ formatServiceAssignments(admin) }}</td>
                      <td class="py-3">{{ formatDate(admin.created_at) }}</td>
                      <td class="py-3 text-right">
                        <button class="admin-users-action" type="button" @click="openEditAdminModal(admin)">
                          Edit
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <p v-if="adminError" class="mt-3 text-base text-[#C0392B]">{{ adminError }}</p>
              </div>
              <div class="admin-users-pagination">
                <span class="admin-users-range">{{ adminRangeLabel }}</span>
                <div class="admin-users-page-controls">
                  <button class="resident-tertiary" type="button" :disabled="adminPage === 1" @click="previousAdminPage">
                    Prev
                  </button>
                  <span class="admin-users-page-label">Page {{ adminPage }} of {{ adminTotalPages }}</span>
                  <button
                    class="resident-tertiary"
                    type="button"
                    :disabled="adminPage === adminTotalPages"
                    @click="nextAdminPage"
                  >
                    Next
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-if="isAdminModalOpen" class="modal-overlay" @click.self="closeAdminModal">
            <div class="modal-card" role="dialog" aria-modal="true">
              <div class="modal-header">
                <div>
                  <h3>Edit Admin</h3>
                  <p>Update admin details or service assignments.</p>
                </div>
                <button class="modal-close" type="button" @click="closeAdminModal">Close</button>
              </div>
              <form class="modal-body" @submit.prevent="submitAdminForm">
                <div class="modal-grid">
                  <label class="modal-field">
                    <span>Name</span>
                    <input v-model="adminForm.name" type="text" required />
                  </label>
                  <label class="modal-field">
                    <span>Email</span>
                    <input v-model="adminForm.email" type="email" required />
                  </label>
                  <label class="modal-field">
                    <span>Role</span>
                    <select v-model="adminForm.role">
                      <option value="staff_admin">staff_admin</option>
                      <option value="super_admin">super_admin</option>
                    </select>
                  </label>
                  <label class="modal-field">
                    <span>Password</span>
                    <input v-model="adminForm.password" type="password" autocomplete="new-password" />
                    <small>Leave blank to keep the existing password.</small>
                  </label>
                  <label class="modal-field modal-full">
                    <span>Service IDs</span>
                    <input v-model="adminForm.service_ids" type="text" />
                    <small>Comma-separated IDs for staff admins.</small>
                  </label>
                </div>
                <p v-if="adminFormError" class="modal-error">{{ adminFormError }}</p>
                <div class="modal-actions">
                  <button class="resident-tertiary" type="button" @click="closeAdminModal">Cancel</button>
                  <button class="resident-primary" type="submit" :disabled="isAdminSaving">
                    {{ isAdminSaving ? 'Saving...' : 'Save Changes' }}
                  </button>
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div v-if="isLogoutModalOpen" class="modal-overlay" @click.self="closeLogoutModal">
      <div class="modal-card is-logout" role="dialog" aria-modal="true">
        <div class="modal-header">
          <div>
            <h3>Sign Out</h3>
            <p>Confirm you want to end this admin session.</p>
          </div>
          <button class="modal-close" type="button" @click="closeLogoutModal">Close</button>
        </div>
        <div class="modal-body logout-modal-body">
          <div class="logout-modal-hero">
            <div class="logout-modal-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none">
                <path
                  d="M15 8V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-2"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                />
                <path d="M10 12h9m0 0-3-3m3 3-3 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            <div>
              <p class="logout-modal-title">You’re About to Sign Out.</p>
              <p class="logout-modal-subtitle">Make sure any pending actions are saved before leaving.</p>
            </div>
          </div>
          <div class="modal-actions logout-modal-actions">
            <button class="resident-tertiary" type="button" @click="closeLogoutModal">Stay signed in</button>
            <button class="resident-danger" type="button" @click="confirmLogout">Sign Out Now</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { baseUrl } from '../api'
import {
  getResidents,
  createResident as apiCreateResident,
  updateResident as apiUpdateResident,
  deleteResident as apiDeleteResident,
  getServices,
  createService as apiCreateService,
  updateService as apiUpdateService,
  getAdmins,
  createAdmin as apiCreateAdmin,
  updateAdmin as apiUpdateAdmin,
  getKiosks,
  createKiosk as apiCreateKiosk,
  getQueue,
  queueNext,
  queueCall,
  queueServe,
  queueCancel,
  getAuditLogs,
  getTimingAnalytics,
} from '../adminApi'

const router = useRouter()
const profile = JSON.parse(localStorage.getItem('admin_profile') || 'null')
const isSuperAdmin = computed(() => profile?.role === 'super_admin')
const adminInitials = computed(() => {
  if (!profile?.name) return 'AD'
  return profile.name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0].toUpperCase())
    .join('')
})
const resolveIdUrl = (url) => {
  if (!url) return ''
  if (url.startsWith('data:')) return url
  if (url.startsWith('http://') || url.startsWith('https://')) return url
  return `${baseUrl}${url}`
}

const isImageUrl = (url) => /\.(png|jpe?g|gif|webp|bmp)$/i.test(url || '')
const activeSection = ref('dashboard')
let dockObserver = null
let hashListener = null
const residents = ref([])
const isLoadingResidents = ref(false)
const residentError = ref('')
const statusFilter = ref('')
const search = ref('')
const residentPage = ref(1)
const residentPageSize = 10
const isResidentModalOpen = ref(false)
const residentModalMode = ref('create')
const residentForm = ref({
  username: '',
  first_name: '',
  middle_name: '',
  last_name: '',
  date_of_birth: '',
  gender: '',
  civil_status: '',
  mobile_number: '',
  address: '',
  email: '',
  status: 'pending',
  password: '',
  profile_photo_url: '',
})
const residentFormError = ref('')
const isResidentSaving = ref(false)
const residentTarget = ref(null)
const isDeleteResidentOpen = ref(false)
const residentDeleteTarget = ref(null)
const deleteResidentError = ref('')
const isDeletingResident = ref(false)
const services = ref([])
const isLoadingServices = ref(false)
const serviceError = ref('')
const serviceSearch = ref('')
const serviceStatusFilter = ref('all')
const servicePage = ref(1)
const servicePageSize = 10
const isServiceModalOpen = ref(false)
const serviceModalMode = ref('create')
const serviceForm = ref({ name: '', code: '', active: 1 })
const serviceFormError = ref('')
const isServiceSaving = ref(false)
const serviceTarget = ref(null)
const kiosks = ref([])
const isLoadingKiosks = ref(false)
const kioskError = ref('')
const newKiosk = ref({ device_id: '', name: '' })
const kioskSearch = ref('')
const kioskStatusFilter = ref('all')
const kioskSortKey = ref('last_seen_at')
const kioskSortDirection = ref('desc')
const kioskPage = ref(1)
const kioskPageSize = 10
const queueTickets = ref([])
const isLoadingQueue = ref(false)
const queueError = ref('')
const queueServiceId = ref('')
const queueStatus = ref('')
const queueSearch = ref('')
const queueSortKey = ref('smart')
const queueSortDirection = ref('asc')
const queuePage = ref(1)
const queuePageSize = 10
const callNextServiceId = ref('auto')
const manualNextTicketId = ref('')
const activeCallTicket = ref(null)
const transactions = ref([])
const isLoadingTransactions = ref(false)
const transactionError = ref('')
const transactionServiceId = ref('')
const transactionStatus = ref('done')
const transactionSearch = ref('')
const transactionSortKey = ref('issued_at')
const transactionSortDirection = ref('desc')
const transactionPage = ref(1)
const transactionPageSize = 10
const auditLogs = ref([])
const isLoadingLogs = ref(false)
const auditError = ref('')
const auditSearch = ref('')
const auditActorFilter = ref('all')
const auditSortKey = ref('created_at')
const auditSortDirection = ref('desc')
const auditPage = ref(1)
const auditPageSize = 10
const admins = ref([])
const isLoadingAdmins = ref(false)
const adminError = ref('')
const adminSearch = ref('')
const adminRoleFilter = ref('all')
const adminSortKey = ref('created_at')
const adminSortDirection = ref('desc')
const adminPage = ref(1)
const adminPageSize = 10
const isAdminModalOpen = ref(false)
const isLogoutModalOpen = ref(false)
const adminForm = ref({ name: '', email: '', role: 'staff_admin', password: '', service_ids: '' })
const adminFormError = ref('')
const isAdminSaving = ref(false)
const adminTarget = ref(null)
const newAdmin = ref({
  name: '',
  email: '',
  password: '',
  role: 'staff_admin',
  service_ids: '',
})
const allResidents = ref([])
const allQueueTickets = ref([])
const analyticsRange = ref('30d')
const analyticsServiceId = ref('')
const analyticsStatus = ref('all')
const analyticsStartDate = ref('')
const analyticsEndDate = ref('')
const timingAnalytics = ref({})
const timingLoading = ref(false)
const timingError = ref('')
const lastUpdatedAt = ref(new Date())
const phTimeLabel = ref('')
const phTimeTimer = ref(null)
const queueRefreshTimer = ref(null)
const isQueueAutoRefreshing = ref(false)
const queueMonitorFrame = ref(null)
const queueMonitorObserver = ref(null)
const queueMonitorScale = ref(1)
const queueMonitorOffset = ref({ x: 0, y: 0 })

const lastUpdatedLabel = computed(() => {
  if (!lastUpdatedAt.value) return 'Just Now'
  return lastUpdatedAt.value.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true })
})

const genderOptions = ['Male', 'Female', 'Non-binary', 'Prefer not to say']
const civilStatusOptions = ['Single', 'Married', 'Separated', 'Widowed']

const residentModalTitle = computed(() =>
  residentModalMode.value === 'create' ? 'Create Resident' : 'Manage Resident'
)

const residentSubmitLabel = computed(() =>
  residentModalMode.value === 'create' ? 'Create Resident' : 'Save Details'
)

const residentModalSubtitle = computed(() =>
  residentModalMode.value === 'create'
    ? 'Add a new resident profile to the directory.'
    : 'Review the profile details and decide the verification status.'
)

const residentModalName = computed(() => {
  return [residentForm.value.first_name, residentForm.value.middle_name, residentForm.value.last_name]
    .filter(Boolean)
    .join(' ')
})

const residentModalId = computed(() => {
  const id = residentTarget.value?.id
  if (!id && id !== 0) return 'ID Pending'
  return `BSM-RES-${String(id).padStart(6, '0')}`
})

const residentModalPhoto = computed(() => {
  const url = residentForm.value.profile_photo_url || residentTarget.value?.profile_photo_url || ''
  return resolveIdUrl(url) || '/favicon.png'
})

const serviceModalTitle = computed(() =>
  serviceModalMode.value === 'create' ? 'Create Service' : 'Update Service'
)

const serviceSubmitLabel = computed(() =>
  serviceModalMode.value === 'create' ? 'Create Service' : 'Save Changes'
)

const residentTotalPages = computed(() =>
  Math.max(1, Math.ceil(residents.value.length / residentPageSize))
)

const residentPageRows = computed(() => {
  const start = (residentPage.value - 1) * residentPageSize
  return residents.value.slice(start, start + residentPageSize)
})

const residentRangeLabel = computed(() => {
  const total = residents.value.length
  if (!total) return 'No residents to display'
  const start = (residentPage.value - 1) * residentPageSize + 1
  const end = Math.min(total, start + residentPageSize - 1)
  return `Showing ${start}-${end} of ${total} residents`
})

const serviceFiltered = computed(() => {
  const term = serviceSearch.value.trim().toLowerCase()
  return services.value.filter((service) => {
    const matchesStatus =
      serviceStatusFilter.value === 'all' ||
      (serviceStatusFilter.value === 'active' && service.active) ||
      (serviceStatusFilter.value === 'inactive' && !service.active)
    if (!matchesStatus) return false
    if (!term) return true
    const name = String(service.name || '').toLowerCase()
    const code = String(service.code || '').toLowerCase()
    return name.includes(term) || code.includes(term)
  })
})

const serviceRangeLabel = computed(() => {
  const total = services.value.length
  const visible = serviceFiltered.value.length
  if (!visible) return 'No services to display'
  const start = (servicePage.value - 1) * servicePageSize + 1
  const end = Math.min(visible, start + servicePageSize - 1)
  if (total === visible) return `Showing ${start}-${end} of ${total} services`
  return `Showing ${start}-${end} of ${visible} services (total ${total})`
})

const serviceTotalPages = computed(() =>
  Math.max(1, Math.ceil(serviceFiltered.value.length / servicePageSize))
)

const servicePageRows = computed(() => {
  const start = (servicePage.value - 1) * servicePageSize
  return serviceFiltered.value.slice(start, start + servicePageSize)
})

const queueFilteredTickets = computed(() => {
  const term = queueSearch.value.trim().toLowerCase()
  return queueTickets.value.filter((ticket) => {
    if (queueServiceId.value) {
      const serviceId = parseInt(queueServiceId.value, 10)
      const serviceMatch = resolveTicketServices(ticket).some((service) => service.id === serviceId)
      if (!serviceMatch) return false
    }
    if (queueStatus.value && ticket.status !== queueStatus.value) return false
    if (!term) return true
    const ticketNo = String(ticket.ticket_no || '').toLowerCase()
    const residentId = String(ticket.resident_id || '').toLowerCase()
    const residentName = formatTicketResident(ticket).toLowerCase()
    const residentEmail = String(ticket.resident_email || '').toLowerCase()
    const residentUsername = String(ticket.resident_username || '').toLowerCase()
    const serviceEntries = resolveTicketServices(ticket)
    const serviceLabel = serviceEntries
      .map((service) => service.name || serviceName(service.id))
      .join(' ')
      .toLowerCase()
    const serviceCodes = serviceEntries
      .map((service) => service.code || serviceCode(service.id))
      .join(' ')
      .toLowerCase()
    return (
      ticketNo.includes(term) ||
      residentId.includes(term) ||
      residentName.includes(term) ||
      residentEmail.includes(term) ||
      residentUsername.includes(term) ||
      serviceLabel.includes(term) ||
      serviceCodes.includes(term)
    )
  })
})

const queueSortedTickets = computed(() => {
  const items = [...queueFilteredTickets.value]
  const direction = queueSortDirection.value === 'desc' ? -1 : 1
  const statusPriority = {
    serving: 0,
    waiting: 1,
    done: 2,
    cancelled: 3,
  }
  const compareValues = (a, b) => (a > b ? 1 : a < b ? -1 : 0)

  items.sort((a, b) => {
    if (queueSortKey.value === 'smart') {
      const statusDiff = compareValues(statusPriority[a.status] ?? 99, statusPriority[b.status] ?? 99)
      if (statusDiff !== 0) return statusDiff * direction
      const timeDiff = compareValues(new Date(a.issued_at || 0).getTime(), new Date(b.issued_at || 0).getTime())
      if (timeDiff !== 0) return timeDiff * direction
      return compareValues(String(a.ticket_no || ''), String(b.ticket_no || '')) * direction
    }
    if (queueSortKey.value === 'status') {
      const statusDiff = compareValues(statusPriority[a.status] ?? 99, statusPriority[b.status] ?? 99)
      if (statusDiff !== 0) return statusDiff * direction
    }
    if (queueSortKey.value === 'service_id') {
      const serviceDiff = compareValues(ticketPrimaryServiceId(a), ticketPrimaryServiceId(b))
      if (serviceDiff !== 0) return serviceDiff * direction
    }
    if (queueSortKey.value === 'ticket_no') {
      const ticketDiff = compareValues(String(a.ticket_no || ''), String(b.ticket_no || ''))
      if (ticketDiff !== 0) return ticketDiff * direction
    }
    if (queueSortKey.value === 'issued_at') {
      const timeDiff = compareValues(new Date(a.issued_at || 0).getTime(), new Date(b.issued_at || 0).getTime())
      if (timeDiff !== 0) return timeDiff * direction
    }
    const fallback = compareValues(new Date(a.issued_at || 0).getTime(), new Date(b.issued_at || 0).getTime())
    return fallback * direction
  })
  return items
})

const queueTotalPages = computed(() =>
  Math.max(1, Math.ceil(queueSortedTickets.value.length / queuePageSize))
)

const queuePageRows = computed(() => {
  const start = (queuePage.value - 1) * queuePageSize
  return queueSortedTickets.value.slice(start, start + queuePageSize)
})

const queueRangeLabel = computed(() => {
  const total = queueSortedTickets.value.length
  if (!total) return 'No tickets to display'
  const start = (queuePage.value - 1) * queuePageSize + 1
  const end = Math.min(total, start + queuePageSize - 1)
  return `Showing ${start}-${end} of ${total} tickets`
})

const transactionCounts = computed(() => {
  const counts = { total: transactions.value.length, done: 0, cancelled: 0 }
  transactions.value.forEach((ticket) => {
    if (ticket.status === 'done') counts.done += 1
    if (ticket.status === 'cancelled') counts.cancelled += 1
  })
  return counts
})

const transactionFiltered = computed(() => {
  const term = transactionSearch.value.trim().toLowerCase()
  return transactions.value.filter((ticket) => {
    if (!term) return true
    const ticketNo = String(ticket.ticket_no || '').toLowerCase()
    const serviceId = String(ticket.service_id || '').toLowerCase()
    const status = String(ticket.status || '').toLowerCase()
    return ticketNo.includes(term) || serviceId.includes(term) || status.includes(term)
  })
})

const transactionSorted = computed(() => {
  const items = [...transactionFiltered.value]
  const direction = transactionSortDirection.value === 'desc' ? -1 : 1
  const statusPriority = { done: 0, cancelled: 1 }
  const compareValues = (a, b) => (a > b ? 1 : a < b ? -1 : 0)

  items.sort((a, b) => {
    if (transactionSortKey.value === 'status') {
      const statusDiff = compareValues(statusPriority[a.status] ?? 99, statusPriority[b.status] ?? 99)
      if (statusDiff !== 0) return statusDiff * direction
    }
    if (transactionSortKey.value === 'service_id') {
      const serviceDiff = compareValues(a.service_id || 0, b.service_id || 0)
      if (serviceDiff !== 0) return serviceDiff * direction
    }
    if (transactionSortKey.value === 'ticket_no') {
      const ticketDiff = compareValues(String(a.ticket_no || ''), String(b.ticket_no || ''))
      if (ticketDiff !== 0) return ticketDiff * direction
    }
    if (transactionSortKey.value === 'issued_at') {
      const timeDiff = compareValues(new Date(a.issued_at || 0).getTime(), new Date(b.issued_at || 0).getTime())
      if (timeDiff !== 0) return timeDiff * direction
    }
    const fallback = compareValues(new Date(a.issued_at || 0).getTime(), new Date(b.issued_at || 0).getTime())
    return fallback * direction
  })
  return items
})

const transactionTotalPages = computed(() =>
  Math.max(1, Math.ceil(transactionSorted.value.length / transactionPageSize))
)

const transactionPageRows = computed(() => {
  const start = (transactionPage.value - 1) * transactionPageSize
  return transactionSorted.value.slice(start, start + transactionPageSize)
})

const transactionRangeLabel = computed(() => {
  const total = transactionSorted.value.length
  if (!total) return 'No transactions to display'
  const start = (transactionPage.value - 1) * transactionPageSize + 1
  const end = Math.min(total, start + transactionPageSize - 1)
  return `Showing ${start}-${end} of ${total} transactions`
})

const auditCounts = computed(() => {
  const counts = { total: auditLogs.value.length, admin: 0, kiosk: 0, resident: 0 }
  auditLogs.value.forEach((log) => {
    if (log.actor_type === 'admin') counts.admin += 1
    if (log.actor_type === 'kiosk') counts.kiosk += 1
    if (log.actor_type === 'resident') counts.resident += 1
  })
  return counts
})

const auditFiltered = computed(() => {
  const term = auditSearch.value.trim().toLowerCase()
  return auditLogs.value.filter((log) => {
    const matchesActor = auditActorFilter.value === 'all' || log.actor_type === auditActorFilter.value
    if (!matchesActor) return false
    if (!term) return true
    const action = String(log.action || '').toLowerCase()
    const actorType = String(log.actor_type || '').toLowerCase()
    const actorId = String(log.actor_id || '').toLowerCase()
    return action.includes(term) || actorType.includes(term) || actorId.includes(term)
  })
})

const auditSorted = computed(() => {
  const items = [...auditFiltered.value]
  const direction = auditSortDirection.value === 'desc' ? -1 : 1
  const compareValues = (a, b) => (a > b ? 1 : a < b ? -1 : 0)
  items.sort((a, b) => {
    if (auditSortKey.value === 'action') {
      const actionDiff = compareValues(String(a.action || ''), String(b.action || ''))
      if (actionDiff !== 0) return actionDiff * direction
    }
    if (auditSortKey.value === 'actor_type') {
      const actorDiff = compareValues(String(a.actor_type || ''), String(b.actor_type || ''))
      if (actorDiff !== 0) return actorDiff * direction
    }
    if (auditSortKey.value === 'created_at') {
      const timeDiff = compareValues(new Date(a.created_at || 0).getTime(), new Date(b.created_at || 0).getTime())
      if (timeDiff !== 0) return timeDiff * direction
    }
    const fallback = compareValues(new Date(a.created_at || 0).getTime(), new Date(b.created_at || 0).getTime())
    return fallback * direction
  })
  return items
})

const auditTotalPages = computed(() =>
  Math.max(1, Math.ceil(auditSorted.value.length / auditPageSize))
)

const auditPageRows = computed(() => {
  const start = (auditPage.value - 1) * auditPageSize
  return auditSorted.value.slice(start, start + auditPageSize)
})

const auditRangeLabel = computed(() => {
  const total = auditSorted.value.length
  if (!total) return 'No logs to display'
  const start = (auditPage.value - 1) * auditPageSize + 1
  const end = Math.min(total, start + auditPageSize - 1)
  return `Showing ${start}-${end} of ${total} logs`
})

const auditLatestLabel = computed(() => {
  if (!auditLogs.value.length) return '--'
  const latest = auditLogs.value.reduce((best, log) => {
    if (!best) return log
    const bestTime = new Date(best.created_at || 0).getTime()
    const logTime = new Date(log.created_at || 0).getTime()
    return logTime > bestTime ? log : best
  }, null)
  if (!latest?.created_at) return '--'
  return `${formatDate(latest.created_at)} ${formatTime(latest.created_at)}`
})

const adminCounts = computed(() => {
  const counts = { total: admins.value.length, super: 0, staff: 0 }
  admins.value.forEach((admin) => {
    if (admin.role === 'super_admin') counts.super += 1
    if (admin.role === 'staff_admin') counts.staff += 1
  })
  return counts
})

const adminFiltered = computed(() => {
  const term = adminSearch.value.trim().toLowerCase()
  return admins.value.filter((admin) => {
    const matchesRole = adminRoleFilter.value === 'all' || admin.role === adminRoleFilter.value
    if (!matchesRole) return false
    if (!term) return true
    const name = String(admin.name || '').toLowerCase()
    const email = String(admin.email || '').toLowerCase()
    return name.includes(term) || email.includes(term)
  })
})

const adminSorted = computed(() => {
  const items = [...adminFiltered.value]
  const direction = adminSortDirection.value === 'desc' ? -1 : 1
  const compareValues = (a, b) => (a > b ? 1 : a < b ? -1 : 0)
  items.sort((a, b) => {
    if (adminSortKey.value === 'name') {
      const nameDiff = compareValues(String(a.name || ''), String(b.name || ''))
      if (nameDiff !== 0) return nameDiff * direction
    }
    if (adminSortKey.value === 'role') {
      const roleDiff = compareValues(String(a.role || ''), String(b.role || ''))
      if (roleDiff !== 0) return roleDiff * direction
    }
    if (adminSortKey.value === 'created_at') {
      const timeDiff = compareValues(new Date(a.created_at || 0).getTime(), new Date(b.created_at || 0).getTime())
      if (timeDiff !== 0) return timeDiff * direction
    }
    const fallback = compareValues(String(a.email || ''), String(b.email || ''))
    return fallback * direction
  })
  return items
})

const adminTotalPages = computed(() =>
  Math.max(1, Math.ceil(adminSorted.value.length / adminPageSize))
)

const adminPageRows = computed(() => {
  const start = (adminPage.value - 1) * adminPageSize
  return adminSorted.value.slice(start, start + adminPageSize)
})

const adminRangeLabel = computed(() => {
  const total = adminSorted.value.length
  if (!total) return 'No admins to display'
  const start = (adminPage.value - 1) * adminPageSize + 1
  const end = Math.min(total, start + adminPageSize - 1)
  return `Showing ${start}-${end} of ${total} admins`
})

function serviceName(serviceId) {
  if (!serviceId) return 'Service unavailable'
  return services.value.find((service) => service.id === serviceId)?.name || `Service #${serviceId}`
}

function serviceCode(serviceId) {
  if (!serviceId) return ''
  return services.value.find((service) => service.id === serviceId)?.code || ''
}

function resolveTicketServices(ticket) {
  if (!ticket) return []
  const list = Array.isArray(ticket.services) ? ticket.services.filter(Boolean) : []
  if (list.length) return list
  if (ticket.service_id) {
    return [
      {
        id: ticket.service_id,
        name: serviceName(ticket.service_id),
        code: serviceCode(ticket.service_id),
      },
    ]
  }
  return []
}

function ticketPrimaryServiceId(ticket) {
  const services = resolveTicketServices(ticket)
  if (services.length) return services[0]?.id || ticket.service_id || 0
  return ticket?.service_id || 0
}

function ticketServiceLabel(ticket) {
  const services = resolveTicketServices(ticket)
  if (!services.length) return 'Service unavailable'
  const primaryName = services[0]?.name || serviceName(services[0]?.id || ticket?.service_id)
  if (services.length === 1) {
    return formatServiceName(primaryName)
  }
  return `${formatServiceName(primaryName)} + ${services.length - 1} more`
}

function ticketServiceCodeLabel(ticket) {
  const services = resolveTicketServices(ticket)
  if (!services.length) return ''
  if (services.length === 1) {
    return services[0]?.code || serviceCode(services[0]?.id || ticket?.service_id)
  }
  return `${services.length} services`
}

function ticketHasMultipleServices(ticket) {
  return resolveTicketServices(ticket).length > 1
}

function ticketServiceList(ticket) {
  const services = resolveTicketServices(ticket)
  if (!services.length) return ''
  return services
    .map((service) => formatServiceName(service.name || serviceName(service.id)))
    .join(', ')
}

function formatTicketResident(ticket) {
  if (!ticket) return 'Resident'
  const name = [ticket.resident_first_name, ticket.resident_middle_name, ticket.resident_last_name]
    .filter(Boolean)
    .join(' ')
    .trim()
  if (name) return name
  return ticket.resident_id ? `Resident #${ticket.resident_id}` : 'Resident'
}

const queueWaitingList = computed(() => {
  const scope = callNextServiceId.value
  return queueTickets.value
    .filter((ticket) => ticket.status === 'waiting')
    .filter((ticket) => {
      if (!scope || scope === 'auto') return true
      const serviceId = parseInt(scope, 10)
      return resolveTicketServices(ticket).some((service) => service.id === serviceId)
    })
    .sort((a, b) => new Date(a.issued_at || 0).getTime() - new Date(b.issued_at || 0).getTime())
})

const queueWaitingTotal = computed(() =>
  queueTickets.value.filter((ticket) => ticket.status === 'waiting').length
)

const queueNextCandidate = computed(() => queueWaitingList.value[0] || null)

const manualNextTicket = computed(() => {
  const selectedId = parseInt(manualNextTicketId.value, 10)
  if (!selectedId) return null
  return queueWaitingList.value.find((ticket) => ticket.id === selectedId) || null
})

const queueDecisionNote = computed(() => {
  if (!queueWaitingList.value.length) {
    return callNextServiceId.value === 'auto'
      ? 'No waiting tickets yet. Auto will call the earliest ticket once available.'
      : 'No waiting tickets for the selected service yet.'
  }
  return callNextServiceId.value === 'auto'
    ? 'Auto will call the earliest waiting ticket across all services.'
    : 'Calling uses the earliest waiting ticket by issued time, matching the queue monitor.'
})

watch(queueWaitingList, (list) => {
  if (!manualNextTicketId.value) return
  const selectedId = parseInt(manualNextTicketId.value, 10)
  if (!selectedId || !list.some((ticket) => ticket.id === selectedId)) {
    manualNextTicketId.value = ''
  }
})

const startOfDay = (date) => new Date(date.getFullYear(), date.getMonth(), date.getDate())

const endOfDay = (date) =>
  new Date(date.getFullYear(), date.getMonth(), date.getDate(), 23, 59, 59, 999)

const addDays = (date, days) =>
  new Date(date.getFullYear(), date.getMonth(), date.getDate() + days)

const parseDateInput = (value) => {
  if (!value) return null
  const date = new Date(`${value}T00:00:00`)
  return Number.isNaN(date.getTime()) ? null : date
}

const isDateInRange = (value, range) => {
  if (!value || !range) return false
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return false
  return date >= range.start && date <= range.end
}

const analyticsDateRange = computed(() => {
  const today = new Date()
  const endToday = endOfDay(today)
  if (analyticsRange.value === 'all') {
    return { start: null, end: null }
  }
  if (analyticsRange.value === 'today') {
    return { start: startOfDay(today), end: endToday }
  }
  if (analyticsRange.value === 'custom') {
    let start = parseDateInput(analyticsStartDate.value)
    let end = parseDateInput(analyticsEndDate.value)
    if (start) start = startOfDay(start)
    if (end) end = endOfDay(end)
    if (!end) end = endToday
    if (!start) start = startOfDay(end)
    if (start > end) {
      const swap = start
      start = startOfDay(end)
      end = endOfDay(swap)
    }
    return { start, end }
  }
  const rangeDays = analyticsRange.value === '90d' ? 90 : analyticsRange.value === '30d' ? 30 : 7
  const start = startOfDay(addDays(endToday, -(rangeDays - 1)))
  return { start, end: endToday }
})

const formatDateParam = (date) => {
  if (!date) return ''
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const formatDurationLabel = (minutes) => {
  if (minutes === null || minutes === undefined || Number.isNaN(minutes)) return '--'
  const totalSeconds = Math.max(0, Math.round(minutes * 60))
  const mins = Math.floor(totalSeconds / 60)
  const secs = totalSeconds % 60
  return `${mins}m ${secs}s`
}

const analyticsRangeLabel = computed(() => {
  if (analyticsRange.value === 'all') return 'All Time'
  if (analyticsRange.value === 'today') return 'Today'
  if (analyticsRange.value === '7d') return 'Last 7 Days'
  if (analyticsRange.value === '30d') return 'Last 30 Days'
  if (analyticsRange.value === '90d') return 'Last 90 Days'
  if (analyticsRange.value === 'custom') {
    if (!analyticsStartDate.value && !analyticsEndDate.value) return 'Custom Range'
    const startLabel = analyticsStartDate.value || 'Start'
    const endLabel = analyticsEndDate.value || 'Today'
    return `${startLabel} to ${endLabel}`
  }
  return 'Custom Range'
})

const analyticsFilterNote = computed(() => {
  const statusLabel =
    analyticsStatus.value === 'all'
      ? 'All Statuses'
      : analyticsStatus.value.charAt(0).toUpperCase() + analyticsStatus.value.slice(1)
  const serviceLabel = analyticsServiceId.value
    ? services.value.find((service) => service.id === parseInt(analyticsServiceId.value, 10))?.name ||
      `Service ${analyticsServiceId.value}`
    : 'All Services'
  return `${analyticsRangeLabel.value} | ${serviceLabel} | ${statusLabel}`
})

const analyticsTickets = computed(() => {
  const { start, end } = analyticsDateRange.value
  return allQueueTickets.value.filter((ticket) => {
    if (!ticket.issued_at) return false
    const issuedAt = new Date(ticket.issued_at)
    if (Number.isNaN(issuedAt.getTime())) return false
    if (start && issuedAt < start) return false
    if (end && issuedAt > end) return false
    if (analyticsServiceId.value) {
      const serviceId = parseInt(analyticsServiceId.value, 10)
      if (ticket.service_id !== serviceId) return false
    }
    if (analyticsStatus.value !== 'all' && ticket.status !== analyticsStatus.value) {
      return false
    }
    return true
  })
})

const kioskTimingStats = computed(() => timingAnalytics.value?.kiosk || {})
const ticketCycleStats = computed(() => timingAnalytics.value?.ticket_cycle || {})
const waitToServeStats = computed(() => timingAnalytics.value?.wait_to_serve || {})
const serviceDurationStats = computed(() => timingAnalytics.value?.service_duration || {})
const timingSeries = computed(() => timingAnalytics.value?.series || {})

const experienceTotalMinutes = computed(() => {
  const kiosk = kioskTimingStats.value.avg_minutes || 0
  const wait = waitToServeStats.value.avg_minutes || 0
  const serve = serviceDurationStats.value.avg_minutes || 0
  const cycle = ticketCycleStats.value.avg_minutes || 0
  const cycleTotal = cycle || wait + serve
  if (!kiosk && !cycleTotal) return 0
  return kiosk + cycleTotal
})

const experienceHasData = computed(() => experienceTotalMinutes.value > 0)

const experienceTotalLabel = computed(() =>
  experienceHasData.value ? formatDurationLabel(experienceTotalMinutes.value) : '--'
)

const experienceSegments = computed(() => {
  const kiosk = kioskTimingStats.value.avg_minutes || 0
  const wait = waitToServeStats.value.avg_minutes || 0
  const serve = serviceDurationStats.value.avg_minutes || 0
  const total = kiosk + wait + serve
  if (!total) {
    return { kiosk: 0, wait: 0, serve: 0 }
  }
  const kioskPercent = Math.round((kiosk / total) * 100)
  const waitPercent = Math.round((wait / total) * 100)
  const servePercent = Math.max(0, 100 - kioskPercent - waitPercent)
  return { kiosk: kioskPercent, wait: waitPercent, serve: servePercent }
})

const mapTimingSeries = (items = []) =>
  items.map((entry) => {
    const date = new Date(`${entry.date}T00:00:00`)
    const label = Number.isNaN(date.getTime())
      ? entry.date
      : date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' })
    return {
      label,
      value: Number(entry.avg_minutes || 0),
    }
  })

const kioskTimingSeries = computed(() => mapTimingSeries(timingSeries.value.kiosk_completion || []))
const ticketCycleSeries = computed(() => mapTimingSeries(timingSeries.value.ticket_cycle || []))

const kioskTimingTrend = computed(() => buildLineMetrics(kioskTimingSeries.value))
const kioskTimingAxis = computed(() => buildAxisLabels(kioskTimingSeries.value))
const ticketCycleTrend = computed(() => buildLineMetrics(ticketCycleSeries.value))
const ticketCycleAxis = computed(() => buildAxisLabels(ticketCycleSeries.value))

const kioskAvgLabel = computed(() => formatDurationLabel(kioskTimingStats.value.avg_minutes))
const kioskMedianLabel = computed(() => formatDurationLabel(kioskTimingStats.value.median_minutes))
const kioskP90Label = computed(() => formatDurationLabel(kioskTimingStats.value.p90_minutes))

const ticketCycleAvgLabel = computed(() => formatDurationLabel(ticketCycleStats.value.avg_minutes))
const ticketCycleMedianLabel = computed(() => formatDurationLabel(ticketCycleStats.value.median_minutes))
const ticketCycleP90Label = computed(() => formatDurationLabel(ticketCycleStats.value.p90_minutes))

const waitToServeLabel = computed(() => formatDurationLabel(waitToServeStats.value.avg_minutes))
const serviceDurationLabel = computed(() => formatDurationLabel(serviceDurationStats.value.avg_minutes))

const maxTimingSegment = computed(() =>
  Math.max(waitToServeStats.value.avg_minutes || 0, serviceDurationStats.value.avg_minutes || 0, 1)
)
const waitToServePercent = computed(() =>
  Math.round(((waitToServeStats.value.avg_minutes || 0) / maxTimingSegment.value) * 100)
)
const serviceDurationPercent = computed(() =>
  Math.round(((serviceDurationStats.value.avg_minutes || 0) / maxTimingSegment.value) * 100)
)

const timingScopeNote = computed(() => {
  if (analyticsStatus.value && !['all', 'done'].includes(analyticsStatus.value)) {
    return 'Completion metrics are tracked for finished tickets only. Switch status to Done or All to see full timing.'
  }
  return ''
})

const kioskTimingInsight = computed(() => {
  const count = kioskTimingStats.value.count || 0
  if (!count) return 'No kiosk completion sessions in this range yet.'
  return `Based on ${count} kiosk session${count === 1 ? '' : 's'}, average completion is ${kioskAvgLabel.value}.`
})

const ticketTimingInsight = computed(() => {
  const count = ticketCycleStats.value.count || 0
  if (!count) return 'No completed tickets in this range yet.'
  return `Average ticket cycle time is ${ticketCycleAvgLabel.value} from issuance to completion.`
})

const queueBreakdownInsight = computed(() => {
  const wait = waitToServeStats.value.avg_minutes
  const serve = serviceDurationStats.value.avg_minutes
  if (!wait && !serve) return 'Queue breakdown metrics will appear once tickets are served and completed.'
  if (wait && serve && wait > serve) {
    return `Residents wait longer (${waitToServeLabel.value}) than service time (${serviceDurationLabel.value}).`
  }
  if (wait && serve) {
    return `Service time (${serviceDurationLabel.value}) is longer than the waiting phase (${waitToServeLabel.value}).`
  }
  return 'Queue timing split is building. Check back after more completions.'
})

const analyticsResidents = computed(() => {
  const { start, end } = analyticsDateRange.value
  return allResidents.value.filter((resident) => {
    if (!resident.created_at) return false
    const createdAt = new Date(resident.created_at)
    if (Number.isNaN(createdAt.getTime())) return false
    if (start && createdAt < start) return false
    if (end && createdAt > end) return false
    return true
  })
})

const residentStats = computed(() => {
  const stats = { total: allResidents.value.length, approved: 0, pending: 0, rejected: 0 }
  allResidents.value.forEach((resident) => {
    if (resident.status === 'approved') stats.approved += 1
    if (resident.status === 'pending') stats.pending += 1
    if (resident.status === 'rejected') stats.rejected += 1
  })
  return stats
})

const approvalRateLabel = computed(() => {
  const total = residentStats.value.total
  if (!total) return '0%'
  return `${Math.round((residentStats.value.approved / total) * 100)}%`
})

const residentPercentages = computed(() => {
  const total = residentStats.value.total || 1
  return {
    approved: Math.round((residentStats.value.approved / total) * 100),
    pending: Math.round((residentStats.value.pending / total) * 100),
    rejected: Math.round((residentStats.value.rejected / total) * 100),
  }
})

const queueStatusCounts = computed(() => {
  const counts = { waiting: 0, serving: 0, done: 0, cancelled: 0, total: allQueueTickets.value.length }
  allQueueTickets.value.forEach((ticket) => {
    if (ticket.status === 'waiting') counts.waiting += 1
    if (ticket.status === 'serving') counts.serving += 1
    if (ticket.status === 'done') counts.done += 1
    if (ticket.status === 'cancelled') counts.cancelled += 1
  })
  counts.total = allQueueTickets.value.length
  return counts
})

const queueStatusPercentages = computed(() => {
  const total = queueStatusCounts.value.total || 1
  return {
    waiting: Math.round((queueStatusCounts.value.waiting / total) * 100),
    serving: Math.round((queueStatusCounts.value.serving / total) * 100),
    done: Math.round((queueStatusCounts.value.done / total) * 100),
    cancelled: Math.round((queueStatusCounts.value.cancelled / total) * 100),
  }
})

const activeQueueCount = computed(() => queueStatusCounts.value.waiting + queueStatusCounts.value.serving)

const queueMonitorUrl = computed(() => {
  if (import.meta.env.VITE_QUEUE_MONITOR_URL) return import.meta.env.VITE_QUEUE_MONITOR_URL
  if (typeof window === 'undefined') return ''
  return `${window.location.origin}/queue-monitor/`
})

const queueMonitorBaseSize = { width: 1920, height: 1080 }

const updateQueueMonitorScale = () => {
  const frame = queueMonitorFrame.value
  if (!frame) return
  const { width, height } = frame.getBoundingClientRect()
  if (!width || !height) return
  const scale = Math.min(width / queueMonitorBaseSize.width, height / queueMonitorBaseSize.height, 1)
  queueMonitorScale.value = scale
  const scaledWidth = queueMonitorBaseSize.width * scale
  const scaledHeight = queueMonitorBaseSize.height * scale
  queueMonitorOffset.value = {
    x: Math.max(0, (width - scaledWidth) / 2),
    y: Math.max(0, (height - scaledHeight) / 2),
  }
}

const queueMonitorIframeStyle = computed(() => ({
  width: `${queueMonitorBaseSize.width}px`,
  height: `${queueMonitorBaseSize.height}px`,
  transform: `translate(${queueMonitorOffset.value.x}px, ${queueMonitorOffset.value.y}px) scale(${queueMonitorScale.value})`,
  transformOrigin: 'top left',
}))

const averageWaitMinutes = computed(() => {
  const now = Date.now()
  const waitingTickets = allQueueTickets.value.filter((ticket) => ticket.status === 'waiting' && ticket.issued_at)
  if (!waitingTickets.length) return 0
  const totalMinutes = waitingTickets.reduce((sum, ticket) => {
    const issuedAt = new Date(ticket.issued_at).getTime()
    if (!issuedAt) return sum
    const minutes = Math.max(0, (now - issuedAt) / 60000)
    return sum + minutes
  }, 0)
  return Math.round(totalMinutes / waitingTickets.length)
})

const averageWaitLabel = computed(() => {
  if (!queueStatusCounts.value.waiting) return '--'
  return `${averageWaitMinutes.value} min`
})

const queuePressurePercent = computed(() => {
  const scale = 20
  const value = activeQueueCount.value
  return Math.min(100, Math.round((value / scale) * 100))
})

const queuePressureNote = computed(() => {
  const active = activeQueueCount.value
  if (!active) return 'Queue is clear. Counters can focus on walk-ins.'
  if (averageWaitMinutes.value >= 30) return `Average wait is ${averageWaitMinutes.value} min. Consider opening another counter.`
  if (active >= 12) return 'High demand. Activate extra counters if available.'
  if (active >= 6) return 'Moderate load. Keep a steady serving pace.'
  return 'Light traffic. Keep monitoring the queue.'
})

const todayRange = computed(() => {
  const anchor = lastUpdatedAt.value ? new Date(lastUpdatedAt.value) : new Date()
  return {
    start: startOfDay(anchor),
    end: endOfDay(anchor),
  }
})

const todayTickets = computed(() => {
  const range = todayRange.value
  return allQueueTickets.value.filter((ticket) => isDateInRange(ticket.issued_at, range))
})

const todayQueueStatusCounts = computed(() => {
  const counts = { waiting: 0, serving: 0, done: 0, cancelled: 0, total: todayTickets.value.length }
  todayTickets.value.forEach((ticket) => {
    if (ticket.status === 'waiting') counts.waiting += 1
    if (ticket.status === 'serving') counts.serving += 1
    if (ticket.status === 'done') counts.done += 1
    if (ticket.status === 'cancelled') counts.cancelled += 1
  })
  counts.total = todayTickets.value.length
  return counts
})

const todayActiveQueueCount = computed(() => todayQueueStatusCounts.value.waiting + todayQueueStatusCounts.value.serving)

const todayAverageWaitMinutes = computed(() => {
  const range = todayRange.value
  const relevantLogs = auditLogs.value.filter((log) => {
    if (!log?.created_at) return false
    if (!isDateInRange(log.created_at, range)) return false
    return log.action === 'queue.updated' || log.action === 'queue.completed'
  })
  if (!relevantLogs.length) return null

  const completedEntries = []
  relevantLogs.forEach((log) => {
    const meta = log.meta_json && typeof log.meta_json === 'object' ? log.meta_json : {}
    if (log.action === 'queue.updated') {
      if (meta.status !== 'done') return
      completedEntries.push({
        completed_at: log.created_at,
        ticket_id: meta.ticket_id ? Number(meta.ticket_id) : null,
        ticket_no: meta.ticket_no || null,
      })
      return
    }
    if (log.action === 'queue.completed') {
      if (!meta.ticket_no) return
      completedEntries.push({
        completed_at: log.created_at,
        ticket_id: null,
        ticket_no: meta.ticket_no,
      })
    }
  })

  if (!completedEntries.length) return null

  const ticketsById = new Map()
  const ticketsByNo = new Map()
  allQueueTickets.value.forEach((ticket) => {
    if (ticket.id) ticketsById.set(ticket.id, ticket)
    if (ticket.ticket_no) ticketsByNo.set(ticket.ticket_no, ticket)
  })

  const completionTimes = []
  let fallbackDurationMinutes = null
  completedEntries.forEach((entry) => {
    let ticket = null
    if (entry.ticket_id && ticketsById.has(entry.ticket_id)) {
      ticket = ticketsById.get(entry.ticket_id)
    } else if (entry.ticket_no && ticketsByNo.has(entry.ticket_no)) {
      ticket = ticketsByNo.get(entry.ticket_no)
    }
    if (!ticket) return
    const completedAt = new Date(entry.completed_at)
    if (Number.isNaN(completedAt.getTime())) return
    completionTimes.push(completedAt.getTime())
    if (fallbackDurationMinutes === null && ticket.issued_at) {
      const issuedAt = new Date(ticket.issued_at)
      if (!Number.isNaN(issuedAt.getTime()) && completedAt.getTime() > issuedAt.getTime()) {
        fallbackDurationMinutes = (completedAt.getTime() - issuedAt.getTime()) / 60000
      }
    }
  })

  if (!completionTimes.length) return null

  completionTimes.sort((a, b) => a - b)
  let totalMinutes = 0
  let countMinutes = 0
  const maxGapMinutes = 30
  for (let i = 1; i < completionTimes.length; i += 1) {
    const gapMinutes = (completionTimes[i] - completionTimes[i - 1]) / 60000
    if (gapMinutes <= 0 || gapMinutes > maxGapMinutes) continue
    totalMinutes += gapMinutes
    countMinutes += 1
  }
  if (countMinutes > 0) return Math.round((totalMinutes / countMinutes) * 10) / 10
  if (fallbackDurationMinutes !== null) return Math.round(fallbackDurationMinutes * 10) / 10
  return null
})

const todayAverageWaitLabel = computed(() => {
  if (todayAverageWaitMinutes.value === null || todayAverageWaitMinutes.value === undefined) return '--'
  return formatDurationLabel(todayAverageWaitMinutes.value)
})

const todayQueuePressurePercent = computed(() => {
  const scale = 20
  const value = todayActiveQueueCount.value
  return Math.min(100, Math.round((value / scale) * 100))
})

const todayQueuePressureNote = computed(() => {
  const active = todayActiveQueueCount.value
  if (!active) return 'Queue is clear today. Counters can focus on walk-ins.'
  if (todayAverageWaitMinutes.value !== null && todayAverageWaitMinutes.value >= 30) {
    return `Average wait is ${formatDurationLabel(todayAverageWaitMinutes.value)} today. Consider opening another counter.`
  }
  if (active >= 12) return 'High demand today. Activate extra counters if available.'
  if (active >= 6) return 'Moderate load today. Keep a steady serving pace.'
  return 'Light traffic today. Keep monitoring the queue.'
})

const activeServiceCount = computed(() => services.value.filter((service) => service.active).length)

const inactiveServiceCount = computed(() => services.value.filter((service) => !service.active).length)

const serviceDemand = computed(() => {
  const counts = new Map()
  allQueueTickets.value.forEach((ticket) => {
    const serviceId = ticket.service_id
    if (!serviceId) return
    counts.set(serviceId, (counts.get(serviceId) || 0) + 1)
  })
  const items = Array.from(counts.entries())
    .map(([id, count]) => {
      const service = services.value.find((entry) => entry.id === id)
      return {
        id,
        count,
        name: service?.name || `Service #${id}`,
        code: service?.code || '',
      }
    })
    .sort((a, b) => b.count - a.count)
    .slice(0, 5)
  const maxCount = Math.max(...items.map((item) => item.count), 1)
  return items.map((item) => ({
    ...item,
    percent: Math.round((item.count / maxCount) * 100),
  }))
})

const serviceVolumeBars = computed(() => {
  const counts = new Map()
  analyticsTickets.value.forEach((ticket) => {
    const serviceId = ticket.service_id
    if (!serviceId) return
    counts.set(serviceId, (counts.get(serviceId) || 0) + 1)
  })
  if (!counts.size) return []
  const items = Array.from(counts.entries())
    .map(([id, count]) => {
      const service = services.value.find((entry) => entry.id === id)
      return {
        id,
        count,
        name: service?.name || `Service #${id}`,
      }
    })
    .sort((a, b) => b.count - a.count)
    .slice(0, 6)
  const maxCount = Math.max(...items.map((item) => item.count), 1)
  return items.map((item) => ({
    ...item,
    percent: Math.round((item.count / maxCount) * 100),
  }))
})

const analyticsTopService = computed(() => serviceVolumeBars.value[0] || null)

const analyticsTopServiceLabel = computed(() => {
  if (!analyticsTopService.value) return 'No Demand Yet'
  return analyticsTopService.value.name
})

const analyticsTopServiceTitle = computed(() => formatServiceName(analyticsTopServiceLabel.value))

const analyticsTopServiceCountLabel = computed(() => {
  if (!analyticsTopService.value) return 'Waiting for Tickets'
  return `${analyticsTopService.value.count} tickets`
})

const analyticsReportFilename = (extension) => {
  const stamp = new Date()
    .toISOString()
    .replace(/[-:]/g, '')
    .replace(/\.\d+Z$/, '')
  return `admin-analytics-report-${stamp}.${extension}`
}

const csvEscape = (value) => {
  const text = value === null || value === undefined ? '' : String(value)
  if (text.includes('"') || text.includes(',') || text.includes('\n')) {
    return `"${text.replace(/"/g, '""')}"`
  }
  return text
}

const buildCsvSection = (rows) => rows.map((row) => row.map(csvEscape).join(',')).join('\n')

const downloadAnalyticsCsv = () => {
  const generatedAt = formatReportTimestamp(new Date())
  const rows = []
  rows.push(['Report', 'Admin Analytics Dashboard'])
  rows.push(['Generated At', generatedAt])
  rows.push(['Filters', analyticsFilterNote.value])
  rows.push([])
  rows.push(['Timing Summary'])
  rows.push(['Metric', 'Average', 'Median', 'P90', 'Min', 'Max', 'Count'])
  rows.push([
    'Kiosk Completion',
    kioskTimingStats.value.avg_minutes ?? '',
    kioskTimingStats.value.median_minutes ?? '',
    kioskTimingStats.value.p90_minutes ?? '',
    kioskTimingStats.value.min_minutes ?? '',
    kioskTimingStats.value.max_minutes ?? '',
    kioskTimingStats.value.count ?? '',
  ])
  rows.push([
    'Ticket Cycle',
    ticketCycleStats.value.avg_minutes ?? '',
    ticketCycleStats.value.median_minutes ?? '',
    ticketCycleStats.value.p90_minutes ?? '',
    ticketCycleStats.value.min_minutes ?? '',
    ticketCycleStats.value.max_minutes ?? '',
    ticketCycleStats.value.count ?? '',
  ])
  rows.push([
    'Wait To Serve',
    waitToServeStats.value.avg_minutes ?? '',
    waitToServeStats.value.median_minutes ?? '',
    waitToServeStats.value.p90_minutes ?? '',
    waitToServeStats.value.min_minutes ?? '',
    waitToServeStats.value.max_minutes ?? '',
    waitToServeStats.value.count ?? '',
  ])
  rows.push([
    'Service Duration',
    serviceDurationStats.value.avg_minutes ?? '',
    serviceDurationStats.value.median_minutes ?? '',
    serviceDurationStats.value.p90_minutes ?? '',
    serviceDurationStats.value.min_minutes ?? '',
    serviceDurationStats.value.max_minutes ?? '',
    serviceDurationStats.value.count ?? '',
  ])
  rows.push([])
  rows.push(['Daily Ticket Volume'])
  rows.push(['Date', 'Tickets'])
  ticketTrendSeries.value.forEach((entry) => rows.push([entry.label, entry.value]))
  rows.push([])
  rows.push(['Ticket Status Mix'])
  rows.push(['Status', 'Count', 'Percent'])
  ticketStatusMix.value.forEach((entry) => rows.push([entry.label, entry.count, entry.percent]))
  rows.push([])
  rows.push(['Most Booked Service'])
  rows.push(['Service', 'Count', 'Percent'])
  serviceVolumeBars.value.forEach((entry) => rows.push([formatServiceName(entry.name), entry.count, entry.percent]))
  rows.push([])
  rows.push(['Service Mix'])
  rows.push(['Service', 'Count', 'Percent'])
  serviceMix.value.forEach((entry) => rows.push([formatServiceName(entry.label), entry.count, entry.percent]))
  rows.push([])
  rows.push(['Traffic By Time'])
  rows.push(['Window', 'Tickets'])
  trafficBuckets.value.forEach((entry) => rows.push([entry.label, entry.count]))
  rows.push([])
  rows.push(['Resident Registrations'])
  rows.push(['Date', 'Registrations'])
  residentTrendSeries.value.forEach((entry) => rows.push([entry.label, entry.value]))

  const csv = buildCsvSection(rows)
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = analyticsReportFilename('csv')
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(link.href)
}

const buildPdfRow = (label, value) => `
  <tr>
    <td>${escapeHtml(label)}</td>
    <td>${escapeHtml(value)}</td>
  </tr>
`

const buildPdfTable = (title, headers, rows) => `
  <div class="section">
    <h2>${escapeHtml(title)}</h2>
    <table>
      <thead>
        <tr>${headers.map((header) => `<th>${escapeHtml(header)}</th>`).join('')}</tr>
      </thead>
      <tbody>
        ${rows}
      </tbody>
    </table>
  </div>
`

const downloadAnalyticsPdf = () => {
  const reportWindow = window.open('', 'analytics-report')
  if (!reportWindow) {
    timingError.value = 'Unable to open the report window.'
    return
  }
  const generatedAt = formatReportTimestamp(new Date())
  const systemName = 'Automated Kiosk for Application Queuing System'
  const systemLocation = 'Barangay San Miguel, Pasig City'
  const preparedBy = profile?.name || 'Admin User'
  const preparedEmail = profile?.email || 'admin@barangay.local'
  const totalTickets = analyticsTickets.value.length
  const doneTickets = ticketStatusMix.value.find((item) => item.label === 'Done')?.count || 0
  const cancelledTickets = ticketStatusMix.value.find((item) => item.label === 'Cancelled')?.count || 0
  const topServiceName = analyticsTopServiceTitle.value || '—'
  const topServiceCount = analyticsTopService.value?.count || 0
  const serviceLabel = analyticsServiceId.value
    ? services.value.find((service) => service.id === parseInt(analyticsServiceId.value, 10))?.name ||
      `Service ${analyticsServiceId.value}`
    : 'All Services'
  const statusLabel =
    analyticsStatus.value === 'all'
      ? 'All Statuses'
      : analyticsStatus.value.charAt(0).toUpperCase() + analyticsStatus.value.slice(1)

  const summaryRows = [
    buildPdfRow('System', systemName),
    buildPdfRow('Location', systemLocation),
    buildPdfRow('Prepared By', `${preparedBy} (${preparedEmail})`),
    buildPdfRow('Generated At', generatedAt),
    buildPdfRow('Range', analyticsRangeLabel.value),
    buildPdfRow('Service Filter', serviceLabel),
    buildPdfRow('Status Filter', statusLabel),
  ].join('')

  const highlightRows = [
    buildPdfRow('Total Tickets', totalTickets),
    buildPdfRow('Completed Tickets', doneTickets),
    buildPdfRow('Cancelled Tickets', cancelledTickets),
    buildPdfRow('Top Service', topServiceName),
    buildPdfRow('Top Service Tickets', topServiceCount),
  ].join('')

  const timingRows = [
    ['Kiosk Completion', kioskAvgLabel.value, kioskMedianLabel.value, kioskP90Label.value, kioskTimingStats.value.count || 0],
    ['Ticket Cycle', ticketCycleAvgLabel.value, ticketCycleMedianLabel.value, ticketCycleP90Label.value, ticketCycleStats.value.count || 0],
    ['Wait To Serve', waitToServeLabel.value, '—', '—', waitToServeStats.value.count || 0],
    ['Service Duration', serviceDurationLabel.value, '—', '—', serviceDurationStats.value.count || 0],
  ]
    .map((row) => `<tr>${row.map((cell) => `<td>${escapeHtml(cell)}</td>`).join('')}</tr>`)
    .join('')

  const ticketRows = ticketTrendSeries.value
    .map((entry) => `<tr><td>${escapeHtml(entry.label)}</td><td>${escapeHtml(entry.value)}</td></tr>`)
    .join('')

  const statusRows = ticketStatusMix.value
    .map((entry) => `<tr><td>${escapeHtml(entry.label)}</td><td>${escapeHtml(entry.count)}</td><td>${escapeHtml(entry.percent)}%</td></tr>`)
    .join('')

  const serviceRows = serviceVolumeBars.value
    .map((entry) => `<tr><td>${escapeHtml(formatServiceName(entry.name))}</td><td>${escapeHtml(entry.count)}</td><td>${escapeHtml(entry.percent)}%</td></tr>`)
    .join('')

  const serviceMixRows = serviceMix.value
    .map((entry) => `<tr><td>${escapeHtml(formatServiceName(entry.label))}</td><td>${escapeHtml(entry.count)}</td><td>${escapeHtml(entry.percent)}%</td></tr>`)
    .join('')

  const trafficRows = trafficBuckets.value
    .map((entry) => `<tr><td>${escapeHtml(entry.label)}</td><td>${escapeHtml(entry.count)}</td></tr>`)
    .join('')

  const residentRows = residentTrendSeries.value
    .map((entry) => `<tr><td>${escapeHtml(entry.label)}</td><td>${escapeHtml(entry.value)}</td></tr>`)
    .join('')

  reportWindow.document.write(`<!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="UTF-8" />
        <title>Admin Analytics Report</title>
        <style>
          @page { size: A4; margin: 18mm; }
          * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
          body { margin: 0; color: #0f172a; }
          .cover { height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 6px; }
          .cover-logo { width: 120px; height: 120px; object-fit: contain; margin-bottom: 10px; }
          .cover-title { font-size: 22px; font-weight: 700; color: #0b2c6f; }
          .cover-subtitle { font-size: 14px; color: #475569; }
          .cover-report { margin-top: 12px; font-size: 13px; letter-spacing: 0.2em; text-transform: uppercase; color: #0f172a; }
          .cover-meta { margin-top: 16px; font-size: 12px; color: #475569; display: grid; gap: 4px; }
          .page { page-break-before: always; padding: 0; position: relative; }
          .watermark {
            position: fixed;
            inset: 0;
            display: grid;
            place-items: center;
            pointer-events: none;
            z-index: 0;
          }
          .watermark span {
            font-size: 60px;
            font-weight: 700;
            letter-spacing: 0.2em;
            color: rgba(15, 23, 42, 0.08);
            transform: rotate(-24deg);
          }
          .page-content { position: relative; z-index: 1; }
          .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0b2c6f; padding-bottom: 12px; gap: 12px; }
          .brand { display: grid; gap: 4px; }
          .brand-row { display: flex; gap: 10px; align-items: center; }
          .report-logo { width: 36px; height: 36px; object-fit: contain; }
          .brand h1 { margin: 0; font-size: 18px; color: #0b2c6f; }
          .brand p { margin: 0; font-size: 12px; color: #475569; }
          .report-meta { text-align: right; font-size: 11px; color: #475569; }
          .section { margin-top: 18px; }
          h2 { font-size: 14px; color: #0b2c6f; margin: 0 0 8px; break-after: avoid; page-break-after: avoid; }
          .summary-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
          .summary-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px; background: #f8fafc; }
          .summary-card h3 { margin: 0 0 6px; font-size: 12px; color: #0b2c6f; text-transform: uppercase; letter-spacing: 0.08em; }
          .summary-card p { margin: 0; font-size: 12px; color: #0f172a; }
          table { width: 100%; border-collapse: collapse; font-size: 11px; page-break-inside: auto; }
          th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
          th { background: #f8fafc; text-transform: uppercase; letter-spacing: 0.08em; font-size: 9.5px; }
          tbody tr:nth-child(even) { background: #fbfdff; }
          thead { display: table-header-group; }
          tfoot { display: table-footer-group; }
          tr { page-break-inside: avoid; break-inside: avoid; }
          .footer { margin-top: 24px; font-size: 10px; color: #64748b; text-align: center; display: grid; gap: 6px; }
          .footer .line { width: 100%; height: 1px; background: #e2e8f0; }
          .footer .meta { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
        </style>
      </head>
      <body>
        <div class="cover">
          <img class="cover-logo" src="/logo.png" alt="Barangay San Miguel" />
          <div class="cover-title">${escapeHtml(systemName)}</div>
          <div class="cover-subtitle">${escapeHtml(systemLocation)}</div>
          <div class="cover-report">Admin Analytics Dashboard Report</div>
          <div class="cover-meta">
            <div>Prepared By: ${escapeHtml(preparedBy)}</div>
            <div>${escapeHtml(preparedEmail)}</div>
            <div>Generated: ${escapeHtml(generatedAt)}</div>
          </div>
        </div>

        <div class="page">
          <div class="watermark"><span>CONFIDENTIAL</span></div>
          <div class="page-content">
          <div class="header">
            <div class="brand">
              <div class="brand-row">
                <img class="report-logo" src="/logo.png" alt="Barangay San Miguel" />
                <div>
                  <h1>${escapeHtml(systemName)}</h1>
                  <p>${escapeHtml(systemLocation)}</p>
                  <p>Admin Analytics Dashboard Report</p>
                </div>
              </div>
            </div>
            <div class="report-meta">
              <div>Generated: ${escapeHtml(generatedAt)}</div>
              <div>Prepared By: ${escapeHtml(preparedBy)}</div>
              <div>${escapeHtml(preparedEmail)}</div>
            </div>
          </div>

          <div class="section">
            <div class="summary-grid">
              <div class="summary-card">
                <h3>Filters</h3>
                <p>Range: ${escapeHtml(analyticsRangeLabel.value)}</p>
                <p>Service: ${escapeHtml(serviceLabel)}</p>
                <p>Status: ${escapeHtml(statusLabel)}</p>
              </div>
              <div class="summary-card">
                <h3>Highlights</h3>
                <p>Total Tickets: ${escapeHtml(totalTickets)}</p>
                <p>Completed: ${escapeHtml(doneTickets)}</p>
                <p>Cancelled: ${escapeHtml(cancelledTickets)}</p>
                <p>Top Service: ${escapeHtml(topServiceName)}</p>
              </div>
            </div>
          </div>

          ${buildPdfTable('Report Summary', ['Item', 'Value'], summaryRows)}
          ${buildPdfTable('Key Highlights', ['Metric', 'Value'], highlightRows)}
          ${buildPdfTable('Timing Summary', ['Metric', 'Average', 'Median', 'P90', 'Count'], timingRows)}
          ${buildPdfTable('Daily Ticket Volume', ['Date', 'Tickets'], ticketRows)}
          ${buildPdfTable('Ticket Status Mix', ['Status', 'Count', 'Percent'], statusRows)}
          ${buildPdfTable('Most Booked Service', ['Service', 'Count', 'Percent'], serviceRows)}
          ${buildPdfTable('Service Mix', ['Service', 'Count', 'Percent'], serviceMixRows)}
          ${buildPdfTable('Traffic By Time', ['Window', 'Tickets'], trafficRows)}
          ${buildPdfTable('Resident Registrations', ['Date', 'Registrations'], residentRows)}

          <div class="footer">
            <div class="line"></div>
            <div>Certified accurate as of ${escapeHtml(generatedAt)}.</div>
            <div class="meta">
              <span>${escapeHtml(systemName)}</span>
              <span>${escapeHtml(systemLocation)}</span>
              <span>Prepared by ${escapeHtml(preparedBy)}</span>
            </div>
            <div>Confidential — For internal operations only.</div>
          </div>
          </div>
        </div>
      </body>
    </html>`)
  reportWindow.document.close()
  reportWindow.focus()
  reportWindow.print()
}

const topServiceLabel = computed(() => {
  const top = serviceDemand.value[0]
  return top ? top.name : 'No Demand Yet'
})

const topServiceCountLabel = computed(() => {
  const top = serviceDemand.value[0]
  return top ? `${top.count} tickets` : 'Waiting for Tickets'
})

const kioskStatusList = computed(() => {
  const now = Date.now()
  return kiosks.value.map((kiosk) => {
    const lastSeen = kiosk.last_seen_at ? new Date(kiosk.last_seen_at).getTime() : null
    const minutesAgo = lastSeen ? Math.round((now - lastSeen) / 60000) : null
    const online = minutesAgo !== null && minutesAgo <= 20
    return {
      ...kiosk,
      online,
      lastSeenLabel: formatMinutesAgo(minutesAgo),
    }
  })
})

const onlineKioskCount = computed(() => kioskStatusList.value.filter((kiosk) => kiosk.online).length)

const offlineKioskCount = computed(() => Math.max(0, kioskStatusList.value.length - onlineKioskCount.value))

const kioskFiltered = computed(() => {
  const term = kioskSearch.value.trim().toLowerCase()
  return kioskStatusList.value.filter((kiosk) => {
    const matchesStatus =
      kioskStatusFilter.value === 'all' ||
      (kioskStatusFilter.value === 'online' && kiosk.online) ||
      (kioskStatusFilter.value === 'offline' && !kiosk.online)
    if (!matchesStatus) return false
    if (!term) return true
    const deviceId = String(kiosk.device_id || '').toLowerCase()
    const name = String(kiosk.name || '').toLowerCase()
    const token = String(kiosk.token || '').toLowerCase()
    return deviceId.includes(term) || name.includes(term) || token.includes(term)
  })
})

const kioskSorted = computed(() => {
  const items = [...kioskFiltered.value]
  const direction = kioskSortDirection.value === 'desc' ? -1 : 1
  const compareValues = (a, b) => (a > b ? 1 : a < b ? -1 : 0)
  items.sort((a, b) => {
    if (kioskSortKey.value === 'status') {
      const statusDiff = compareValues(a.online ? 0 : 1, b.online ? 0 : 1)
      if (statusDiff !== 0) return statusDiff * direction
    }
    if (kioskSortKey.value === 'name') {
      const nameDiff = compareValues(String(a.name || ''), String(b.name || ''))
      if (nameDiff !== 0) return nameDiff * direction
    }
    if (kioskSortKey.value === 'device_id') {
      const deviceDiff = compareValues(String(a.device_id || ''), String(b.device_id || ''))
      if (deviceDiff !== 0) return deviceDiff * direction
    }
    if (kioskSortKey.value === 'last_seen_at') {
      const timeDiff = compareValues(new Date(a.last_seen_at || 0).getTime(), new Date(b.last_seen_at || 0).getTime())
      if (timeDiff !== 0) return timeDiff * direction
    }
    const fallback = compareValues(String(a.device_id || ''), String(b.device_id || ''))
    return fallback * direction
  })
  return items
})

const kioskLatestSeenLabel = computed(() => {
  if (!kioskStatusList.value.length) return '--'
  const sorted = [...kioskStatusList.value].sort(
    (a, b) => new Date(b.last_seen_at || 0).getTime() - new Date(a.last_seen_at || 0).getTime()
  )
  return sorted[0]?.lastSeenLabel || '--'
})

const kioskTotalPages = computed(() =>
  Math.max(1, Math.ceil(kioskSorted.value.length / kioskPageSize))
)

const kioskPageRows = computed(() => {
  const start = (kioskPage.value - 1) * kioskPageSize
  return kioskSorted.value.slice(start, start + kioskPageSize)
})

const kioskRangeLabel = computed(() => {
  const total = kioskSorted.value.length
  if (!total) return 'No kiosks to display'
  const start = (kioskPage.value - 1) * kioskPageSize + 1
  const end = Math.min(total, start + kioskPageSize - 1)
  return `Showing ${start}-${end} of ${total} kiosks`
})

const trafficBuckets = computed(() => {
  if (!analyticsTickets.value.length) return []
  const slots = Array.from({ length: 8 }, (_, index) => ({ index, count: 0 }))
  analyticsTickets.value.forEach((ticket) => {
    if (!ticket.issued_at) return
    const hour = new Date(ticket.issued_at).getHours()
    if (Number.isNaN(hour)) return
    const bucket = Math.floor(hour / 3)
    const slot = slots[bucket]
    if (slot) {
      slot.count += 1
    }
  })
  const maxCount = Math.max(...slots.map((slot) => slot.count), 1)
  return slots.map((slot) => ({
    ...slot,
    percent: Math.round((slot.count / maxCount) * 100),
    label: formatTimeRange(slot.index * 3),
  }))
})

const trafficSeries = computed(() =>
  trafficBuckets.value.map((slot) => ({
    label: slot.label,
    value: slot.count,
  }))
)

const trafficTrend = computed(() => buildLineMetrics(trafficSeries.value))
const trafficAxis = computed(() => buildAxisLabels(trafficSeries.value))

const busiestTrafficSlot = computed(() => {
  if (!trafficBuckets.value.length) return null
  return trafficBuckets.value.reduce((best, slot) => {
    if (!best || slot.count > best.count) return slot
    return best
  }, null)
})

const trafficInsight = computed(() => {
  if (!busiestTrafficSlot.value || busiestTrafficSlot.value.count === 0) {
    return 'Not enough traffic data yet. Ticket volume will populate this view.'
  }
  return `Highest traffic window: ${busiestTrafficSlot.value.label} with ${busiestTrafficSlot.value.count} tickets.`
})

const lineChartConfig = {
  width: 640,
  height: 220,
  padding: 24,
}

const lineGrid = computed(() => {
  const usable = lineChartConfig.height - lineChartConfig.padding * 2
  return [0.25, 0.5, 0.75].map((ratio) => lineChartConfig.padding + usable * ratio)
})

const toDateKey = (date) => {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const getDateBounds = (items, key) => {
  let minDate = null
  let maxDate = null
  items.forEach((item) => {
    if (!item[key]) return
    const date = new Date(item[key])
    if (Number.isNaN(date.getTime())) return
    if (!minDate || date < minDate) minDate = date
    if (!maxDate || date > maxDate) maxDate = date
  })
  return { minDate, maxDate }
}

const buildDailySeries = (items, key) => {
  if (!items.length) return []
  const { start, end } = analyticsDateRange.value
  const bounds = getDateBounds(items, key)
  let startDate = start || bounds.minDate
  let endDate = end || bounds.maxDate
  if (!startDate || !endDate) return []
  startDate = startOfDay(startDate)
  endDate = endOfDay(endDate)

  const dayMap = new Map()
  items.forEach((item) => {
    if (!item[key]) return
    const date = new Date(item[key])
    if (Number.isNaN(date.getTime())) return
    const keyValue = toDateKey(date)
    dayMap.set(keyValue, (dayMap.get(keyValue) || 0) + 1)
  })

  const series = []
  for (let cursor = new Date(startDate); cursor <= endDate; cursor = addDays(cursor, 1)) {
    const label = cursor.toLocaleDateString(undefined, { month: 'short', day: 'numeric' })
    const keyValue = toDateKey(cursor)
    series.push({
      label,
      value: dayMap.get(keyValue) || 0,
    })
  }
  return series
}

const buildLineMetrics = (series) => {
  if (!series.length) {
    return { linePath: '', areaPath: '', points: [] }
  }
  const maxValue = Math.max(...series.map((point) => point.value), 1)
  const stepX =
    series.length > 1
      ? (lineChartConfig.width - lineChartConfig.padding * 2) / (series.length - 1)
      : 0
  const points = series.map((point, index) => {
    const x = lineChartConfig.padding + index * stepX
    const y =
      lineChartConfig.height -
      lineChartConfig.padding -
      (point.value / maxValue) * (lineChartConfig.height - lineChartConfig.padding * 2)
    return { x, y, value: point.value, index }
  })
  const linePath = points
    .map((point, index) => `${index === 0 ? 'M' : 'L'}${point.x} ${point.y}`)
    .join(' ')
  const areaPath = `${linePath} L ${
    points[points.length - 1].x
  } ${lineChartConfig.height - lineChartConfig.padding} L ${
    points[0].x
  } ${lineChartConfig.height - lineChartConfig.padding} Z`
  const markerStep = points.length > 12 ? Math.ceil(points.length / 6) : 1
  const markers = points.filter(
    (point, index) =>
      index === 0 || index === points.length - 1 || index % markerStep === 0
  )
  return { linePath, areaPath, points: markers }
}

const buildAxisLabels = (series) => {
  if (!series.length) return []
  const midIndex = Math.floor(series.length / 2)
  return [series[0].label, series[midIndex].label, series[series.length - 1].label]
}

const buildTrendInsight = (series, label) => {
  if (series.length < 4) return `Not enough ${label.toLowerCase()} history yet for trend insights.`
  const windowSize = Math.min(3, Math.floor(series.length / 2))
  const recent = series.slice(-windowSize).reduce((sum, point) => sum + point.value, 0)
  const previous = series
    .slice(-(windowSize * 2), -windowSize)
    .reduce((sum, point) => sum + point.value, 0)
  const peak = series.reduce((best, point) => {
    if (!best || point.value > best.value) return point
    return best
  }, null)
  const peakNote =
    peak && peak.value
      ? ` Peak day: ${peak.label} (${peak.value}).`
      : ''
  if (previous === 0) {
    if (recent === 0) return `No ${label.toLowerCase()} activity in this range yet.${peakNote}`
    return `${label} activity started in the last ${windowSize} days.${peakNote}`
  }
  const change = Math.round(((recent - previous) / previous) * 100)
  if (change > 0) return `${label} volume is up ${change}% versus the previous ${windowSize} days.${peakNote}`
  if (change < 0) return `${label} volume is down ${Math.abs(change)}% versus the previous ${windowSize} days.${peakNote}`
  return `${label} volume is steady compared to the previous ${windowSize} days.${peakNote}`
}

const ticketTrendSeries = computed(() => buildDailySeries(analyticsTickets.value, 'issued_at'))

const ticketTrend = computed(() => buildLineMetrics(ticketTrendSeries.value))

const ticketTrendAxis = computed(() => buildAxisLabels(ticketTrendSeries.value))

const ticketTrendInsight = computed(() => buildTrendInsight(ticketTrendSeries.value, 'Ticket'))

const residentTrendSeries = computed(() => buildDailySeries(analyticsResidents.value, 'created_at'))

const residentTrend = computed(() => buildLineMetrics(residentTrendSeries.value))

const residentTrendAxis = computed(() => buildAxisLabels(residentTrendSeries.value))

const residentTrendInsight = computed(() => buildTrendInsight(residentTrendSeries.value, 'Registration'))

const ticketStatusMix = computed(() => {
  const total = analyticsTickets.value.length
  const counts = {
    Waiting: 0,
    Serving: 0,
    Done: 0,
    Cancelled: 0,
  }
  analyticsTickets.value.forEach((ticket) => {
    if (ticket.status === 'waiting') counts.Waiting += 1
    if (ticket.status === 'serving') counts.Serving += 1
    if (ticket.status === 'done') counts.Done += 1
    if (ticket.status === 'cancelled') counts.Cancelled += 1
  })
  const palette = {
    Waiting: '#f2c300',
    Serving: '#0b2c6f',
    Done: '#2e7d32',
    Cancelled: '#c0392b',
  }
  return Object.entries(counts)
    .filter(([, count]) => count > 0)
    .map(([label, count]) => ({
      label,
      count,
      share: total ? count / total : 0,
      percent: total ? Math.round((count / total) * 100) : 0,
      color: palette[label],
    }))
})

const ticketStatusTotal = computed(() => analyticsTickets.value.length)

const buildSegments = (items, radius) => {
  const circumference = 2 * Math.PI * radius
  let offset = 0
  return items.map((item) => {
    const length = item.share * circumference
    const dasharray = `${length} ${circumference - length}`
    const dashoffset = -offset
    offset += length
    return { ...item, dasharray, dashoffset }
  })
}

const ticketStatusSegments = computed(() => buildSegments(ticketStatusMix.value, 46))

const statusMixInsight = computed(() => {
  const total = ticketStatusTotal.value
  if (!total) return 'No ticket activity in this range yet.'
  const done = ticketStatusMix.value.find((item) => item.label === 'Done')?.count || 0
  const cancelled = ticketStatusMix.value.find((item) => item.label === 'Cancelled')?.count || 0
  const doneRate = Math.round((done / total) * 100)
  const cancelRate = Math.round((cancelled / total) * 100)
  return `Completion rate is ${doneRate}% with ${cancelRate}% cancellations for this range.`
})

const serviceMix = computed(() => {
  const counts = new Map()
  analyticsTickets.value.forEach((ticket) => {
    if (!ticket.service_id) return
    counts.set(ticket.service_id, (counts.get(ticket.service_id) || 0) + 1)
  })
  const total = Array.from(counts.values()).reduce((sum, value) => sum + value, 0)
  if (!total) return []
  const palette = ['#0b2c6f', '#f2c300', '#22c55e', '#0ea5e9', '#f97316', '#64748b']
  const items = Array.from(counts.entries())
    .map(([id, count]) => {
      const service = services.value.find((entry) => entry.id === id)
      return {
        label: service?.name || `Service #${id}`,
        count,
      }
    })
    .sort((a, b) => b.count - a.count)
  const topItems = items.slice(0, 4)
  const otherCount = items.slice(4).reduce((sum, item) => sum + item.count, 0)
  if (otherCount > 0) {
    topItems.push({ label: 'Other', count: otherCount })
  }
  return topItems.map((item, index) => ({
    ...item,
    share: item.count / total,
    percent: Math.round((item.count / total) * 100),
    color: item.label === 'Other' ? '#cbd5e1' : palette[index % palette.length],
  }))
})

const serviceMixSegments = computed(() => buildSegments(serviceMix.value, 28))

const analyticsServiceInsight = computed(() => {
  const top = analyticsTopService.value
  if (!top) return 'No service demand for the selected range yet.'
  const total = analyticsTickets.value.length || 1
  const share = Math.round((top.count / total) * 100)
  return `${top.name} accounts for ${share}% of tickets in this range.`
})

const serviceMixInsight = computed(() => {
  const top = serviceMix.value[0]
  if (!top) return 'No service mix to analyze yet.'
  return `${top.label} leads the service mix with ${top.percent}% of bookings.`
})


const queueInsight = computed(() => {
  if (!queueStatusCounts.value.total) return 'No tickets yet. The queue board is ready for the first visitors.'
  if (queueStatusCounts.value.waiting > queueStatusCounts.value.serving * 2) {
    return 'Waiting line is growing faster than serving. Call next tickets to reduce wait time.'
  }
  return queuePressureNote.value
})

const residentInsight = computed(() => {
  if (!residentStats.value.total) return 'No residents registered yet. Promote the portal to build the base.'
  if (residentStats.value.pending > residentStats.value.approved) {
    return 'Pending approvals are high. Prioritize reviews to unlock kiosk access.'
  }
  return `${approvalRateLabel.value} of residents are approved and ready to use the kiosk.`
})

const serviceInsight = computed(() => {
  const top = serviceDemand.value[0]
  if (!top) return 'Service demand will surface once tickets are created.'
  return `${top.name} leads demand with ${top.count} tickets in the queue.`
})

const kioskInsight = computed(() => {
  if (!kioskStatusList.value.length) return 'Add kiosks to monitor device readiness.'
  if (offlineKioskCount.value > 0) {
    return `${offlineKioskCount.value} kiosk${offlineKioskCount.value > 1 ? 's are' : ' is'} offline. Check connectivity.`
  }
  return 'All kiosks are reporting in on time.'
})

const openLogoutModal = () => {
  isLogoutModalOpen.value = true
}

const closeLogoutModal = () => {
  isLogoutModalOpen.value = false
}

const confirmLogout = () => {
  isLogoutModalOpen.value = false
  logout()
}

const logout = () => {
  localStorage.removeItem('admin_token')
  localStorage.removeItem('admin_profile')
  router.push('/login')
}

const openCreateResidentModal = () => {
  residentModalMode.value = 'create'
  residentForm.value = {
    username: '',
    first_name: '',
    middle_name: '',
    last_name: '',
    date_of_birth: '',
    gender: '',
    civil_status: '',
    mobile_number: '',
    address: '',
    email: '',
    status: 'pending',
    password: '',
    profile_photo_url: '',
  }
  residentTarget.value = null
  residentFormError.value = ''
  isResidentSaving.value = false
  isResidentModalOpen.value = true
}

const openEditResidentModal = (resident) => {
  residentModalMode.value = 'edit'
  residentTarget.value = resident
  residentForm.value = {
    username: resident.username || '',
    first_name: resident.first_name || '',
    middle_name: resident.middle_name || '',
    last_name: resident.last_name || '',
    date_of_birth: resident.date_of_birth || '',
    gender: resident.gender || '',
    civil_status: resident.civil_status || '',
    mobile_number: resident.mobile_number || '',
    address: resident.address || '',
    email: resident.email || '',
    status: resident.status || 'pending',
    password: '',
    profile_photo_url: resident.profile_photo_url || '',
  }
  residentFormError.value = ''
  isResidentSaving.value = false
  isResidentModalOpen.value = true
}

const closeResidentModal = () => {
  isResidentModalOpen.value = false
  residentFormError.value = ''
  isResidentSaving.value = false
  residentTarget.value = null
}

const setResidentPage = (page) => {
  const next = Math.min(Math.max(page, 1), residentTotalPages.value)
  residentPage.value = next
}

const nextResidentPage = () => {
  setResidentPage(residentPage.value + 1)
}

const previousResidentPage = () => {
  setResidentPage(residentPage.value - 1)
}

const setQueuePage = (page) => {
  const next = Math.min(Math.max(page, 1), queueTotalPages.value)
  queuePage.value = next
}

const nextQueuePage = () => {
  setQueuePage(queuePage.value + 1)
}

const previousQueuePage = () => {
  setQueuePage(queuePage.value - 1)
}

const toggleQueueSortDirection = () => {
  queueSortDirection.value = queueSortDirection.value === 'asc' ? 'desc' : 'asc'
  queuePage.value = 1
}

const setTransactionPage = (page) => {
  const next = Math.min(Math.max(page, 1), transactionTotalPages.value)
  transactionPage.value = next
}

const nextTransactionPage = () => {
  setTransactionPage(transactionPage.value + 1)
}

const previousTransactionPage = () => {
  setTransactionPage(transactionPage.value - 1)
}

const toggleTransactionSortDirection = () => {
  transactionSortDirection.value = transactionSortDirection.value === 'asc' ? 'desc' : 'asc'
  transactionPage.value = 1
}

const handleTransactionFilterChange = () => {
  transactionPage.value = 1
  loadTransactions()
}

const setKioskPage = (page) => {
  const next = Math.min(Math.max(page, 1), kioskTotalPages.value)
  kioskPage.value = next
}

const nextKioskPage = () => {
  setKioskPage(kioskPage.value + 1)
}

const previousKioskPage = () => {
  setKioskPage(kioskPage.value - 1)
}

const toggleKioskSortDirection = () => {
  kioskSortDirection.value = kioskSortDirection.value === 'asc' ? 'desc' : 'asc'
  kioskPage.value = 1
}

const setAuditPage = (page) => {
  const next = Math.min(Math.max(page, 1), auditTotalPages.value)
  auditPage.value = next
}

const nextAuditPage = () => {
  setAuditPage(auditPage.value + 1)
}

const previousAuditPage = () => {
  setAuditPage(auditPage.value - 1)
}

const toggleAuditSortDirection = () => {
  auditSortDirection.value = auditSortDirection.value === 'asc' ? 'desc' : 'asc'
  auditPage.value = 1
}

const resetAuditFilters = () => {
  auditSearch.value = ''
  auditActorFilter.value = 'all'
  auditSortKey.value = 'created_at'
  auditSortDirection.value = 'desc'
  auditPage.value = 1
}

const setAdminPage = (page) => {
  const next = Math.min(Math.max(page, 1), adminTotalPages.value)
  adminPage.value = next
}

const nextAdminPage = () => {
  setAdminPage(adminPage.value + 1)
}

const previousAdminPage = () => {
  setAdminPage(adminPage.value - 1)
}

const toggleAdminSortDirection = () => {
  adminSortDirection.value = adminSortDirection.value === 'asc' ? 'desc' : 'asc'
  adminPage.value = 1
}

const openEditAdminModal = (admin) => {
  adminTarget.value = admin
  adminForm.value = {
    name: admin.name || '',
    email: admin.email || '',
    role: admin.role || 'staff_admin',
    password: '',
    service_ids: Array.isArray(admin.service_ids) ? admin.service_ids.join(', ') : '',
  }
  adminFormError.value = ''
  isAdminSaving.value = false
  isAdminModalOpen.value = true
}

const closeAdminModal = () => {
  isAdminModalOpen.value = false
  adminFormError.value = ''
  isAdminSaving.value = false
  adminTarget.value = null
}

const submitAdminForm = async () => {
  if (!adminTarget.value) return
  adminFormError.value = ''
  const serviceIds = adminForm.value.service_ids
    ? adminForm.value.service_ids
        .split(',')
        .map((id) => parseInt(id.trim(), 10))
        .filter(Boolean)
    : []
  const payload = {
    name: adminForm.value.name.trim(),
    email: adminForm.value.email.trim(),
    role: adminForm.value.role,
    service_ids: adminForm.value.role === 'super_admin' ? [] : serviceIds,
  }

  if (!payload.name || !payload.email) {
    adminFormError.value = 'Name and email are required.'
    return
  }

  if (adminForm.value.password) {
    payload.password = adminForm.value.password
  }

  isAdminSaving.value = true
  try {
    await apiUpdateAdmin(adminTarget.value.id, payload)
    closeAdminModal()
    await loadAdmins()
  } catch (err) {
    adminFormError.value = err.message
  } finally {
    isAdminSaving.value = false
  }
}

const resetAdminFilters = () => {
  adminSearch.value = ''
  adminRoleFilter.value = 'all'
  adminSortKey.value = 'created_at'
  adminSortDirection.value = 'desc'
  adminPage.value = 1
}

const setServicePage = (page) => {
  const next = Math.min(Math.max(page, 1), serviceTotalPages.value)
  servicePage.value = next
}

const nextServicePage = () => {
  setServicePage(servicePage.value + 1)
}

const previousServicePage = () => {
  setServicePage(servicePage.value - 1)
}

const openCreateServiceModal = () => {
  serviceModalMode.value = 'create'
  serviceForm.value = { name: '', code: '', active: 1 }
  serviceTarget.value = null
  serviceFormError.value = ''
  isServiceSaving.value = false
  isServiceModalOpen.value = true
}

const openEditServiceModal = (service) => {
  serviceModalMode.value = 'edit'
  serviceTarget.value = service
  serviceForm.value = {
    name: service.name || '',
    code: service.code || '',
    active: service.active ? 1 : 0,
  }
  serviceFormError.value = ''
  isServiceSaving.value = false
  isServiceModalOpen.value = true
}

const closeServiceModal = () => {
  isServiceModalOpen.value = false
  serviceFormError.value = ''
  isServiceSaving.value = false
  serviceTarget.value = null
}

const submitServiceForm = async () => {
  serviceFormError.value = ''
  const activeValue = Number(serviceForm.value.active) === 1 ? 1 : 0
  const payload = {
    name: serviceForm.value.name.trim(),
    code: serviceForm.value.code.trim(),
    active: activeValue,
  }

  if (!payload.name || !payload.code) {
    serviceFormError.value = 'Service name and code are required.'
    return
  }

  isServiceSaving.value = true
  try {
    if (serviceModalMode.value === 'create') {
      await apiCreateService(payload)
    } else if (serviceTarget.value) {
      await apiUpdateService(serviceTarget.value.id, payload)
    }
    closeServiceModal()
    await loadServices()
  } catch (err) {
    serviceFormError.value = err.message
  } finally {
    isServiceSaving.value = false
  }
}

const submitResidentForm = async () => {
  residentFormError.value = ''
  const payload = {
    first_name: residentForm.value.first_name.trim(),
    last_name: residentForm.value.last_name.trim(),
    email: residentForm.value.email.trim(),
    status: residentForm.value.status,
    username: residentForm.value.username.trim() || null,
    middle_name: residentForm.value.middle_name.trim() || null,
    date_of_birth: residentForm.value.date_of_birth || null,
    gender: residentForm.value.gender || null,
    civil_status: residentForm.value.civil_status || null,
    mobile_number: residentForm.value.mobile_number.trim() || null,
    address: residentForm.value.address.trim() || null,
  }

  if (!payload.first_name || !payload.last_name || !payload.email) {
    residentFormError.value = 'First name, last name, and email are required.'
    return
  }

  if (residentModalMode.value === 'create') {
    if (!residentForm.value.password) {
      residentFormError.value = 'Password is required for new residents.'
      return
    }
    payload.password = residentForm.value.password
  } else if (residentForm.value.password) {
    payload.password = residentForm.value.password
  }

  isResidentSaving.value = true
  try {
    if (residentModalMode.value === 'create') {
      await apiCreateResident(payload)
    } else if (residentTarget.value) {
      await apiUpdateResident(residentTarget.value.id, payload)
    }
    closeResidentModal()
    await refreshResidents()
  } catch (err) {
    residentFormError.value = err.message
  } finally {
    isResidentSaving.value = false
  }
}

const applyResidentStatus = async (status) => {
  residentForm.value.status = status
  await submitResidentForm()
}

const openDeleteResidentModal = (resident) => {
  residentDeleteTarget.value = resident
  deleteResidentError.value = ''
  isDeletingResident.value = false
  isDeleteResidentOpen.value = true
}

const closeDeleteResidentModal = () => {
  isDeleteResidentOpen.value = false
  deleteResidentError.value = ''
  isDeletingResident.value = false
  residentDeleteTarget.value = null
}

const confirmDeleteResident = async () => {
  if (!residentDeleteTarget.value) return
  deleteResidentError.value = ''
  isDeletingResident.value = true
  try {
    await apiDeleteResident(residentDeleteTarget.value.id)
    closeDeleteResidentModal()
    await refreshResidents()
  } catch (err) {
    deleteResidentError.value = err.message
  } finally {
    isDeletingResident.value = false
  }
}

const loadResidents = async () => {
  residentError.value = ''
  isLoadingResidents.value = true
  try {
    const data = await getResidents({ status: statusFilter.value, search: search.value })
    residents.value = data.residents || []
    if (residentPage.value > residentTotalPages.value) {
      residentPage.value = residentTotalPages.value
    }
  } catch (err) {
    residentError.value = err.message
  } finally {
    isLoadingResidents.value = false
  }
}

const refreshResidents = async () => {
  await Promise.all([loadResidents(), loadAllResidents()])
}

const loadAllResidents = async () => {
  try {
    const data = await getResidents()
    allResidents.value = data.residents || []
  } catch (err) {
    // Ignore analytics fetch errors for now.
  }
}

const resetResidentFilters = async () => {
  statusFilter.value = ''
  search.value = ''
  residentPage.value = 1
  await refreshResidents()
}

const loadServices = async () => {
  serviceError.value = ''
  isLoadingServices.value = true
  try {
    const data = await getServices()
    services.value = data.services || []
    if (servicePage.value > serviceTotalPages.value) {
      servicePage.value = serviceTotalPages.value
    }
  } catch (err) {
    serviceError.value = err.message
  } finally {
    isLoadingServices.value = false
  }
}

const toggleService = async (service) => {
  serviceError.value = ''
  try {
    await apiUpdateService(service.id, { active: service.active ? 0 : 1 })
    await loadServices()
  } catch (err) {
    serviceError.value = err.message
  }
}

const resetServiceFilters = () => {
  serviceSearch.value = ''
  serviceStatusFilter.value = 'all'
  servicePage.value = 1
}

const loadKiosks = async () => {
  kioskError.value = ''
  isLoadingKiosks.value = true
  try {
    const data = await getKiosks()
    kiosks.value = data.kiosks || []
    if (kioskPage.value > kioskTotalPages.value) {
      kioskPage.value = kioskTotalPages.value
    }
  } catch (err) {
    kioskError.value = err.message
  } finally {
    isLoadingKiosks.value = false
  }
}

const createKiosk = async () => {
  kioskError.value = ''
  try {
    await apiCreateKiosk({ device_id: newKiosk.value.device_id, name: newKiosk.value.name })
    newKiosk.value = { device_id: '', name: '' }
    await loadKiosks()
  } catch (err) {
    kioskError.value = err.message
  }
}

const resetKioskFilters = () => {
  kioskSearch.value = ''
  kioskStatusFilter.value = 'all'
  kioskSortKey.value = 'last_seen_at'
  kioskSortDirection.value = 'desc'
  kioskPage.value = 1
}

const loadQueue = async () => {
  queueError.value = ''
  isLoadingQueue.value = true
  try {
    const data = await getQueue()
    queueTickets.value = data.tickets || []
    if (activeCallTicket.value?.id) {
      const match = queueTickets.value.find((ticket) => ticket.id === activeCallTicket.value.id)
      activeCallTicket.value = match || null
    }
    if (!activeCallTicket.value) {
      const serving = queueTickets.value.find((ticket) => ticket.status === 'serving')
      activeCallTicket.value = serving || null
    }
    if (queuePage.value > queueTotalPages.value) {
      queuePage.value = queueTotalPages.value
    }
  } catch (err) {
    queueError.value = err.message
  } finally {
    isLoadingQueue.value = false
  }
}

const resetQueueFilters = () => {
  queueServiceId.value = ''
  queueStatus.value = ''
  queueSearch.value = ''
  queueSortKey.value = 'smart'
  queueSortDirection.value = 'asc'
  queuePage.value = 1
}

const loadAllQueueTickets = async () => {
  try {
    const data = await getQueue()
    allQueueTickets.value = data.tickets || []
  } catch (err) {
    // Ignore analytics fetch errors for now.
  }
}

const refreshQueueSnapshot = async () => {
  if (isQueueAutoRefreshing.value) return
  isQueueAutoRefreshing.value = true
  try {
    await loadQueue()
    await loadAllQueueTickets()
  } finally {
    isQueueAutoRefreshing.value = false
  }
}

const loadTransactions = async () => {
  transactionError.value = ''
  isLoadingTransactions.value = true
  try {
    const data = await getQueue({
      service_id: transactionServiceId.value,
      status: transactionStatus.value,
    })
    transactions.value = data.tickets || []
    if (transactionPage.value > transactionTotalPages.value) {
      transactionPage.value = transactionTotalPages.value
    }
  } catch (err) {
    transactionError.value = err.message
  } finally {
    isLoadingTransactions.value = false
  }
}

const resetTransactionFilters = () => {
  transactionServiceId.value = ''
  transactionStatus.value = 'done'
  transactionSearch.value = ''
  transactionSortKey.value = 'issued_at'
  transactionSortDirection.value = 'desc'
  transactionPage.value = 1
  loadTransactions()
}

const callNext = async () => {
  queueError.value = ''
  try {
    const scope = callNextServiceId.value
    const serviceId = scope && scope !== 'auto' ? parseInt(scope, 10) : null
    const data = await queueNext(serviceId)
    await loadQueue()
    await loadAllQueueTickets()
    if (data?.ticket?.id) {
      const match = queueTickets.value.find((ticket) => ticket.id === data.ticket.id)
      activeCallTicket.value = match || data.ticket
    }
  } catch (err) {
    queueError.value = err.message
  }
}

const callTicket = async (ticket) => {
  if (!ticket?.id) return
  queueError.value = ''
  try {
    const data = await queueCall(ticket.id)
    await loadQueue()
    await loadAllQueueTickets()
    if (data?.ticket?.id) {
      const match = queueTickets.value.find((entry) => entry.id === data.ticket.id)
      activeCallTicket.value = match || data.ticket
    }
  } catch (err) {
    queueError.value = err.message
  }
}

const serveTicket = async (ticket) => {
  queueError.value = ''
  try {
    await queueServe(ticket.id)
    if (activeCallTicket.value?.id === ticket.id) {
      activeCallTicket.value = null
    }
    await loadQueue()
    await loadAllQueueTickets()
  } catch (err) {
    queueError.value = err.message
  }
}

const cancelTicket = async (ticket) => {
  queueError.value = ''
  try {
    await queueCancel(ticket.id)
    if (activeCallTicket.value?.id === ticket.id) {
      activeCallTicket.value = null
    }
    await loadQueue()
    await loadAllQueueTickets()
  } catch (err) {
    queueError.value = err.message
  }
}

const escapeHtml = (value) =>
  String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;')

const printCallTicket = async (ticket) => {
  if (!ticket?.id) return
  queueError.value = ''
  try {
    const token = localStorage.getItem('admin_token')
    if (!token) {
      queueError.value = 'Please sign in again to print the form.'
      return
    }
    const fallbackName = ticket.ticket_no ? `${ticket.ticket_no}` : `ticket-${ticket.id}`
    const url = `${baseUrl}/api/admin/queue/${ticket.id}/print?token=${encodeURIComponent(token)}`
    const link = document.createElement('a')
    link.href = url
    link.download = `${fallbackName}.docx`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  } catch (err) {
    queueError.value = err.message
  }
}

const loadAuditLogs = async () => {
  auditError.value = ''
  isLoadingLogs.value = true
  try {
    const data = await getAuditLogs(100)
    auditLogs.value = data.logs || []
    if (auditPage.value > auditTotalPages.value) {
      auditPage.value = auditTotalPages.value
    }
  } catch (err) {
    auditError.value = err.message
  } finally {
    isLoadingLogs.value = false
  }
}

const loadTimingAnalytics = async () => {
  timingError.value = ''
  timingLoading.value = true
  try {
    const { start, end } = analyticsDateRange.value
    const data = await getTimingAnalytics({
      start: start ? formatDateParam(start) : '',
      end: end ? formatDateParam(end) : '',
      service_id: analyticsServiceId.value,
    })
    timingAnalytics.value = data || {}
  } catch (err) {
    timingError.value = err.message
    timingAnalytics.value = {}
  } finally {
    timingLoading.value = false
  }
}

const loadAdmins = async () => {
  adminError.value = ''
  isLoadingAdmins.value = true
  try {
    const data = await getAdmins()
    admins.value = data.admins || []
    if (adminPage.value > adminTotalPages.value) {
      adminPage.value = adminTotalPages.value
    }
  } catch (err) {
    adminError.value = err.message
  } finally {
    isLoadingAdmins.value = false
  }
}

const createAdmin = async () => {
  adminError.value = ''
  try {
    const serviceIds = newAdmin.value.service_ids
      ? newAdmin.value.service_ids.split(',').map((id) => parseInt(id.trim(), 10)).filter(Boolean)
      : []
    await apiCreateAdmin({
      name: newAdmin.value.name,
      email: newAdmin.value.email,
      password: newAdmin.value.password,
      role: newAdmin.value.role,
      service_ids: serviceIds,
    })
    newAdmin.value = { name: '', email: '', password: '', role: 'staff_admin', service_ids: '' }
    await loadAdmins()
  } catch (err) {
    adminError.value = err.message
  }
}

const refreshAll = async () => {
  const tasks = [
    loadResidents(),
    loadAllResidents(),
    loadServices(),
    loadKiosks(),
    loadQueue(),
    loadAllQueueTickets(),
    loadTransactions(),
    loadAuditLogs(),
    loadTimingAnalytics(),
  ]
  if (isSuperAdmin.value) {
    tasks.push(loadAdmins())
  }
  await Promise.all(tasks)
  lastUpdatedAt.value = new Date()
}

const resetAnalyticsFilters = () => {
  analyticsRange.value = '30d'
  analyticsServiceId.value = ''
  analyticsStatus.value = 'all'
  analyticsStartDate.value = ''
  analyticsEndDate.value = ''
}

const residentInitials = (resident) => {
  if (!resident) return 'RS'
  const first = String(resident.first_name || '').trim()
  const last = String(resident.last_name || '').trim()
  const initials = `${first.charAt(0)}${last.charAt(0)}`.toUpperCase()
  return initials || 'RS'
}

const statusTone = (status) => {
  const value = String(status || '').toLowerCase()
  if (['approved', 'active', 'done'].includes(value)) return 'success'
  if (['rejected', 'cancelled', 'inactive'].includes(value)) return 'danger'
  if (['serving'].includes(value)) return 'info'
  if (['waiting', 'pending'].includes(value)) return 'warning'
  return 'neutral'
}

const statusClass = (status) => `is-${statusTone(status)}`

const rowClass = (status) => [`table-row`, `row-status-${statusTone(status)}`]

const formatServiceName = (value) => {
  if (!value) return ''
  const smallWords = new Set(['of', 'and', 'the', 'in', 'on', 'for'])
  return String(value)
    .split(' ')
    .map((word) => {
      const lower = word.toLowerCase()
      if (smallWords.has(lower)) return lower
      return lower.charAt(0).toUpperCase() + lower.slice(1)
    })
    .join(' ')
}

const formatDate = (value) => {
  if (!value) return '-'
  const date = new Date(value)
  return date.toLocaleDateString()
}

const formatReportTimestamp = (value = new Date()) => {
  if (!value) return ''
  return value
    .toLocaleString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: 'numeric',
      minute: '2-digit',
      hour12: true,
    })
    .replace(',', '')
}

const formatTime = (value) => {
  if (!value) return '--'
  const date = new Date(value)
  return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true })
}

const formatAction = (value = '') => {
  return value
    .replace(/[_-]+/g, ' ')
    .replace(/\./g, ' ')
    .replace(/\b\w/g, (char) => char.toUpperCase())
}

const formatMinutesAgo = (minutes) => {
  if (minutes === null || minutes === undefined) return 'Never checked in'
  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  const hours = Math.floor(minutes / 60)
  const remainder = minutes % 60
  if (!remainder) return `${hours}h ago`
  return `${hours}h ${remainder}m ago`
}

const formatServiceAssignments = (admin) => {
  if (!admin) return '-'
  if (admin.role === 'super_admin') return 'All services'
  const ids = Array.isArray(admin.service_ids) ? admin.service_ids : []
  if (!ids.length) return 'Unassigned'
  const labels = ids.map((id) => {
    const match = services.value.find((service) => service.id === id)
    return match?.code || `#${id}`
  })
  return labels.join(', ')
}

const getAdminInitials = (admin) => {
  const name = String(admin?.name || '').trim()
  if (!name) return 'AD'
  return name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0].toUpperCase())
    .join('')
}

const formatHourLabel = (hour) => {
  const normalized = ((hour % 24) + 24) % 24
  const period = normalized >= 12 ? 'PM' : 'AM'
  const display = normalized % 12 || 12
  return `${display} ${period}`
}

const formatTimeRange = (startHour) => {
  const startLabel = formatHourLabel(startHour)
  const endLabel = formatHourLabel(startHour + 3)
  return `${startLabel} - ${endLabel}`
}

onMounted(() => {
  const updatePhTime = () => {
    const formatter = new Intl.DateTimeFormat('en-PH', {
      timeZone: 'Asia/Manila',
      hour: 'numeric',
      minute: '2-digit',
      hour12: true,
    })
    phTimeLabel.value = formatter.format(new Date())
  }
  updatePhTime()
  phTimeTimer.value = setInterval(updatePhTime, 60 * 1000)
  queueRefreshTimer.value = setInterval(() => {
    if (document.hidden) return
    refreshQueueSnapshot()
  }, 8000)
  if (typeof ResizeObserver !== 'undefined') {
    queueMonitorObserver.value = new ResizeObserver(() => updateQueueMonitorScale())
    if (queueMonitorFrame.value) {
      queueMonitorObserver.value.observe(queueMonitorFrame.value)
    }
  }
  requestAnimationFrame(updateQueueMonitorScale)

  const sectionIds = ['dashboard', 'queue-control', 'resident-verification', 'services', 'transactions', 'kiosk-devices', 'audit-logs', 'admin-users']
  const sections = sectionIds
    .map((id) => document.getElementById(id))
    .filter(Boolean)
  hashListener = () => {
    if (window.location.hash) {
      activeSection.value = window.location.hash.replace('#', '')
    }
  }
  hashListener()
  window.addEventListener('hashchange', hashListener)
  dockObserver = new IntersectionObserver(
    (entries) => {
      const visible = entries
        .filter((entry) => entry.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)
      if (visible[0]) {
        activeSection.value = visible[0].target.id
      }
    },
    { threshold: [0.2, 0.4, 0.6] }
  )
  sections.forEach((section) => dockObserver.observe(section))

  refreshAll()
})

watch(
  [analyticsRange, analyticsStartDate, analyticsEndDate, analyticsServiceId],
  () => {
    loadTimingAnalytics()
  }
)

watch(activeSection, (section) => {
  if (section === 'queue-control') {
    refreshQueueSnapshot()
    requestAnimationFrame(updateQueueMonitorScale)
  }
})

onBeforeUnmount(() => {
  if (dockObserver) {
    dockObserver.disconnect()
  }
  if (hashListener) {
    window.removeEventListener('hashchange', hashListener)
  }
  if (phTimeTimer.value) {
    clearInterval(phTimeTimer.value)
  }
  if (queueRefreshTimer.value) {
    clearInterval(queueRefreshTimer.value)
  }
  if (queueMonitorObserver.value) {
    queueMonitorObserver.value.disconnect()
  }
})
</script>

<style scoped>
.admin-shell {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  padding: 2.5rem 2rem 3.5rem;
  background:
    radial-gradient(1200px 420px at 12% -20%, rgba(11, 44, 111, 0.18), transparent 60%),
    radial-gradient(900px 320px at 88% -10%, rgba(242, 195, 0, 0.16), transparent 55%),
    #f3f4f6;
  position: relative;
  isolation: isolate;
}

.admin-dock {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: nowrap;
  min-width: 0;
  padding: 0.95rem 1.45rem;
  border-radius: 26px;
  background: linear-gradient(135deg, rgba(11, 44, 111, 0.98), rgba(9, 30, 78, 0.95));
  border: 1px solid rgba(255, 255, 255, 0.18);
  box-shadow: 0 20px 45px rgba(11, 44, 111, 0.28), 0 4px 10px rgba(15, 23, 42, 0.12);
  backdrop-filter: blur(14px);
  position: sticky;
  top: 1.25rem;
  z-index: 3;
  animation: dock-reveal 0.6s ease both;
}

.admin-dock::after {
  content: '';
  position: absolute;
  left: 1.6rem;
  right: 1.6rem;
  bottom: -18px;
  height: 20px;
  border-radius: 999px;
  background: linear-gradient(90deg, rgba(242, 195, 0, 0.5), rgba(11, 44, 111, 0));
  opacity: 0.7;
  filter: blur(8px);
  pointer-events: none;
}

.admin-dock-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex: 0 1 auto;
  min-width: 0;
}

.admin-avatar {
  height: 44px;
  width: 44px;
  border-radius: 16px;
  display: grid;
  place-items: center;
  font-weight: 700;
  letter-spacing: 0.08em;
  background: #f2c300;
  color: #0b2c6f;
  box-shadow: inset 0 0 0 2px rgba(11, 44, 111, 0.2);
}

.admin-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #f8f7ff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.admin-email {
  margin-top: 0.15rem;
  font-size: 1rem;
  color: rgba(226, 232, 240, 0.8);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.admin-dock-divider {
  height: 38px;
  width: 1px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.35), rgba(255, 255, 255, 0.08));
  flex: 0 0 auto;
}

.admin-dock-nav {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex: 1;
  flex-wrap: nowrap;
  min-width: 0;
  overflow-x: auto;
  overflow-y: hidden;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.admin-dock-nav::-webkit-scrollbar {
  display: none;
}

.admin-nav-item {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  padding: 0.55rem 0.85rem;
  border-radius: 999px;
  font-size: 0.92rem;
  line-height: 1.1;
  white-space: nowrap;
  flex-shrink: 0;
  color: rgba(255, 255, 255, 0.88);
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.08);
  text-decoration: none;
  transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}

.admin-nav-item:hover {
  transform: translateY(-1px);
  background: rgba(242, 195, 0, 0.28);
  color: #ffffff;
  box-shadow: 0 12px 24px rgba(11, 44, 111, 0.18);
}

.admin-nav-item.is-active {
  background: linear-gradient(135deg, #f2c300, #ffd666);
  color: #0b2c6f;
  box-shadow: 0 14px 28px rgba(242, 195, 0, 0.35);
}

.admin-nav-icon {
  height: 28px;
  width: 28px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.2);
  color: inherit;
}

.admin-nav-icon svg {
  width: 16px;
  height: 16px;
}

.admin-logout {
  border: none;
  border-radius: 999px;
  padding: 0.65rem 1.1rem;
  font-size: 1rem;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  background: #f2c300;
  color: #0b2c6f;
  cursor: pointer;
  box-shadow: 0 14px 26px rgba(242, 195, 0, 0.3);
  transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
}

.admin-time {
  display: grid;
  gap: 0.2rem;
  text-align: right;
  color: #ffffff;
  font-weight: 600;
  padding: 0.4rem 0.8rem;
  border-radius: 14px;
  background: linear-gradient(135deg, rgba(242, 195, 0, 0.18), rgba(255, 255, 255, 0.08));
  border: 1px solid rgba(242, 195, 0, 0.25);
  box-shadow: 0 10px 22px rgba(11, 44, 111, 0.18);
  flex: 0 0 auto;
  white-space: nowrap;
}

.admin-time-label {
  font-size: 0.65rem;
  letter-spacing: 0.24em;
  text-transform: uppercase;
  color: rgba(226, 232, 240, 0.75);
}

.admin-time-value {
  font-size: 1.05rem;
  color: #fef3c7;
  letter-spacing: 0.1em;
  font-weight: 700;
}

.admin-logout:hover {
  transform: translateY(-1px);
  background: #f7cf1a;
  box-shadow: 0 18px 30px rgba(242, 195, 0, 0.4);
}

.admin-content {
  flex: 1;
  min-width: 0;
  font-size: 1rem;
  position: relative;
  z-index: 1;
}

.admin-page {
  width: 100%;
  padding: 0.6rem 0 2.75rem;
  position: relative;
  z-index: 1;
}

@keyframes dock-reveal {
  from {
    opacity: 0;
    transform: translateY(-12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.admin-card {
  background: #ffffff;
  border-radius: 26px;
  padding: 1.8rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
  position: relative;
  overflow: hidden;
}

.admin-card::after {
  content: '';
  position: absolute;
  top: 18px;
  right: 24px;
  width: 96px;
  height: 96px;
  border-radius: 50%;
  border: 1px solid rgba(11, 44, 111, 0.12);
  pointer-events: none;
}

.tool-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.75rem 0.75rem 0.9rem;
  border-bottom: 1px solid #e5e7eb;
  position: relative;
}

.tool-header::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0.6rem;
  bottom: 0.6rem;
  width: 4px;
  border-radius: 999px;
  background: #0b2c6f;
}

.tool-title {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  padding-left: 0.85rem;
}

.tool-heading {
  font-size: 1.2rem;
  font-weight: 700;
  color: #0b2c6f;
}

.tool-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #0b2c6f;
  color: #ffffff;
}

.tool-icon.is-gold {
  background: #f2c300;
  color: #0b2c6f;
}

.tool-icon svg {
  width: 18px;
  height: 18px;
}

.tool-accent {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.tool-accent-bar {
  width: 46px;
  height: 8px;
  border-radius: 999px;
  background: #e5e7eb;
}

.tool-accent-bar.is-primary {
  width: 64px;
  background: #0b2c6f;
}

.tool-accent-bar.is-gold {
  background: #f2c300;
}

.tool-accent-bar.is-neutral {
  width: 32px;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
}

.control-strip {
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 18px;
  padding: 0.85rem 1rem;
}

.table-state {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  color: #6b7280;
  font-weight: 600;
}

.table-state-icon {
  width: 14px;
  height: 14px;
  border-radius: 4px;
  border: 2px solid #cbd5f5;
  background: #ffffff;
}

.table-row {
  position: relative;
  transition: background 0.2s ease;
}

.table-row td:first-child {
  padding-left: 1rem;
}

.table-row:hover {
  background: #f8fafc;
}

.row-status-success {
  border-left: 4px solid #2e7d32;
}

.row-status-danger {
  border-left: 4px solid #c0392b;
}

.row-status-warning {
  border-left: 4px solid #f2c300;
}

.row-status-info {
  border-left: 4px solid #0b2c6f;
}

.row-status-neutral {
  border-left: 4px solid #e5e7eb;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.3rem 0.85rem;
  border-radius: 999px;
  font-weight: 700;
  text-transform: capitalize;
  border: 1px solid #e5e7eb;
  background: #ffffff;
  color: #111827;
}

.status-pill::before {
  content: '';
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: currentColor;
}

.status-pill.is-success {
  border-color: rgba(46, 125, 50, 0.4);
  color: #2e7d32;
}

.status-pill.is-danger {
  border-color: rgba(192, 57, 43, 0.4);
  color: #c0392b;
}

.status-pill.is-warning {
  border-color: rgba(242, 195, 0, 0.55);
  color: #a07200;
}

.status-pill.is-info {
  border-color: rgba(11, 44, 111, 0.35);
  color: #0b2c6f;
}

.status-pill.is-neutral {
  border-color: #e5e7eb;
  color: #6b7280;
}

.resident-layout {
  display: grid;
  grid-template-columns: minmax(0, 360px) minmax(0, 1fr);
  gap: 1.5rem;
  align-items: stretch;
}

.resident-side {
  display: grid;
  gap: 1.25rem;
  grid-template-rows: auto 1fr;
}

.resident-overview {
  position: relative;
  overflow: hidden;
  border-radius: 24px;
  padding: 1.5rem;
  color: #f8fafc;
  background: linear-gradient(140deg, #0b2c6f 0%, #134793 55%, #0b2c6f 100%);
  box-shadow: 0 18px 40px rgba(11, 44, 111, 0.18);
}

.resident-overview::after {
  content: '';
  position: absolute;
  width: 160px;
  height: 160px;
  border-radius: 50%;
  border: 1px solid rgba(242, 195, 0, 0.5);
  top: -40px;
  right: -40px;
  opacity: 0.7;
  pointer-events: none;
}

.resident-kicker {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.22em;
  color: rgba(226, 232, 240, 0.8);
}

.resident-title {
  margin-top: 0.5rem;
  font-size: 1.55rem;
  font-weight: 700;
}

.resident-subtitle {
  margin-top: 0.6rem;
  color: rgba(226, 232, 240, 0.9);
}

.resident-metric-grid {
  margin-top: 1.25rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.resident-metric-card {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  padding: 0.85rem;
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  background: #14387a;
  color: #f8fafc;
}

.resident-metric-card strong {
  font-size: 1.4rem;
  font-weight: 700;
}

.resident-metric-card small {
  font-size: 0.85rem;
  color: rgba(226, 232, 240, 0.85);
}

.resident-metric-card.is-success {
  background: #2e7d32;
  border-color: rgba(46, 125, 50, 0.7);
  color: #ffffff;
}

.resident-metric-card.is-success small,
.resident-metric-card.is-danger small {
  color: rgba(255, 255, 255, 0.85);
}

.resident-metric-card.is-warning {
  background: #f2c300;
  border-color: rgba(242, 195, 0, 0.8);
  color: #0b2c6f;
}

.resident-metric-card.is-warning small {
  color: rgba(11, 44, 111, 0.8);
}

.resident-metric-card.is-danger {
  background: #c0392b;
  border-color: rgba(192, 57, 43, 0.7);
  color: #ffffff;
}

.resident-filters-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
}

.resident-filters-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.resident-filters-header h4 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0b2c6f;
}

.resident-filters-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.resident-filter-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}

.resident-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  font-weight: 600;
  color: #374151;
}

.resident-input {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 0.65rem 0.85rem;
  font-size: 1rem;
  background: #f8fafc;
}

.resident-filter-actions {
  margin-top: auto;
  padding-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.resident-primary,
.resident-secondary,
.resident-tertiary,
.resident-danger {
  border-radius: 999px;
  padding: 0.55rem 1.1rem;
  font-weight: 600;
  font-size: 0.95rem;
  border: 1px solid transparent;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.resident-primary {
  background: #0b2c6f;
  color: #ffffff;
  border-color: #0b2c6f;
  box-shadow: 0 10px 18px rgba(11, 44, 111, 0.22);
}

.resident-secondary {
  background: #f2c300;
  color: #0b2c6f;
  border-color: rgba(242, 195, 0, 0.6);
}

.resident-tertiary {
  background: #ffffff;
  color: #374151;
  border-color: #e5e7eb;
}

.resident-danger {
  background: #c0392b;
  color: #ffffff;
  border-color: #c0392b;
}

.resident-primary:hover,
.resident-secondary:hover,
.resident-tertiary:hover,
.resident-danger:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 20px rgba(15, 23, 42, 0.12);
}

.resident-tertiary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  box-shadow: none;
  transform: none;
}

.resident-table-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
  display: grid;
  grid-template-rows: auto 1fr auto;
}

.resident-meta {
  margin-top: 0.2rem;
  font-size: 0.85rem;
  color: #64748b;
}

.resident-table-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.resident-table-header h3 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #0b2c6f;
}

.resident-table-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.resident-table-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.resident-chip {
  border-radius: 999px;
  padding: 0.2rem 0.7rem;
  font-size: 0.85rem;
  font-weight: 600;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  color: #374151;
}

.resident-chip.is-muted {
  background: #ffffff;
  color: #6b7280;
}

.resident-table-wrapper {
  overflow-x: auto;
  min-height: 360px;
}

.resident-identity {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.resident-avatar {
  width: 40px;
  height: 40px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  font-weight: 700;
  letter-spacing: 0.08em;
  background: #0b2c6f;
  color: #ffffff;
}

.resident-name {
  font-weight: 600;
  color: #111827;
}

.resident-id {
  font-size: 0.85rem;
  color: #6b7280;
}

.resident-email {
  font-weight: 500;
  color: #111827;
}

.resident-id-link {
  display: grid;
  gap: 0.35rem;
  font-size: 0.95rem;
}

.resident-id-link a {
  color: #0b2c6f;
  font-weight: 700;
  text-decoration: none;
}

.resident-id-link a:hover {
  text-decoration: underline;
}

.resident-id-missing {
  color: #94a3b8;
  font-weight: 600;
}

.resident-id-status {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: #64748b;
  font-weight: 700;
}

.resident-action-group {
  display: flex;
  justify-content: flex-end;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.resident-action {
  border-radius: 999px;
  padding: 0.25rem 0.75rem;
  font-size: 0.85rem;
  font-weight: 600;
  border: 1px solid transparent;
  background: #ffffff;
  color: #0b2c6f;
}

.resident-action.is-outline {
  border-color: #0b2c6f;
  background: #f8fafc;
}

.resident-action.is-success {
  border-color: rgba(46, 125, 50, 0.4);
  color: #2e7d32;
  background: #f0faf2;
}

.resident-action.is-danger {
  border-color: rgba(192, 57, 43, 0.4);
  color: #c0392b;
  background: #fff5f5;
}

.resident-action.is-warning {
  border-color: rgba(242, 195, 0, 0.55);
  color: #8a6d00;
  background: #fff9e6;
}

.resident-action.is-ghost {
  border-color: #e5e7eb;
  color: #c0392b;
  background: #ffffff;
}

.resident-pagination {
  margin-top: 1.25rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.resident-range {
  font-weight: 600;
  color: #6b7280;
}

.resident-page-controls {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.resident-page-label {
  font-weight: 600;
  color: #0b2c6f;
}

.service-layout {
  display: grid;
  grid-template-columns: minmax(0, 420px) minmax(0, 1fr);
  gap: 1.5rem;
  align-items: stretch;
}

.service-side {
  display: grid;
  gap: 1.25rem;
  grid-template-rows: auto 1fr;
}

.service-overview {
  position: relative;
  overflow: hidden;
  border-radius: 24px;
  padding: 1.5rem;
  background: linear-gradient(140deg, #fff7d1 0%, #ffffff 65%);
  border: 1px solid rgba(242, 195, 0, 0.55);
  color: #0b2c6f;
  box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
}

.service-overview::after {
  content: '';
  position: absolute;
  width: 160px;
  height: 160px;
  border-radius: 50%;
  border: 1px solid rgba(11, 44, 111, 0.2);
  right: -40px;
  top: -40px;
  pointer-events: none;
}

.service-kicker {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: rgba(11, 44, 111, 0.7);
}

.service-title {
  margin-top: 0.5rem;
  font-size: 1.5rem;
  font-weight: 700;
}

.service-subtitle {
  margin-top: 0.6rem;
  color: rgba(11, 44, 111, 0.75);
}

.service-metric-grid {
  margin-top: 1.25rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.service-metric-card {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  padding: 0.85rem;
  border-radius: 16px;
  background: #0b2c6f;
  color: #ffffff;
  border: 1px solid rgba(11, 44, 111, 0.6);
}

.service-metric-card strong {
  font-size: 1.4rem;
  font-weight: 700;
}

.service-metric-card small {
  font-size: 0.85rem;
  color: rgba(248, 250, 252, 0.85);
}

.service-metric-card.is-success {
  background: #2e7d32;
  border-color: rgba(46, 125, 50, 0.7);
}

.service-metric-card.is-danger {
  background: #c0392b;
  border-color: rgba(192, 57, 43, 0.7);
}

.service-metric-card.is-accent {
  background: #f2c300;
  color: #0b2c6f;
  border-color: rgba(242, 195, 0, 0.85);
}

.service-metric-card.is-accent small {
  color: rgba(11, 44, 111, 0.8);
}

.service-action-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
}

.service-action-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.service-action-header h4 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0b2c6f;
}

.service-action-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.service-action-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}

.service-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  font-weight: 600;
  color: #374151;
}

.service-input {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 0.65rem 0.85rem;
  font-size: 1rem;
  background: #f8fafc;
}

.service-action-footer {
  margin-top: auto;
  padding-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.service-table-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
  display: grid;
  grid-template-rows: auto 1fr auto;
}

.service-table-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.service-table-header h3 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #0b2c6f;
}

.service-table-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.service-table-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.service-chip {
  border-radius: 999px;
  padding: 0.2rem 0.7rem;
  font-size: 0.85rem;
  font-weight: 600;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  color: #374151;
}

.service-chip.is-muted {
  background: #ffffff;
  color: #6b7280;
}

.service-table-wrapper {
  overflow-x: auto;
  min-height: 360px;
}

.service-identity {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.service-badge {
  width: 46px;
  height: 46px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  font-weight: 700;
  letter-spacing: 0.08em;
  background: #0b2c6f;
  color: #ffffff;
}

.service-name {
  font-weight: 600;
  color: #111827;
}

.service-id {
  font-size: 0.85rem;
  color: #6b7280;
}

.service-code {
  font-weight: 600;
  color: #0b2c6f;
}

.service-action-group {
  display: flex;
  justify-content: flex-end;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.service-action {
  border-radius: 999px;
  padding: 0.25rem 0.75rem;
  font-size: 0.85rem;
  font-weight: 600;
  border: 1px solid transparent;
  background: #ffffff;
  color: #0b2c6f;
}

.service-action.is-outline {
  border-color: #0b2c6f;
  background: #f8fafc;
}

.service-action.is-success {
  border-color: rgba(46, 125, 50, 0.4);
  color: #2e7d32;
  background: #f0faf2;
}

.service-action.is-danger {
  border-color: rgba(192, 57, 43, 0.4);
  color: #c0392b;
  background: #fff5f5;
}

.service-pagination {
  margin-top: 1.25rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.service-range {
  font-weight: 600;
  color: #6b7280;
}

.service-page-controls {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.service-page-label {
  font-weight: 600;
  color: #0b2c6f;
}

.queue-command-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.8fr) minmax(0, 1fr);
  gap: 1.1rem;
  align-items: stretch;
}

.queue-primary-stack {
  display: grid;
}

.queue-serving-card {
  border-radius: 26px;
  padding: 1.3rem 1.4rem;
  background: linear-gradient(145deg, #0b2c6f 0%, #123d86 60%);
  color: #f8fafc;
  box-shadow: 0 20px 45px rgba(11, 44, 111, 0.22);
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(242, 195, 0, 0.35);
  display: grid;
  align-content: center;
  justify-items: center;
}

.queue-serving-card::after {
  content: '';
  position: absolute;
  width: 140px;
  height: 140px;
  border-radius: 50%;
  border: 1px solid rgba(242, 195, 0, 0.6);
  right: -45px;
  top: -45px;
  pointer-events: none;
}

.queue-serving-header {
  display: grid;
  justify-items: center;
  text-align: center;
  gap: 0.35rem;
  position: relative;
  width: 100%;
}

.queue-serving-body {
  margin-top: 1rem;
  display: grid;
  gap: 1.1rem;
  justify-items: center;
  text-align: center;
  width: 100%;
}

.queue-card-label {
  text-transform: uppercase;
  letter-spacing: 0.18em;
  font-size: 0.68rem;
  font-weight: 700;
  color: #64748b;
}

.queue-side-stack {
  display: grid;
  gap: 0.85rem;
  align-content: start;
}

.queue-next-card {
  border-radius: 22px;
  padding: 1.1rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  display: grid;
  gap: 0.75rem;
}

.queue-next-card.is-inline {
  border-color: rgba(11, 44, 111, 0.12);
  box-shadow: 0 18px 40px rgba(15, 23, 42, 0.09);
}

.queue-monitor-card {
  border-radius: 22px;
  padding: 1.1rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  display: grid;
  gap: 0.85rem;
}

.queue-monitor-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 0.75rem;
}

.queue-monitor-subtitle {
  margin: 0.35rem 0 0;
  color: #6b7280;
  font-size: 0.85rem;
}

.queue-monitor-pill {
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  background: rgba(11, 44, 111, 0.1);
  color: #0b2c6f;
}

.queue-monitor-frame {
  width: 100%;
  height: 320px;
  border-radius: 18px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  background: #0f172a;
  position: relative;
}

.queue-monitor-frame iframe {
  border: none;
}

.queue-empty-notice {
  display: flex;
  gap: 0.7rem;
  align-items: center;
  padding: 0.75rem 0.9rem;
  border-radius: 16px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  color: #0f172a;
}

.queue-empty-notice strong {
  display: block;
  font-size: 0.95rem;
}

.queue-empty-notice span {
  font-size: 0.85rem;
  color: #64748b;
}

.queue-empty-icon {
  width: 2.2rem;
  height: 2.2rem;
  border-radius: 12px;
  display: grid;
  place-items: center;
  background: #e2e8f0;
  color: #0b2c6f;
}

.queue-next-block {
  display: grid;
  gap: 0.65rem;
  padding: 0.75rem;
  border-radius: 16px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.queue-next-label {
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.65rem;
  font-weight: 700;
  color: #64748b;
}

.queue-next-main {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.queue-call-cta {
  padding: 0.7rem 1.6rem;
  font-size: 1rem;
  border-radius: 999px;
  letter-spacing: 0.01em;
  box-shadow: 0 14px 30px rgba(11, 44, 111, 0.22);
  background: linear-gradient(135deg, #0b2c6f 0%, #1d4ed8 100%);
  border-color: #1d4ed8;
}

.queue-manual-cta {
  align-self: flex-start;
  padding: 0.55rem 1.3rem;
  border-radius: 999px;
  font-size: 0.95rem;
}

.queue-next-ticket {
  font-size: 1.6rem;
  font-weight: 700;
  color: #0b2c6f;
}

.queue-next-resident {
  margin-top: 0.35rem;
  color: #111827;
  font-weight: 600;
}

.queue-next-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.85rem;
  font-size: 0.85rem;
  color: #6b7280;
}

.queue-call-note {
  font-size: 0.85rem;
  color: #6b7280;
}

.queue-serving-main {
  display: grid;
  gap: 0.45rem;
  justify-items: center;
}

.queue-serving-ticket {
  font-size: 4.6rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  color: #f2c300;
}

.queue-serving-resident {
  font-size: 1.9rem;
  font-weight: 600;
}

.queue-serving-service {
  font-size: 1.3rem;
  color: rgba(226, 232, 240, 0.85);
}

.queue-serving-service-list {
  font-size: 0.9rem;
  color: rgba(226, 232, 240, 0.7);
  text-align: center;
  max-width: 560px;
}

.queue-serving-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.85rem;
  width: min(100%, 680px);
}

.queue-serving-detail {
  border-radius: 14px;
  padding: 0.7rem 0.85rem;
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.18);
  display: grid;
  gap: 0.2rem;
  text-align: center;
}

.queue-serving-detail span {
  text-transform: uppercase;
  letter-spacing: 0.18em;
  font-size: 0.7rem;
  color: rgba(226, 232, 240, 0.75);
  font-weight: 700;
}

.queue-serving-detail strong {
  font-size: 1.15rem;
  color: #ffffff;
}

.queue-serving-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  justify-content: center;
}

.queue-serving-actions .resident-secondary,
.queue-serving-actions .resident-primary,
.queue-serving-actions .resident-danger {
  padding: 0.75rem 1.7rem;
  font-size: 1.05rem;
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.18);
}

.queue-serving-actions .resident-secondary {
  background: linear-gradient(135deg, #f2c300 0%, #f59e0b 100%);
  border-color: rgba(245, 158, 11, 0.8);
  color: #0b2c6f;
}

.queue-serving-actions .resident-primary {
  background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
  border-color: rgba(22, 163, 74, 0.9);
}

.queue-serving-actions .resident-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  border-color: rgba(220, 38, 38, 0.9);
}

.queue-serving-empty {
  margin: 0.6rem 0 0;
  color: rgba(226, 232, 240, 0.8);
  font-size: 0.9rem;
  text-align: center;
  display: grid;
  gap: 0.9rem;
  justify-items: center;
}

.queue-serving-empty-text {
  margin: 0;
}

.queue-serving-suggest-card {
  width: min(100%, 520px);
  padding: 0.9rem 1.1rem;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.14);
  border: 1px solid rgba(255, 255, 255, 0.22);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  text-align: left;
}

.queue-serving-suggest-label {
  text-transform: uppercase;
  letter-spacing: 0.18em;
  font-size: 0.65rem;
  color: rgba(226, 232, 240, 0.7);
  font-weight: 700;
  display: block;
  margin-bottom: 0.35rem;
}

.queue-serving-suggest-ticket {
  font-size: 1.8rem;
  font-weight: 700;
  color: #f2c300;
  letter-spacing: 0.08em;
}

.queue-serving-suggest-resident {
  font-size: 1rem;
  font-weight: 600;
  color: #ffffff;
}

.queue-serving-suggest-service {
  font-size: 0.85rem;
  color: rgba(226, 232, 240, 0.75);
}

.queue-serving-suggest-cta {
  padding: 0.65rem 1.5rem;
  border-radius: 999px;
  font-size: 0.95rem;
  white-space: nowrap;
}

.queue-serving-empty-state {
  font-size: 0.85rem;
  color: rgba(226, 232, 240, 0.65);
}

.queue-serving-status {
  position: absolute;
  top: 0.35rem;
  right: 0.35rem;
}

.queue-metric-card {
  border-radius: 22px;
  padding: 1.1rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  display: grid;
  gap: 0.75rem;
}

.queue-metric-card.is-summary {
  gap: 0.85rem;
}

.queue-snapshot-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.queue-snapshot-card {
  border-radius: 16px;
  padding: 0.75rem 0.85rem;
  color: #ffffff;
  display: grid;
  gap: 0.3rem;
  border: 1px solid rgba(11, 44, 111, 0.6);
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.1);
}

.queue-snapshot-card span {
  font-size: 0.68rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  font-weight: 700;
}

.queue-snapshot-card strong {
  font-size: 1.3rem;
  font-weight: 700;
}

.queue-snapshot-card small {
  font-size: 0.75rem;
  color: rgba(248, 250, 252, 0.85);
}

.queue-snapshot-card.is-waiting {
  background: #f2c300;
  border-color: rgba(242, 195, 0, 0.85);
  color: #0b2c6f;
}

.queue-snapshot-card.is-serving {
  background: #0b2c6f;
  border-color: rgba(11, 44, 111, 0.6);
  color: #ffffff;
}

.queue-snapshot-card.is-done {
  background: #2e7d32;
  border-color: rgba(46, 125, 50, 0.7);
  color: #ffffff;
}

.queue-snapshot-card.is-cancelled {
  background: #c0392b;
  border-color: rgba(192, 57, 43, 0.7);
  color: #ffffff;
}

.queue-snapshot-card.is-waiting small,
.queue-snapshot-card.is-waiting span {
  color: rgba(11, 44, 111, 0.8);
}

.queue-snapshot-footer {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
  padding-top: 0.2rem;
}

.queue-snapshot-footer span {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.18em;
  color: #6b7280;
  font-weight: 700;
}

.queue-snapshot-footer strong {
  display: block;
  margin-top: 0.25rem;
  font-size: 1.15rem;
  color: #0b2c6f;
}

.queue-metric-note {
  color: #6b7280;
  font-size: 0.9rem;
}

.queue-board-grid {
  display: grid;
  grid-template-columns: minmax(0, 2.1fr) minmax(0, 1fr);
  gap: 1.5rem;
  align-items: start;
}

.queue-kicker {
  font-size: 0.68rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: rgba(226, 232, 240, 0.8);
}

.queue-title {
  margin-top: 0.25rem;
  font-size: 1.5rem;
  font-weight: 700;
}

.queue-subtitle {
  margin-top: 0.35rem;
  font-size: 0.95rem;
  color: rgba(226, 232, 240, 0.9);
}

.queue-field {
  margin-top: 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  font-weight: 600;
  color: #374151;
}

.queue-input {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 0.65rem 0.85rem;
  font-size: 1rem;
  background: #f8fafc;
  color: #111827;
}

.queue-filter-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
}

.queue-filter-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.queue-filter-header h4 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0b2c6f;
}

.queue-filter-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.queue-filter-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}

.queue-filter-card .queue-field {
  color: #374151;
}

.queue-filter-card .queue-input {
  background: #f8fafc;
  color: #111827;
  border-color: #e5e7eb;
}

.queue-filter-actions {
  margin-top: auto;
  padding-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.queue-table-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
  display: grid;
  grid-template-rows: auto 1fr auto;
}

.queue-table-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.queue-table-header h3 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #0b2c6f;
}

.queue-table-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.queue-table-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.queue-chip {
  border-radius: 999px;
  padding: 0.2rem 0.7rem;
  font-size: 0.85rem;
  font-weight: 600;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  color: #374151;
}

.queue-chip.is-muted {
  background: #ffffff;
  color: #6b7280;
}

.queue-table-wrapper {
  overflow-x: auto;
  min-height: 360px;
}

.queue-ticket {
  display: grid;
  gap: 0.2rem;
}

.queue-ticket-no {
  font-weight: 700;
  color: #0b2c6f;
}

.queue-ticket small {
  color: #6b7280;
}

.queue-service {
  display: grid;
  gap: 0.2rem;
}

.queue-service-name {
  font-weight: 600;
  color: #111827;
}

.queue-service small {
  color: #6b7280;
}

.queue-action-group {
  display: flex;
  justify-content: flex-end;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.queue-action {
  border-radius: 999px;
  padding: 0.25rem 0.75rem;
  font-size: 0.85rem;
  font-weight: 600;
  border: 1px solid transparent;
  background: #ffffff;
}

.queue-action.is-primary {
  border-color: rgba(11, 44, 111, 0.4);
  color: #0b2c6f;
  background: #eef2ff;
}

.queue-action.is-success {
  border-color: rgba(46, 125, 50, 0.4);
  color: #2e7d32;
  background: #f0faf2;
}

.queue-action.is-danger {
  border-color: rgba(192, 57, 43, 0.4);
  color: #c0392b;
  background: #fff5f5;
}

.queue-action-muted {
  color: #94a3b8;
  font-weight: 600;
}

.queue-pagination {
  margin-top: 1.25rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.queue-range {
  font-weight: 600;
  color: #6b7280;
}

.queue-page-controls {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.queue-page-label {
  font-weight: 600;
  color: #0b2c6f;
}

.transaction-layout {
  display: grid;
  grid-template-columns: minmax(0, 420px) minmax(0, 1fr);
  gap: 1.5rem;
  align-items: stretch;
}

.transaction-side {
  display: grid;
  gap: 1.25rem;
  grid-template-rows: auto 1fr;
}

.transaction-overview {
  position: relative;
  overflow: hidden;
  border-radius: 24px;
  padding: 1.5rem;
  background: linear-gradient(140deg, #ecfdf3 0%, #ffffff 65%);
  border: 1px solid rgba(46, 125, 50, 0.3);
  color: #0b2c6f;
  box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
}

.transaction-overview::after {
  content: '';
  position: absolute;
  width: 160px;
  height: 160px;
  border-radius: 50%;
  border: 1px solid rgba(11, 44, 111, 0.2);
  right: -40px;
  top: -40px;
  pointer-events: none;
}

.transaction-kicker {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: rgba(11, 44, 111, 0.7);
}

.transaction-title {
  margin-top: 0.5rem;
  font-size: 1.5rem;
  font-weight: 700;
}

.transaction-subtitle {
  margin-top: 0.6rem;
  color: rgba(11, 44, 111, 0.75);
}

.transaction-metric-grid {
  margin-top: 1.25rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.transaction-metric-card {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  padding: 0.85rem;
  border-radius: 16px;
  background: #0b2c6f;
  color: #ffffff;
  border: 1px solid rgba(11, 44, 111, 0.6);
}

.transaction-metric-card strong {
  font-size: 1.4rem;
  font-weight: 700;
}

.transaction-metric-card small {
  font-size: 0.85rem;
  color: rgba(248, 250, 252, 0.85);
}

.transaction-metric-card.is-success {
  background: #2e7d32;
  border-color: rgba(46, 125, 50, 0.7);
}

.transaction-metric-card.is-danger {
  background: #c0392b;
  border-color: rgba(192, 57, 43, 0.7);
}

.transaction-metric-card.is-accent {
  background: #f2c300;
  color: #0b2c6f;
  border-color: rgba(242, 195, 0, 0.85);
}

.transaction-metric-card.is-accent small {
  color: rgba(11, 44, 111, 0.8);
}

.transaction-filter-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
}

.transaction-filter-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.transaction-filter-header h4 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0b2c6f;
}

.transaction-filter-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.transaction-filter-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}

.transaction-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  font-weight: 600;
  color: #374151;
}

.transaction-input {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 0.65rem 0.85rem;
  font-size: 1rem;
  background: #f8fafc;
}

.transaction-filter-actions {
  margin-top: auto;
  padding-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.transaction-table-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
  display: grid;
  grid-template-rows: auto 1fr auto;
}

.transaction-table-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.transaction-table-header h3 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #0b2c6f;
}

.transaction-table-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.transaction-table-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.transaction-chip {
  border-radius: 999px;
  padding: 0.2rem 0.7rem;
  font-size: 0.85rem;
  font-weight: 600;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  color: #374151;
}

.transaction-chip.is-muted {
  background: #ffffff;
  color: #6b7280;
}

.transaction-table-wrapper {
  overflow-x: auto;
  min-height: 360px;
}

.transaction-ticket {
  display: grid;
  gap: 0.2rem;
}

.transaction-ticket-no {
  font-weight: 700;
  color: #0b2c6f;
}

.transaction-ticket small {
  color: #6b7280;
}

.transaction-service {
  display: grid;
  gap: 0.2rem;
}

.transaction-service-name {
  font-weight: 600;
  color: #111827;
}

.transaction-service small {
  color: #6b7280;
}

.transaction-pagination {
  margin-top: 1.25rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.transaction-range {
  font-weight: 600;
  color: #6b7280;
}

.transaction-page-controls {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.transaction-page-label {
  font-weight: 600;
  color: #0b2c6f;
}

.kiosk-layout {
  display: grid;
  grid-template-columns: minmax(0, 420px) minmax(0, 1fr);
  gap: 1.5rem;
  align-items: stretch;
}

.kiosk-side {
  display: grid;
  gap: 1.25rem;
  grid-template-rows: auto 1fr;
}

.kiosk-overview {
  position: relative;
  overflow: hidden;
  border-radius: 24px;
  padding: 1.5rem;
  background: linear-gradient(140deg, #eaf2ff 0%, #ffffff 65%);
  border: 1px solid rgba(11, 44, 111, 0.2);
  color: #0b2c6f;
  box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
}

.kiosk-overview::after {
  content: '';
  position: absolute;
  width: 160px;
  height: 160px;
  border-radius: 50%;
  border: 1px solid rgba(11, 44, 111, 0.2);
  right: -40px;
  top: -40px;
  pointer-events: none;
}

.kiosk-kicker {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: rgba(11, 44, 111, 0.7);
}

.kiosk-title {
  margin-top: 0.5rem;
  font-size: 1.5rem;
  font-weight: 700;
}

.kiosk-subtitle {
  margin-top: 0.6rem;
  color: rgba(11, 44, 111, 0.75);
}

.kiosk-metric-grid {
  margin-top: 1.25rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.kiosk-metric-card {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  padding: 0.85rem;
  border-radius: 16px;
  background: #0b2c6f;
  color: #ffffff;
  border: 1px solid rgba(11, 44, 111, 0.6);
}

.kiosk-metric-card strong {
  font-size: 1.4rem;
  font-weight: 700;
}

.kiosk-metric-card small {
  font-size: 0.85rem;
  color: rgba(248, 250, 252, 0.85);
}

.kiosk-metric-card.is-success {
  background: #2e7d32;
  border-color: rgba(46, 125, 50, 0.7);
}

.kiosk-metric-card.is-danger {
  background: #c0392b;
  border-color: rgba(192, 57, 43, 0.7);
}

.kiosk-metric-card.is-accent {
  background: #f2c300;
  color: #0b2c6f;
  border-color: rgba(242, 195, 0, 0.85);
}

.kiosk-metric-card.is-accent small {
  color: rgba(11, 44, 111, 0.8);
}

.kiosk-action-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
}

.kiosk-action-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.kiosk-action-header h4 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0b2c6f;
}

.kiosk-action-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.kiosk-action-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}

.kiosk-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  font-weight: 600;
  color: #374151;
}

.kiosk-input {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 0.65rem 0.85rem;
  font-size: 1rem;
  background: #f8fafc;
}

.kiosk-action-footer {
  margin-top: auto;
  padding-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.kiosk-table-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
  display: grid;
  grid-template-rows: auto auto 1fr auto;
}

.kiosk-table-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.kiosk-table-header h3 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #0b2c6f;
}

.kiosk-table-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.kiosk-table-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.kiosk-chip {
  border-radius: 999px;
  padding: 0.2rem 0.7rem;
  font-size: 0.85rem;
  font-weight: 600;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  color: #374151;
}

.kiosk-chip.is-muted {
  background: #ffffff;
  color: #6b7280;
}

.kiosk-filter-row {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr)) auto;
  gap: 0.9rem;
  margin-bottom: 1rem;
}

.kiosk-filter-actions {
  display: flex;
  align-items: flex-end;
}

.kiosk-table-wrapper {
  overflow-x: auto;
  min-height: 360px;
}

.kiosk-device {
  display: grid;
  gap: 0.2rem;
}

.kiosk-device-id {
  font-weight: 700;
  color: #0b2c6f;
}

.kiosk-device small {
  color: #6b7280;
}

.kiosk-name {
  font-weight: 600;
  color: #111827;
}

.kiosk-token {
  font-weight: 600;
  color: #0b2c6f;
}

.kiosk-last {
  color: #6b7280;
}

.kiosk-pagination {
  margin-top: 1.25rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.kiosk-range {
  font-weight: 600;
  color: #6b7280;
}

.kiosk-page-controls {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.kiosk-page-label {
  font-weight: 600;
  color: #0b2c6f;
}

.admin-users-layout {
  display: grid;
  grid-template-columns: minmax(0, 420px) minmax(0, 1fr);
  gap: 1.5rem;
  align-items: stretch;
}

.admin-users-side {
  display: grid;
  gap: 1.25rem;
  grid-template-rows: auto auto 1fr;
}

.admin-users-overview {
  position: relative;
  overflow: hidden;
  border-radius: 24px;
  padding: 1.5rem;
  background: linear-gradient(140deg, #f0f4ff 0%, #ffffff 65%);
  border: 1px solid rgba(11, 44, 111, 0.2);
  color: #0b2c6f;
  box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
}

.admin-users-overview::after {
  content: '';
  position: absolute;
  width: 160px;
  height: 160px;
  border-radius: 50%;
  border: 1px solid rgba(11, 44, 111, 0.2);
  right: -40px;
  top: -40px;
  pointer-events: none;
}

.admin-users-kicker {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: rgba(11, 44, 111, 0.7);
}

.admin-users-title {
  margin-top: 0.5rem;
  font-size: 1.5rem;
  font-weight: 700;
}

.admin-users-subtitle {
  margin-top: 0.6rem;
  color: rgba(11, 44, 111, 0.75);
}

.admin-users-metric-grid {
  margin-top: 1.25rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.admin-users-metric-card {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  padding: 0.85rem;
  border-radius: 16px;
  background: #0b2c6f;
  color: #ffffff;
  border: 1px solid rgba(11, 44, 111, 0.6);
}

.admin-users-metric-card strong {
  font-size: 1.4rem;
  font-weight: 700;
}

.admin-users-metric-card small {
  font-size: 0.85rem;
  color: rgba(248, 250, 252, 0.85);
}

.admin-users-metric-card.is-success {
  background: #2e7d32;
  border-color: rgba(46, 125, 50, 0.7);
}

.admin-users-metric-card.is-accent {
  background: #f2c300;
  color: #0b2c6f;
  border-color: rgba(242, 195, 0, 0.85);
}

.admin-users-metric-card.is-accent small,
.admin-users-metric-card.is-neutral small {
  color: rgba(11, 44, 111, 0.8);
}

.admin-users-metric-card.is-neutral {
  background: #e5e7eb;
  color: #0b2c6f;
  border-color: rgba(15, 23, 42, 0.1);
}

.admin-users-form-card,
.admin-users-filter-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
}

.admin-users-form-header,
.admin-users-filter-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.admin-users-form-header h4,
.admin-users-filter-header h4 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0b2c6f;
}

.admin-users-form-header p,
.admin-users-filter-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.admin-users-form-grid,
.admin-users-filter-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}

.admin-users-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  font-weight: 600;
  color: #374151;
}

.admin-users-field-full {
  grid-column: 1 / -1;
}

.admin-users-input {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 0.65rem 0.85rem;
  font-size: 1rem;
  background: #f8fafc;
}

.admin-users-form-actions,
.admin-users-filter-actions {
  margin-top: auto;
  padding-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.admin-users-table-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
  display: grid;
  grid-template-rows: auto 1fr auto;
}

.admin-users-table-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.admin-users-table-header h3 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #0b2c6f;
}

.admin-users-table-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.admin-users-table-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.admin-users-chip {
  border-radius: 999px;
  padding: 0.2rem 0.7rem;
  font-size: 0.85rem;
  font-weight: 600;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  color: #374151;
}

.admin-users-chip.is-muted {
  background: #ffffff;
  color: #6b7280;
}

.admin-users-table-wrapper {
  overflow-x: auto;
  min-height: 360px;
}

.admin-users-identity {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.admin-users-avatar {
  width: 40px;
  height: 40px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  font-weight: 700;
  letter-spacing: 0.08em;
  background: #0b2c6f;
  color: #ffffff;
}

.admin-users-name {
  font-weight: 600;
  color: #111827;
}

.admin-users-id {
  font-size: 0.85rem;
  color: #6b7280;
}

.admin-users-action {
  border-radius: 999px;
  padding: 0.25rem 0.75rem;
  font-size: 0.85rem;
  font-weight: 600;
  border: 1px solid #0b2c6f;
  background: #f8fafc;
  color: #0b2c6f;
}

.admin-users-pagination {
  margin-top: 1.25rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.admin-users-range {
  font-weight: 600;
  color: #6b7280;
}

.admin-users-page-controls {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.admin-users-page-label {
  font-weight: 600;
  color: #0b2c6f;
}

.audit-layout {
  display: grid;
  grid-template-columns: minmax(0, 420px) minmax(0, 1fr);
  gap: 1.5rem;
  align-items: stretch;
}

.audit-side {
  display: grid;
  gap: 1.25rem;
  grid-template-rows: auto 1fr;
}

.audit-overview {
  position: relative;
  overflow: hidden;
  border-radius: 24px;
  padding: 1.5rem;
  background: linear-gradient(140deg, #f1f5ff 0%, #ffffff 65%);
  border: 1px solid rgba(11, 44, 111, 0.2);
  color: #0b2c6f;
  box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
}

.audit-overview::after {
  content: '';
  position: absolute;
  width: 160px;
  height: 160px;
  border-radius: 50%;
  border: 1px solid rgba(11, 44, 111, 0.2);
  right: -40px;
  top: -40px;
  pointer-events: none;
}

.audit-kicker {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: rgba(11, 44, 111, 0.7);
}

.audit-title {
  margin-top: 0.5rem;
  font-size: 1.5rem;
  font-weight: 700;
}

.audit-subtitle {
  margin-top: 0.6rem;
  color: rgba(11, 44, 111, 0.75);
}

.audit-metric-grid {
  margin-top: 1.25rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.audit-metric-card {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  padding: 0.85rem;
  border-radius: 16px;
  background: #0b2c6f;
  color: #ffffff;
  border: 1px solid rgba(11, 44, 111, 0.6);
}

.audit-metric-card strong {
  font-size: 1.4rem;
  font-weight: 700;
}

.audit-metric-card small {
  font-size: 0.85rem;
  color: rgba(248, 250, 252, 0.85);
}

.audit-metric-card.is-success {
  background: #2e7d32;
  border-color: rgba(46, 125, 50, 0.7);
}

.audit-metric-card.is-accent {
  background: #f2c300;
  color: #0b2c6f;
  border-color: rgba(242, 195, 0, 0.85);
}

.audit-metric-card.is-neutral {
  background: #e5e7eb;
  color: #0b2c6f;
  border-color: rgba(15, 23, 42, 0.1);
}

.audit-filter-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
}

.audit-filter-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.audit-filter-header h4 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0b2c6f;
}

.audit-filter-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.audit-filter-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}

.audit-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  font-weight: 600;
  color: #374151;
}

.audit-input {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 0.65rem 0.85rem;
  font-size: 1rem;
  background: #f8fafc;
}

.audit-filter-actions {
  margin-top: auto;
  padding-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.audit-table-card {
  border-radius: 22px;
  padding: 1.25rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
  display: grid;
  grid-template-rows: auto 1fr auto;
}

.audit-table-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.audit-table-header h3 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #0b2c6f;
}

.audit-table-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.audit-table-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.audit-chip {
  border-radius: 999px;
  padding: 0.2rem 0.7rem;
  font-size: 0.85rem;
  font-weight: 600;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  color: #374151;
}

.audit-chip.is-muted {
  background: #ffffff;
  color: #6b7280;
}

.audit-table-wrapper {
  overflow-x: auto;
  min-height: 360px;
}

.audit-time {
  display: grid;
  gap: 0.2rem;
}

.audit-time small {
  color: #6b7280;
}

.audit-action {
  font-weight: 600;
  color: #111827;
}

.audit-actor {
  color: #374151;
}

.audit-meta {
  color: #6b7280;
}

.audit-pagination {
  margin-top: 1.25rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.audit-range {
  font-weight: 600;
  color: #6b7280;
}

.audit-page-controls {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.audit-page-label {
  font-weight: 600;
  color: #0b2c6f;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  z-index: 20;
}

.modal-card {
  width: min(560px, 100%);
  background: #ffffff;
  border-radius: 22px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25);
  overflow: hidden;
}

.modal-card.is-wide {
  width: min(860px, 100%);
}

.modal-card.is-wide .modal-body {
  max-height: 72vh;
  overflow: auto;
}

.modal-card.is-danger {
  border-color: rgba(192, 57, 43, 0.4);
}

.modal-card.is-logout {
  border-color: rgba(11, 44, 111, 0.2);
  box-shadow: 0 26px 65px rgba(11, 44, 111, 0.25);
}

.modal-card.is-logout::before {
  content: '';
  display: block;
  height: 4px;
  background: linear-gradient(90deg, #0b2c6f 0%, #f2c300 55%, #f59e0b 100%);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.25rem 1.5rem;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  font-size: 1.2rem;
  font-weight: 700;
  color: #0b2c6f;
}

.modal-header p {
  margin-top: 0.25rem;
  color: #6b7280;
}

.modal-close {
  border-radius: 999px;
  padding: 0.35rem 0.9rem;
  font-size: 0.9rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  color: #374151;
}

.modal-body {
  padding: 1.25rem 1.5rem 1.5rem;
  display: grid;
  gap: 1rem;
}

.modal-summary {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  align-items: center;
  justify-content: space-between;
  padding: 0.85rem;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  background: #f8fafc;
}

.modal-summary-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.modal-summary-profile img {
  width: 54px;
  height: 54px;
  border-radius: 16px;
  object-fit: cover;
  border: 1px solid rgba(148, 163, 184, 0.4);
  background: #ffffff;
}

.modal-summary-name {
  margin: 0;
  font-weight: 700;
  color: #0f172a;
}

.modal-summary-meta {
  margin-top: 0.2rem;
  font-size: 0.85rem;
  color: #64748b;
}

.modal-summary-status {
  display: grid;
  justify-items: end;
  gap: 0.4rem;
}

.modal-summary-id {
  margin: 0;
  font-size: 0.85rem;
  font-weight: 600;
  color: #0b2c6f;
}

.modal-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.85rem;
}

.modal-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  font-weight: 600;
  color: #374151;
}

.modal-field input,
.modal-field select,
.modal-field textarea {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 0.55rem 0.75rem;
  font-size: 1rem;
  background: #f9fafb;
}

.modal-field textarea {
  resize: vertical;
}

.modal-field small {
  font-size: 0.85rem;
  font-weight: 500;
  color: #6b7280;
}

.modal-full {
  grid-column: 1 / -1;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.logout-modal-body {
  gap: 1.25rem;
}

.logout-modal-hero {
  display: flex;
  gap: 1rem;
  align-items: center;
  padding: 0.85rem;
  border-radius: 16px;
  background: linear-gradient(135deg, rgba(11, 44, 111, 0.08), rgba(242, 195, 0, 0.15));
  border: 1px solid rgba(11, 44, 111, 0.1);
}

.logout-modal-icon {
  width: 52px;
  height: 52px;
  border-radius: 16px;
  display: grid;
  place-items: center;
  background: #0b2c6f;
  color: #f2c300;
  box-shadow: 0 12px 25px rgba(11, 44, 111, 0.3);
}

.logout-modal-icon svg {
  width: 26px;
  height: 26px;
}

.logout-modal-title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #0b2c6f;
}

.logout-modal-subtitle {
  margin-top: 0.3rem;
  color: #6b7280;
}

.logout-modal-actions {
  justify-content: space-between;
  align-items: center;
}

.modal-id-card {
  margin-top: 0.5rem;
  border-radius: 16px;
  border: 1px dashed rgba(148, 163, 184, 0.5);
  background: #f8fafc;
  padding: 0.85rem;
  display: grid;
  gap: 0.6rem;
}

.modal-id-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.85rem;
  color: #64748b;
}

.modal-id-meta strong {
  text-transform: uppercase;
  letter-spacing: 0.2em;
  font-size: 0.7rem;
  color: #0b2c6f;
}

.modal-id-link {
  margin-left: auto;
  font-weight: 700;
  color: #0b2c6f;
  text-decoration: none;
}

.modal-id-link:hover {
  text-decoration: underline;
}

.modal-id-preview img {
  width: 100%;
  border-radius: 12px;
  border: 1px solid rgba(148, 163, 184, 0.35);
}

.modal-id-file {
  border-radius: 12px;
  padding: 0.65rem 0.8rem;
  background: #ffffff;
  border: 1px solid rgba(148, 163, 184, 0.35);
  font-weight: 600;
  color: #0b2c6f;
}

.modal-id-empty {
  color: #94a3b8;
  font-weight: 600;
  margin: 0;
}

.modal-status-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  padding: 0.75rem;
  border-radius: 16px;
  background: #f8fafc;
  border: 1px solid #e5e7eb;
}

.modal-status-actions .resident-primary,
.modal-status-actions .resident-secondary,
.modal-status-actions .resident-danger {
  flex: 1 1 160px;
  justify-content: center;
}

.modal-error {
  color: #c0392b;
  font-weight: 600;
}

.modal-muted {
  color: #6b7280;
}

.admin-dashboard {
  border-radius: 28px;
  padding: 2.2rem;
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 45%, #f6f1e6 100%);
  border: 1px solid #e5e7eb;
  box-shadow: 0 20px 45px rgba(15, 23, 42, 0.1);
  position: relative;
  overflow: hidden;
}

.admin-dashboard::before {
  content: '';
  position: absolute;
  right: -120px;
  top: -140px;
  width: 280px;
  height: 280px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(11, 44, 111, 0.14), transparent 70%);
  pointer-events: none;
}

.dashboard-hero {
  display: grid;
  grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
  gap: 2rem;
  align-items: stretch;
  position: relative;
  z-index: 1;
}

.dashboard-kicker {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.35em;
  color: #6b7280;
}

.dashboard-title {
  margin-top: 0.5rem;
  font-size: clamp(1.8rem, 3vw, 2.6rem);
  font-weight: 700;
  color: #0b2c6f;
}

.dashboard-subtitle {
  margin-top: 0.75rem;
  font-size: 1rem;
  color: #4b5563;
  max-width: 520px;
}

.dashboard-actions {
  margin-top: 1.5rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.dashboard-refresh {
  border: none;
  border-radius: 999px;
  padding: 0.65rem 1.4rem;
  background: #0b2c6f;
  color: #ffffff;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.dashboard-refresh:hover {
  transform: translateY(-1px);
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.15);
}

.dashboard-link {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.6rem 1.3rem;
  border-radius: 999px;
  border: 1px solid #0b2c6f;
  color: #0b2c6f;
  text-decoration: none;
  font-weight: 600;
}

.dashboard-updated {
  margin-top: 0.85rem;
  font-size: 0.9rem;
  color: #6b7280;
}

.dashboard-hero-card {
  background: linear-gradient(135deg, rgba(11, 44, 111, 0.92), rgba(9, 26, 62, 0.95));
  border-radius: 24px;
  padding: 1.8rem;
  color: #f8fafc;
  box-shadow: 0 24px 50px rgba(15, 23, 42, 0.25);
  position: relative;
  overflow: hidden;
}

.dashboard-hero-card::after {
  content: '';
  position: absolute;
  inset: -20% -30%;
  background: radial-gradient(circle, rgba(242, 195, 0, 0.25), transparent 60%);
  pointer-events: none;
}

.hero-stat {
  position: relative;
  z-index: 1;
}

.hero-stat-label {
  text-transform: uppercase;
  letter-spacing: 0.25em;
  font-size: 0.7rem;
  color: rgba(226, 232, 240, 0.8);
}

.hero-stat-value {
  margin-top: 0.5rem;
  font-size: 2.4rem;
  font-weight: 700;
  display: flex;
  align-items: baseline;
  gap: 0.4rem;
}

.hero-stat-unit {
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: rgba(226, 232, 240, 0.7);
}

.hero-stat-note {
  margin-top: 0.6rem;
  font-size: 0.95rem;
  color: rgba(226, 232, 240, 0.85);
}

.hero-mini-grid {
  margin-top: 1.5rem;
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.75rem;
  position: relative;
  z-index: 1;
}

.hero-mini {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 16px;
  padding: 0.75rem 0.9rem;
  display: grid;
  gap: 0.35rem;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

.hero-mini strong {
  font-size: 1.2rem;
  letter-spacing: 0.08em;
}

.hero-meter {
  margin-top: 1.4rem;
  position: relative;
  z-index: 1;
}

.hero-meter-track {
  height: 10px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.18);
  overflow: hidden;
}

.hero-meter-fill {
  display: block;
  height: 100%;
  background: linear-gradient(90deg, #f2c300, #ffffff);
  border-radius: inherit;
  transition: width 0.4s ease;
}

.hero-meter-labels {
  margin-top: 0.4rem;
  display: flex;
  justify-content: space-between;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: rgba(226, 232, 240, 0.7);
}

.dashboard-stat-grid {
  margin-top: 2rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
  gap: 1rem;
}

.dashboard-stat {
  background: #ffffff;
  border-radius: 20px;
  padding: 1rem 1.2rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.dashboard-stat-label {
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.25em;
  color: #6b7280;
}

.dashboard-stat-value {
  margin-top: 0.45rem;
  font-size: 1.6rem;
  font-weight: 700;
  color: #0b2c6f;
}

.dashboard-stat-meta {
  margin-top: 0.3rem;
  font-size: 0.9rem;
  color: #4b5563;
}

.dashboard-grid {
  margin-top: 2rem;
  display: grid;
  grid-template-columns: repeat(12, minmax(0, 1fr));
  gap: 1.2rem;
}

.dashboard-panel {
  grid-column: span 6;
  background: #ffffff;
  border-radius: 22px;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
}

.dashboard-panel.span-7 {
  grid-column: span 7;
}

.dashboard-panel.span-5 {
  grid-column: span 5;
}

.dashboard-panel.span-8 {
  grid-column: span 8;
}

.dashboard-panel.span-4 {
  grid-column: span 4;
}

.dashboard-panel.span-12 {
  grid-column: span 12;
}

.dashboard-panel-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.dashboard-panel-header h3 {
  margin: 0;
  font-size: 1.2rem;
  color: #0b2c6f;
}

.dashboard-panel-header p {
  margin-top: 0.35rem;
  font-size: 0.9rem;
  color: #6b7280;
}

.panel-badge {
  padding: 0.35rem 0.75rem;
  border-radius: 999px;
  background: rgba(11, 44, 111, 0.08);
  color: #0b2c6f;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.15em;
}

.panel-badge.is-gold {
  background: rgba(242, 195, 0, 0.25);
  color: #6b4e00;
}

.panel-badge.is-neutral {
  background: #f3f4f6;
  color: #374151;
}

.queue-flow {
  margin-top: 1.2rem;
}

.queue-bar {
  display: flex;
  height: 16px;
  border-radius: 999px;
  overflow: hidden;
  background: #e5e7eb;
}

.queue-segment {
  height: 100%;
  min-width: 6px;
  transition: width 0.4s ease;
}

.queue-segment.is-waiting {
  background: #f2c300;
}

.queue-segment.is-serving {
  background: #0b2c6f;
}

.queue-segment.is-done {
  background: #2e7d32;
}

.queue-segment.is-cancelled {
  background: #c0392b;
}

.queue-legend {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 0.75rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.9rem;
  color: #4b5563;
}

.legend-item strong {
  margin-left: auto;
  color: #111827;
}

.legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

.legend-dot.is-waiting {
  background: #f2c300;
}

.legend-dot.is-serving {
  background: #0b2c6f;
}

.legend-dot.is-done {
  background: #2e7d32;
}

.legend-dot.is-cancelled {
  background: #c0392b;
}

.queue-insight {
  margin-top: 1rem;
  font-size: 0.95rem;
  color: #4b5563;
}

.pipeline-list {
  margin-top: 1.2rem;
  display: grid;
  gap: 1rem;
}

.pipeline-row {
  display: grid;
  gap: 0.5rem;
}

.pipeline-label {
  display: flex;
  justify-content: space-between;
  font-size: 0.95rem;
  color: #374151;
  font-weight: 600;
}

.pipeline-track {
  height: 10px;
  border-radius: 999px;
  background: #f3f4f6;
  overflow: hidden;
}

.pipeline-fill {
  display: block;
  height: 100%;
  border-radius: inherit;
  transition: width 0.4s ease;
}

.pipeline-fill.is-success {
  background: #2e7d32;
}

.pipeline-fill.is-warning {
  background: #f2c300;
}

.pipeline-fill.is-danger {
  background: #c0392b;
}

.pipeline-note {
  margin-top: 1rem;
  font-size: 0.9rem;
  color: #6b7280;
}

.demand-list {
  margin-top: 1.2rem;
  display: grid;
  gap: 0.9rem;
}

.demand-row {
  display: grid;
  gap: 0.5rem;
}

.demand-label {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-weight: 600;
  color: #1f2937;
}

.demand-track {
  height: 10px;
  border-radius: 999px;
  background: #f3f4f6;
  overflow: hidden;
}

.demand-fill {
  display: block;
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, rgba(11, 44, 111, 0.9), rgba(242, 195, 0, 0.85));
  transition: width 0.4s ease;
}

.service-chart {
  margin-top: 1.2rem;
  display: grid;
  gap: 1.4rem;
  min-height: var(--chart-height);
}

.iso-bar-chart {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(96px, 1fr));
  gap: 1.2rem;
  align-items: end;
  min-height: 200px;
  padding-top: 0.5rem;
}

.iso-bar {
  display: grid;
  gap: 0.5rem;
  justify-items: center;
  text-align: center;
  grid-template-rows: 150px 2.6em auto;
}

.iso-bar-stack {
  position: relative;
  width: 70px;
  height: 150px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}

.iso-bar-body {
  position: relative;
  width: 44px;
  height: var(--bar-height);
  border-radius: 6px;
  background: linear-gradient(
    180deg,
    hsl(var(--bar-hue) 70% 35%) 0%,
    hsl(var(--bar-hue) 80% 50%) 55%,
    hsl(var(--bar-hue) 90% 62%) 100%
  );
  box-shadow: 0 16px 22px rgba(15, 23, 42, 0.16);
}

.iso-bar-body::before {
  content: '';
  position: absolute;
  top: -10px;
  left: 0;
  width: 44px;
  height: 10px;
  background: linear-gradient(
    135deg,
    hsl(var(--bar-hue) 90% 70%),
    hsl(var(--bar-hue) 80% 60%)
  );
  transform: skewX(-45deg);
  transform-origin: left bottom;
  border-radius: 6px 6px 2px 2px;
}

.iso-bar-body::after {
  content: '';
  position: absolute;
  top: -10px;
  right: -10px;
  width: 10px;
  height: calc(var(--bar-height) + 10px);
  background: linear-gradient(
    180deg,
    hsl(var(--bar-hue) 70% 28%),
    hsl(var(--bar-hue) 80% 40%)
  );
  transform: skewY(-45deg);
  transform-origin: top left;
  border-radius: 0 6px 6px 0;
}

.iso-bar-label {
  font-size: 0.78rem;
  letter-spacing: 0.03em;
  color: #6b7280;
  max-width: 130px;
  line-height: 1.2;
  min-height: 2.6em;
  max-height: 2.6em;
  overflow: hidden;
  word-break: break-word;
  text-wrap: balance;
}

.iso-bar-value {
  font-weight: 700;
  color: #111827;
  min-height: 1.1em;
}

.line-chart.is-volume .line-path {
  stroke: #0b2c6f;
  stroke-width: 3.2;
}

.line-chart.is-volume .line-area {
  opacity: 0.85;
}

.line-chart.is-volume .line-point {
  fill: #0b2c6f;
  stroke: #ffffff;
  stroke-width: 2;
}

.service-chart-summary {
  padding: 0.95rem 1.1rem;
  border-radius: 18px;
  background: #f8fafc;
  border: 1px solid #e5e7eb;
}

.summary-title {
  font-weight: 700;
  color: #0b2c6f;
}

.summary-subtitle {
  margin-top: 0.2rem;
  font-size: 0.9rem;
  color: #374151;
}

.summary-note {
  margin-top: 0.4rem;
  font-size: 0.85rem;
  color: #6b7280;
}

.line-chart.is-traffic .line-path {
  stroke: #0ea5e9;
  stroke-width: 3.2;
}

.line-chart.is-traffic .line-area {
  opacity: 0.75;
}

.line-chart.is-traffic .line-point {
  fill: #0ea5e9;
  stroke: #ffffff;
  stroke-width: 2;
}

.analytics-zone {
  margin-top: 2.5rem;
  padding: 2rem;
  border-radius: 28px;
  border: 1px solid #e5e7eb;
  background: linear-gradient(145deg, #ffffff 0%, #f8fafc 50%, #f7f2e8 100%);
  box-shadow: 0 20px 50px rgba(15, 23, 42, 0.12);
}

.analytics-header {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
}

.analytics-controls {
  display: grid;
  gap: 0.8rem;
  min-width: min(420px, 100%);
}

.analytics-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  justify-content: flex-end;
}

.analytics-export {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 0.55rem 0.9rem;
  background: #ffffff;
  color: #0f172a;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.analytics-export:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
}

.analytics-export.is-pdf {
  background: #0b2c6f;
  color: #ffffff;
  border-color: transparent;
}

.analytics-kicker {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.3em;
  color: #6b7280;
}

.analytics-title {
  margin-top: 0.4rem;
  font-size: 1.8rem;
  font-weight: 700;
  color: #0b2c6f;
}

.analytics-subtitle {
  margin-top: 0.5rem;
  font-size: 0.95rem;
  color: #4b5563;
  max-width: 520px;
}

.analytics-range {
  margin-top: 0.65rem;
  font-size: 0.9rem;
  color: #6b7280;
}

.analytics-filters {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 0.8rem;
  align-items: end;
}

.filter-field {
  display: grid;
  gap: 0.35rem;
}

.filter-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: #6b7280;
}

.filter-input {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 0.55rem 0.8rem;
  background: #ffffff;
  font-size: 0.9rem;
}

.filter-reset {
  border: none;
  border-radius: 12px;
  padding: 0.65rem 1rem;
  background: #0b2c6f;
  color: #ffffff;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.filter-reset:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.16);
}

.analytics-grid {
  margin-top: 2rem;
  display: grid;
  grid-template-columns: repeat(12, minmax(0, 1fr));
  gap: 1.2rem;
}

.analytics-card {
  grid-column: span 6;
  background: #ffffff;
  border-radius: 22px;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
  display: grid;
  gap: 1rem;
  --chart-height: 240px;
}

.analytics-card.span-7 {
  grid-column: span 7;
}

.analytics-card.span-5 {
  grid-column: span 5;
}

.analytics-card.span-12 {
  grid-column: span 12;
}

.analytics-card.span-6 {
  grid-column: span 6;
}

.timing-card {
  background: linear-gradient(135deg, rgba(11, 44, 111, 0.08), rgba(255, 255, 255, 0.95));
  border: 1px solid rgba(11, 44, 111, 0.15);
}

.timing-badge {
  align-self: flex-start;
  border-radius: 999px;
  padding: 0.3rem 0.9rem;
  background: rgba(11, 44, 111, 0.12);
  color: #0b2c6f;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.timing-definition {
  margin: 0.4rem 0 0;
  font-size: 0.9rem;
  color: #4b5563;
  max-width: 780px;
}

.timing-filter-note {
  margin: 0.4rem 0 0;
  font-size: 0.85rem;
  color: #64748b;
}

.timing-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
}

.timing-chart-grid {
  margin-top: 1.4rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1rem;
}

.timing-chart {
  border-radius: 18px;
  padding: 1.1rem 1.2rem;
  background: #ffffff;
  border: 1px solid rgba(148, 163, 184, 0.3);
  display: grid;
  gap: 0.8rem;
  min-height: 250px;
}

.timing-chart-header h5 {
  margin: 0;
  font-size: 1.05rem;
  color: #0b2c6f;
}

.timing-chart-header p {
  margin-top: 0.3rem;
  font-size: 0.85rem;
  color: #64748b;
}

.mini-line-chart {
  display: grid;
  gap: 0.5rem;
}

.mini-line-svg {
  width: 100%;
  height: 150px;
}

.timing-journey {
  background: linear-gradient(135deg, rgba(248, 250, 252, 0.98), rgba(226, 232, 240, 0.9));
}

.journey-body {
  display: grid;
  gap: 1rem;
}

.journey-bar {
  display: flex;
  height: 14px;
  border-radius: 999px;
  overflow: hidden;
  background: rgba(148, 163, 184, 0.2);
}

.journey-segment {
  height: 100%;
}

.journey-segment.is-kiosk {
  background: linear-gradient(90deg, #0b2c6f, #3b82f6);
}

.journey-segment.is-wait {
  background: linear-gradient(90deg, #f2c300, #f59e0b);
}

.journey-segment.is-serve {
  background: linear-gradient(90deg, #22c55e, #16a34a);
}

.journey-legend {
  display: grid;
  gap: 0.5rem;
}

.journey-item {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.85rem;
  color: #475569;
  font-weight: 600;
}

.journey-item strong {
  margin-left: auto;
  color: #0f172a;
}

.journey-swatch {
  width: 12px;
  height: 12px;
  border-radius: 999px;
}

.journey-swatch.is-kiosk {
  background: #1d4ed8;
}

.journey-swatch.is-wait {
  background: #f59e0b;
}

.journey-swatch.is-serve {
  background: #22c55e;
}

.timing-scope-note {
  margin-top: 1rem;
  font-size: 0.9rem;
  color: #6b7280;
}

.timing-tile {
  border-radius: 18px;
  padding: 1.2rem 1.3rem;
  background: #ffffff;
  border: 1px solid rgba(148, 163, 184, 0.35);
  display: grid;
  gap: 0.6rem;
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.6);
}

.timing-tile.timing-total {
  border-color: rgba(59, 130, 246, 0.35);
  background: linear-gradient(135deg, rgba(226, 232, 240, 0.7), rgba(255, 255, 255, 0.95));
}

.timing-tile.is-gold {
  border-color: rgba(242, 195, 0, 0.4);
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(255, 244, 219, 0.9));
}

.timing-tile.is-slate {
  border-color: rgba(15, 23, 42, 0.15);
  background: linear-gradient(135deg, rgba(248, 250, 252, 0.98), rgba(226, 232, 240, 0.7));
}

.timing-label {
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: #64748b;
  font-weight: 700;
}

.timing-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: #0b2c6f;
}

.timing-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  font-size: 0.85rem;
  color: #475569;
  font-weight: 600;
}

.timing-note {
  font-size: 0.9rem;
  color: #4b5563;
  margin: 0;
}

.timing-error {
  color: #b91c1c;
  font-size: 0.95rem;
}

.timing-loading {
  color: #64748b;
  font-size: 0.95rem;
}

.chart-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.chart-header h4 {
  margin: 0;
  font-size: 1.2rem;
  color: #0b2c6f;
}

.chart-header p {
  margin-top: 0.35rem;
  font-size: 0.9rem;
  color: #6b7280;
}

.analysis-note {
  font-size: 0.9rem;
  color: #4b5563;
  margin-top: 0.3rem;
}

.analysis-note::before {
  content: 'AI Insight: ';
  font-weight: 700;
  color: #0b2c6f;
}

.line-chart {
  display: grid;
  gap: 0.7rem;
  min-height: var(--chart-height);
}

.line-svg {
  width: 100%;
  height: 200px;
}

.line-grid line {
  stroke: rgba(148, 163, 184, 0.35);
  stroke-width: 1;
  stroke-dasharray: 6 8;
}

.line-path {
  fill: none;
  stroke: #0b2c6f;
  stroke-width: 3;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.line-path.is-gold {
  stroke: #f2c300;
}

.line-area {
  opacity: 1;
}

.line-area.is-gold {
  opacity: 0.85;
}

.line-point {
  fill: #0b2c6f;
  stroke: #ffffff;
  stroke-width: 2;
}

.line-point.is-gold {
  fill: #f2c300;
}

.line-axis {
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.16em;
}

.donut-chart,
.pie-chart {
  display: grid;
  gap: 1rem;
  min-height: var(--chart-height);
}

.donut-figure,
.pie-figure {
  position: relative;
  display: grid;
  place-items: center;
}

.donut-svg,
.pie-svg {
  width: 170px;
  height: 170px;
}

.donut-bg {
  fill: none;
  stroke: #f3f4f6;
  stroke-width: 16;
}

.donut-segment {
  fill: none;
  stroke-width: 16;
  stroke-linecap: round;
  transform: rotate(-90deg);
  transform-origin: 60px 60px;
}

.donut-center {
  position: absolute;
  text-align: center;
}

.donut-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0b2c6f;
}

.donut-label {
  display: block;
  font-size: 0.8rem;
  color: #6b7280;
}

.pie-segment {
  fill: none;
  stroke-width: 56;
  stroke-linecap: butt;
  transform: rotate(-90deg);
  transform-origin: 60px 60px;
}

.chart-legend {
  display: grid;
  gap: 0.45rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
  color: #4b5563;
}

.legend-item strong {
  margin-left: auto;
  color: #111827;
}

.legend-swatch {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

.kiosk-grid {
  margin-top: 1.2rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 0.9rem;
}

.kiosk-card {
  padding: 0.9rem 1rem;
  border-radius: 18px;
  border: 1px solid #e5e7eb;
  background: #f8fafc;
  display: grid;
  gap: 0.5rem;
}

.kiosk-card.is-online {
  border-color: rgba(46, 125, 50, 0.4);
  background: rgba(46, 125, 50, 0.08);
}

.kiosk-card-header {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.kiosk-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #c0392b;
}

.kiosk-card.is-online .kiosk-dot {
  background: #2e7d32;
}

.kiosk-name {
  font-weight: 700;
  color: #0b2c6f;
}

.kiosk-id {
  font-size: 0.8rem;
  color: #6b7280;
}

.kiosk-time {
  font-size: 0.85rem;
  color: #4b5563;
}

.insight-list {
  margin-top: 1.2rem;
  display: grid;
  gap: 0.8rem;
  padding: 0;
  list-style: none;
  color: #374151;
  font-size: 0.95rem;
}

.insight-list li {
  padding: 0.8rem 1rem;
  border-radius: 16px;
  background: #f8fafc;
  border: 1px solid #e5e7eb;
}


.empty-state {
  margin-top: 1.2rem;
  font-size: 0.9rem;
  color: #6b7280;
}

@media (max-width: 1200px) {
  .resident-layout {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 960px) {
  .admin-shell {
    padding: 2rem 1.25rem 3rem;
  }

  .admin-dock {
    flex-direction: column;
    align-items: flex-start;
  }

  .admin-dock::after {
    display: none;
  }

  .admin-dock-nav {
    width: 100%;
  }

  .admin-page {
    padding-top: 0.6rem;
    padding-bottom: 2.25rem;
  }

  .dashboard-hero {
    grid-template-columns: 1fr;
  }

  .dashboard-grid {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }

  .dashboard-panel,
  .dashboard-panel.span-7,
  .dashboard-panel.span-5,
  .dashboard-panel.span-8,
  .dashboard-panel.span-4,
  .dashboard-panel.span-12 {
    grid-column: span 1;
  }

  .analytics-grid {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }

  .analytics-card,
  .analytics-card.span-7,
  .analytics-card.span-5,
  .analytics-card.span-6,
  .analytics-card.span-12 {
    grid-column: span 1;
  }

  .resident-layout {
    grid-template-columns: 1fr;
  }

  .service-layout {
    grid-template-columns: 1fr;
  }

  .queue-command-grid,
  .queue-board-grid {
    grid-template-columns: 1fr;
  }

  .transaction-layout {
    grid-template-columns: 1fr;
  }

  .kiosk-layout {
    grid-template-columns: 1fr;
  }

  .audit-layout {
    grid-template-columns: 1fr;
  }

  .admin-users-layout {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .admin-dock {
    padding: 1rem;
  }

  .admin-page {
    padding-top: 0.5rem;
    padding-bottom: 2rem;
  }

  .admin-nav-item {
    padding: 0.45rem 0.7rem;
    font-size: 0.95rem;
  }

  .admin-time {
    width: 100%;
    text-align: left;
  }

  .dashboard-stat-grid {
    grid-template-columns: 1fr;
  }

  .hero-mini-grid {
    grid-template-columns: 1fr;
  }

  .analytics-filters {
    grid-template-columns: 1fr;
  }

  .analytics-actions {
    justify-content: flex-start;
  }

  .resident-metric-grid {
    grid-template-columns: 1fr;
  }

  .resident-filter-grid {
    grid-template-columns: 1fr;
  }

  .service-metric-grid {
    grid-template-columns: 1fr;
  }

  .service-action-grid {
    grid-template-columns: 1fr;
  }

  .service-table-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .queue-monitor-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .queue-monitor-frame {
    height: 220px;
  }

  .queue-serving-status {
    position: static;
    margin-top: 0.3rem;
  }

  .queue-serving-ticket {
    font-size: 3.4rem;
  }

  .queue-serving-resident {
    font-size: 1.5rem;
  }

  .queue-serving-service {
    font-size: 1.1rem;
  }

  .queue-serving-service-list {
    font-size: 0.85rem;
  }

  .queue-serving-grid {
    grid-template-columns: 1fr;
  }

  .queue-next-main {
    flex-direction: column;
    align-items: flex-start;
  }

  .queue-snapshot-grid {
    grid-template-columns: 1fr;
  }

  .queue-snapshot-footer {
    grid-template-columns: 1fr;
  }

  .queue-next-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.4rem;
  }

  .queue-serving-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .queue-call-cta {
    width: 100%;
  }

  .logout-modal-hero {
    flex-direction: column;
    text-align: center;
  }

  .logout-modal-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .queue-filter-grid {
    grid-template-columns: 1fr;
  }

  .queue-table-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .transaction-metric-grid {
    grid-template-columns: 1fr;
  }

  .transaction-filter-grid {
    grid-template-columns: 1fr;
  }

  .transaction-table-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .kiosk-metric-grid {
    grid-template-columns: 1fr;
  }

  .kiosk-action-grid {
    grid-template-columns: 1fr;
  }

  .kiosk-filter-row {
    grid-template-columns: 1fr;
  }

  .kiosk-table-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .audit-metric-grid {
    grid-template-columns: 1fr;
  }

  .audit-filter-grid {
    grid-template-columns: 1fr;
  }

  .audit-table-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .admin-users-metric-grid {
    grid-template-columns: 1fr;
  }

  .admin-users-form-grid,
  .admin-users-filter-grid {
    grid-template-columns: 1fr;
  }

  .admin-users-table-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .resident-table-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .modal-grid {
    grid-template-columns: 1fr;
  }

  .resident-table-wrapper {
    min-height: 240px;
  }

  .resident-pagination {
    align-items: flex-start;
  }

  .service-table-wrapper {
    min-height: 240px;
  }

  .queue-table-wrapper {
    min-height: 240px;
  }

  .transaction-table-wrapper {
    min-height: 240px;
  }

  .kiosk-table-wrapper {
    min-height: 240px;
  }

  .audit-table-wrapper {
    min-height: 240px;
  }

  .admin-users-table-wrapper {
    min-height: 240px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .admin-dock {
    animation: none;
  }
}
</style>
