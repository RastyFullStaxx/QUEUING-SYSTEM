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
        <a class="admin-nav-item" :class="{ 'is-active': activeSection === 'queue-control' }" href="#queue-control">
          <span class="admin-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M5 7h14M5 12h10M5 17h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
              <path d="M17 14v6l3-3-3-3Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
          Queue
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
      <button class="admin-logout" type="button" @click="logout">
        Sign out
      </button>
    </header>

    <div class="admin-content">
      <div class="w-full py-12">
        <div class="admin-intro">
          <div class="admin-intro-left">
            <div class="admin-emblem">
              <span class="admin-emblem-ring"></span>
              <span class="admin-emblem-dot"></span>
              <span class="admin-emblem-dot is-gold"></span>
            </div>
            <h1 class="admin-title">{{ activeSectionTitle }}</h1>
          </div>
          <div class="admin-intro-art" aria-hidden="true">
            <span class="admin-intro-bar is-primary"></span>
            <span class="admin-intro-bar is-gold"></span>
            <span class="admin-intro-bar is-neutral"></span>
          </div>
        </div>

        <section id="dashboard" class="admin-dashboard mt-10" v-show="activeSection === 'dashboard'">
          <div class="dashboard-hero">
            <div class="dashboard-hero-main">
              <p class="dashboard-kicker">Operations snapshot</p>
              <h2 class="dashboard-title">Admin Analytics Dashboard</h2>
              <p class="dashboard-subtitle">
                Live signals from registrations, queue flow, services, and kiosk activity to guide daily decisions.
              </p>
              <div class="dashboard-actions">
                <button class="dashboard-refresh" type="button" @click="refreshAll">Refresh data</button>
                <a class="dashboard-link" href="#queue-control">Jump to queue</a>
              </div>
              <p class="dashboard-updated">Updated {{ lastUpdatedLabel }}</p>
            </div>
            <div class="dashboard-hero-card">
              <div class="hero-stat">
                <p class="hero-stat-label">Active queue</p>
                <div class="hero-stat-value">
                  <span>{{ activeQueueCount }}</span>
                  <span class="hero-stat-unit">tickets</span>
                </div>
                <p class="hero-stat-note">{{ queuePressureNote }}</p>
              </div>
              <div class="hero-mini-grid">
                <div class="hero-mini">
                  <span>Waiting</span>
                  <strong>{{ queueStatusCounts.waiting }}</strong>
                </div>
                <div class="hero-mini">
                  <span>Serving</span>
                  <strong>{{ queueStatusCounts.serving }}</strong>
                </div>
                <div class="hero-mini">
                  <span>Avg wait</span>
                  <strong>{{ averageWaitLabel }}</strong>
                </div>
              </div>
              <div class="hero-meter">
                <div class="hero-meter-track">
                  <span class="hero-meter-fill" :style="{ width: `${queuePressurePercent}%` }"></span>
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
              <p class="dashboard-stat-label">Pending review</p>
              <p class="dashboard-stat-value">{{ residentStats.pending }}</p>
              <p class="dashboard-stat-meta">{{ approvalRateLabel }} approval rate</p>
            </div>
            <div class="dashboard-stat">
              <p class="dashboard-stat-label">Active services</p>
              <p class="dashboard-stat-value">{{ activeServiceCount }}</p>
              <p class="dashboard-stat-meta">{{ inactiveServiceCount }} inactive</p>
            </div>
            <div class="dashboard-stat">
              <p class="dashboard-stat-label">Kiosk health</p>
              <p class="dashboard-stat-value">{{ onlineKioskCount }}</p>
              <p class="dashboard-stat-meta">{{ offlineKioskCount }} offline</p>
            </div>
            <div class="dashboard-stat">
              <p class="dashboard-stat-label">Total tickets</p>
              <p class="dashboard-stat-value">{{ queueStatusCounts.total }}</p>
              <p class="dashboard-stat-meta">{{ queueStatusCounts.done }} completed</p>
            </div>
            <div class="dashboard-stat">
              <p class="dashboard-stat-label">Service focus</p>
              <p class="dashboard-stat-value">{{ topServiceLabel }}</p>
              <p class="dashboard-stat-meta">{{ topServiceCountLabel }}</p>
            </div>
          </div>

          <div class="dashboard-grid">
            <div class="dashboard-panel span-7">
              <div class="dashboard-panel-header">
                <div>
                  <h3>Queue flow</h3>
                  <p>Live composition of queue tickets with status distribution.</p>
                </div>
                <span class="panel-badge">Realtime</span>
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
                  <h3>Resident pipeline</h3>
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
                  <h3>Kiosk health</h3>
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

            <div class="dashboard-panel span-12">
              <div class="dashboard-panel-header">
                <div>
                  <h3>Recent activity</h3>
                  <p>Latest admin actions and system updates.</p>
                </div>
                <span class="panel-badge is-neutral">Audit</span>
              </div>
              <div v-if="recentLogs.length" class="activity-list">
                <div v-for="log in recentLogs" :key="log.id" class="activity-row">
                  <div class="activity-time">{{ formatTime(log.created_at) }}</div>
                  <div class="activity-meta">
                    <p class="activity-action">{{ formatAction(log.action) }}</p>
                    <p class="activity-actor">{{ log.actor_type }} #{{ log.actor_id }}</p>
                  </div>
                </div>
              </div>
              <p v-else class="empty-state">No recent activity yet.</p>
            </div>
          </div>

          <div class="analytics-zone">
            <div class="analytics-header">
              <div>
                <p class="analytics-kicker">Deep analytics</p>
                <h3 class="analytics-title">Operational Intelligence</h3>
                <p class="analytics-subtitle">
                  Visualize demand, approvals, and traffic patterns. Filters below apply to every chart.
                </p>
                <p class="analytics-range">Showing: {{ analyticsRangeLabel }}</p>
              </div>
              <div class="analytics-filters">
                <div class="filter-field">
                  <label class="filter-label">Range</label>
                  <select v-model="analyticsRange" class="filter-input">
                    <option value="today">Today</option>
                    <option value="7d">Last 7 days</option>
                    <option value="30d">Last 30 days</option>
                    <option value="90d">Last 90 days</option>
                    <option value="all">All time</option>
                    <option value="custom">Custom</option>
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
                    <option value="">All services</option>
                    <option v-for="service in services" :key="service.id" :value="service.id">
                      {{ service.name }}
                    </option>
                  </select>
                </div>
                <div class="filter-field">
                  <label class="filter-label">Status</label>
                  <select v-model="analyticsStatus" class="filter-input">
                    <option value="all">All statuses</option>
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

            <div class="analytics-grid">
              <div class="analytics-card span-7">
                <div class="chart-header">
                  <div>
                    <h4>Daily ticket volume</h4>
                    <p>Line trend of tickets created per day.</p>
                  </div>
                </div>
                <div v-if="ticketTrendSeries.length" class="line-chart">
                  <svg class="line-svg" viewBox="0 0 640 220" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                      <linearGradient id="ticketTrendFill" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#0b2c6f" stop-opacity="0.25" />
                        <stop offset="100%" stop-color="#0b2c6f" stop-opacity="0" />
                      </linearGradient>
                    </defs>
                    <g class="line-grid">
                      <line v-for="grid in lineGrid" :key="`ticket-grid-${grid}`" :x1="24" :x2="616" :y1="grid" :y2="grid" />
                    </g>
                    <path class="line-area" :d="ticketTrend.areaPath" fill="url(#ticketTrendFill)" />
                    <path class="line-path" :d="ticketTrend.linePath" />
                    <circle
                      v-for="point in ticketTrend.points"
                      :key="`ticket-point-${point.index}`"
                      class="line-point"
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
                    <h4>Ticket status mix</h4>
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
                    <h4>Most booked service</h4>
                    <p>Bar chart of service volume for the selected range.</p>
                  </div>
                </div>
                <div v-if="serviceVolumeBars.length" class="service-chart">
                  <div class="service-chart-bars">
                    <div v-for="service in serviceVolumeBars" :key="service.id" class="service-bar">
                      <div class="service-bar-track">
                        <span class="service-bar-fill" :style="{ height: `${service.percent}%` }"></span>
                      </div>
                      <span class="service-bar-label">{{ service.shortLabel }}</span>
                      <span class="service-bar-value">{{ service.count }}</span>
                    </div>
                  </div>
                  <div class="service-chart-summary">
                    <p class="summary-title">{{ analyticsTopServiceLabel }}</p>
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
                    <h4>Service mix</h4>
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
                    <h4>Traffic by time</h4>
                    <p>Ticket volume grouped by 3-hour windows.</p>
                  </div>
                </div>
                <div v-if="trafficBuckets.length" class="traffic-chart">
                  <div class="traffic-bars">
                    <div v-for="slot in trafficBuckets" :key="slot.label" class="traffic-bar">
                      <div class="traffic-bar-track">
                        <span class="traffic-bar-fill" :style="{ height: `${slot.percent}%` }"></span>
                      </div>
                      <span class="traffic-bar-label">{{ slot.label }}</span>
                      <span class="traffic-bar-value">{{ slot.count }}</span>
                    </div>
                  </div>
                </div>
                <p v-else class="empty-state">No traffic data yet. Ticket activity will build this view.</p>
                <p class="analysis-note">{{ trafficInsight }}</p>
              </div>

              <div class="analytics-card span-6">
                <div class="chart-header">
                  <div>
                    <h4>Resident registrations</h4>
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

        <div id="resident-verification" class="admin-card mt-10" v-show="activeSection === 'resident-verification'">
      <div class="tool-header">
        <div class="tool-title">
          <span class="tool-icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M4 12l8-7 8 7v8a2 2 0 0 1-2 2h-4v-6H10v6H6a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
          <h2 class="tool-heading">Resident Verification</h2>
        </div>
        <div class="tool-accent" aria-hidden="true">
          <span class="tool-accent-bar is-primary"></span>
          <span class="tool-accent-bar is-gold"></span>
          <span class="tool-accent-bar is-neutral"></span>
        </div>
      </div>
      <div class="control-strip mt-4 flex flex-wrap gap-3">
        <select v-model="statusFilter" class="border border-[#E5E7EB] rounded-full px-4 py-2 text-base">
          <option value="">All statuses</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
        </select>
        <input
          v-model="search"
          type="text"
          placeholder="Search name or email"
          class="border border-[#E5E7EB] rounded-full px-4 py-2 text-base w-64"
        />
        <button class="bg-[#0B2C6F] text-white px-4 py-2 rounded-full text-base" @click="loadResidents">
          Refresh
        </button>
      </div>
      <div class="mt-6 overflow-x-auto">
        <table class="w-full text-base">
          <thead class="text-left text-[#6B7280]">
            <tr class="border-b border-[#E5E7EB]">
              <th class="py-2">Name</th>
              <th class="py-2">Email</th>
              <th class="py-2">Status</th>
              <th class="py-2">Registered</th>
              <th class="py-2 text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoadingResidents">
              <td colspan="5" class="py-6">
                <div class="table-state">
                  <span class="table-state-icon"></span>
                  <span>Loading residents...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="residents.length === 0">
              <td colspan="5" class="py-6">
                <div class="table-state">
                  <span class="table-state-icon"></span>
                  <span>No residents found.</span>
                </div>
              </td>
            </tr>
            <tr v-for="resident in residents" :key="resident.id" class="border-b border-[#F3F4F6]" :class="rowClass(resident.status)">
              <td class="py-3">{{ resident.first_name }} {{ resident.last_name }}</td>
              <td class="py-3">{{ resident.email }}</td>
              <td class="py-3">
                <span class="status-pill" :class="statusClass(resident.status)">
                  {{ resident.status }}
                </span>
              </td>
              <td class="py-3">{{ formatDate(resident.created_at) }}</td>
              <td class="py-3 text-right">
                <div class="inline-flex gap-2">
                  <button
                    class="px-3 py-1 rounded-full text-base border border-[#2E7D32] text-[#2E7D32]"
                    @click="updateStatus(resident.id, 'approved')"
                  >
                    Approve
                  </button>
                  <button
                    class="px-3 py-1 rounded-full text-base border border-[#C0392B] text-[#C0392B]"
                    @click="updateStatus(resident.id, 'rejected')"
                  >
                    Reject
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-if="residentError" class="mt-4 text-base text-[#C0392B]">{{ residentError }}</p>
      </div>
    </div>

          <section id="services" class="admin-card mt-10" v-show="activeSection === 'services'">
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
        <div class="control-strip mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
          <input v-model="newService.name" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base" placeholder="Service name" />
          <input v-model="newService.code" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base" placeholder="Code" />
          <button class="bg-[#F2C300] text-black rounded-xl text-base font-semibold" @click="createService">
            Add service
          </button>
          <button class="bg-[#0B2C6F] text-white rounded-xl text-base" @click="loadServices">
            Refresh
          </button>
        </div>
        <div class="mt-4 overflow-x-auto">
          <table class="w-full text-base">
            <thead class="text-left text-[#6B7280]">
              <tr class="border-b border-[#E5E7EB]">
                <th class="py-2">Name</th>
                <th class="py-2">Code</th>
                <th class="py-2">Active</th>
                <th class="py-2 text-right">Action</th>
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
              <tr v-else-if="services.length === 0">
                <td colspan="4" class="py-4">
                  <div class="table-state">
                    <span class="table-state-icon"></span>
                    <span>No services found.</span>
                  </div>
                </td>
              </tr>
              <tr v-for="service in services" :key="service.id" class="border-b border-[#F3F4F6]" :class="rowClass(service.active ? 'active' : 'inactive')">
                <td class="py-2">{{ service.name }}</td>
                <td class="py-2">{{ service.code }}</td>
                <td class="py-2">
                  <span class="status-pill" :class="statusClass(service.active ? 'active' : 'inactive')">
                    {{ service.active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="py-2 text-right">
                  <button class="text-base border border-[#0B2C6F] text-[#0B2C6F] px-3 py-1 rounded-full" @click="toggleService(service)">
                    Toggle
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <p v-if="serviceError" class="mt-3 text-base text-[#C0392B]">{{ serviceError }}</p>
        </div>
      </section>

          <section id="queue-control" class="admin-card mt-10" v-show="activeSection === 'queue-control'">
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
        <div class="control-strip mt-4 flex flex-wrap gap-3">
          <input v-model="queueServiceId" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base w-32" placeholder="Service ID" />
          <select v-model="queueStatus" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base">
            <option value="">All</option>
            <option value="waiting">Waiting</option>
            <option value="serving">Serving</option>
            <option value="done">Done</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <button class="bg-[#F2C300] text-black rounded-xl px-4 py-2 text-base font-semibold" @click="callNext">
            Call next
          </button>
          <button class="bg-[#0B2C6F] text-white rounded-xl px-4 py-2 text-base" @click="loadQueue">
            Refresh
          </button>
        </div>
        <div class="mt-4 overflow-x-auto">
          <table class="w-full text-base">
            <thead class="text-left text-[#6B7280]">
              <tr class="border-b border-[#E5E7EB]">
                <th class="py-2">Ticket</th>
                <th class="py-2">Service</th>
                <th class="py-2">Status</th>
                <th class="py-2 text-right">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoadingQueue">
                <td colspan="4" class="py-4">
                  <div class="table-state">
                    <span class="table-state-icon"></span>
                    <span>Loading tickets...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="queueTickets.length === 0">
                <td colspan="4" class="py-4">
                  <div class="table-state">
                    <span class="table-state-icon"></span>
                    <span>No tickets found.</span>
                  </div>
                </td>
              </tr>
              <tr v-for="ticket in queueTickets" :key="ticket.id" class="border-b border-[#F3F4F6]" :class="rowClass(ticket.status)">
                <td class="py-2">{{ ticket.ticket_no }}</td>
                <td class="py-2">{{ ticket.service_id }}</td>
                <td class="py-2">
                  <span class="status-pill" :class="statusClass(ticket.status)">
                    {{ ticket.status }}
                  </span>
                </td>
                <td class="py-2 text-right">
                  <button class="text-base border border-[#2E7D32] text-[#2E7D32] px-3 py-1 rounded-full" @click="serveTicket(ticket)">
                    Serve
                  </button>
                  <button class="ml-2 text-base border border-[#C0392B] text-[#C0392B] px-3 py-1 rounded-full" @click="cancelTicket(ticket)">
                    Cancel
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <p v-if="queueError" class="mt-3 text-base text-[#C0392B]">{{ queueError }}</p>
        </div>
      </section>
        <section id="transactions" class="admin-card mt-10" v-show="activeSection === 'transactions'">
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
          <div class="control-strip mt-4 flex flex-wrap gap-3">
            <input v-model="transactionServiceId" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base w-32" placeholder="Service ID" />
            <select v-model="transactionStatus" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base">
              <option value="done">Done</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <button class="bg-[#0B2C6F] text-white rounded-xl px-4 py-2 text-base" @click="loadTransactions">
              Refresh
            </button>
          </div>
          <div class="mt-4 overflow-x-auto">
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
                <tr v-else-if="transactions.length === 0">
                  <td colspan="4" class="py-4">
                    <div class="table-state">
                      <span class="table-state-icon"></span>
                      <span>No transactions found.</span>
                    </div>
                  </td>
                </tr>
                <tr v-for="ticket in transactions" :key="ticket.id" class="border-b border-[#F3F4F6]" :class="rowClass(ticket.status)">
                  <td class="py-2">{{ ticket.ticket_no }}</td>
                  <td class="py-2">{{ ticket.service_id }}</td>
                  <td class="py-2">
                    <span class="status-pill" :class="statusClass(ticket.status)">
                      {{ ticket.status }}
                    </span>
                  </td>
                  <td class="py-2">{{ formatDate(ticket.issued_at) }}</td>
                </tr>
              </tbody>
            </table>
            <p v-if="transactionError" class="mt-3 text-base text-[#C0392B]">{{ transactionError }}</p>
          </div>
        </section>

          <section id="kiosk-devices" class="admin-card mt-10" v-show="activeSection === 'kiosk-devices'">
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
        <div class="control-strip mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
          <input v-model="newKiosk.device_id" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base" placeholder="Device ID" />
          <input v-model="newKiosk.name" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base" placeholder="Name" />
          <button class="bg-[#F2C300] text-black rounded-xl text-base font-semibold" @click="createKiosk">
            Add kiosk
          </button>
          <button class="bg-[#0B2C6F] text-white rounded-xl text-base" @click="loadKiosks">
            Refresh
          </button>
        </div>
        <div class="mt-4 overflow-x-auto">
          <table class="w-full text-base">
            <thead class="text-left text-[#6B7280]">
              <tr class="border-b border-[#E5E7EB]">
                <th class="py-2">Device ID</th>
                <th class="py-2">Name</th>
                <th class="py-2">Token</th>
                <th class="py-2">Last seen</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoadingKiosks">
                <td colspan="4" class="py-4">
                  <div class="table-state">
                    <span class="table-state-icon"></span>
                    <span>Loading kiosks...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="kiosks.length === 0">
                <td colspan="4" class="py-4">
                  <div class="table-state">
                    <span class="table-state-icon"></span>
                    <span>No kiosks found.</span>
                  </div>
                </td>
              </tr>
              <tr v-for="kiosk in kiosks" :key="kiosk.id" class="border-b border-[#F3F4F6]" :class="rowClass('neutral')">
                <td class="py-2">{{ kiosk.device_id }}</td>
                <td class="py-2">{{ kiosk.name }}</td>
                <td class="py-2 text-base">{{ kiosk.token }}</td>
                <td class="py-2">{{ kiosk.last_seen_at || '-' }}</td>
              </tr>
            </tbody>
          </table>
          <p v-if="kioskError" class="mt-3 text-base text-[#C0392B]">{{ kioskError }}</p>
        </div>
      </section>

          <section id="audit-logs" class="admin-card mt-10" v-show="activeSection === 'audit-logs'">
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
        <div class="control-strip mt-4 flex justify-end">
          <button class="bg-[#0B2C6F] text-white rounded-full px-4 py-2 text-base" @click="loadAuditLogs">
            Refresh
          </button>
        </div>
        <div class="mt-4 overflow-x-auto">
          <table class="w-full text-base">
            <thead class="text-left text-[#6B7280]">
              <tr class="border-b border-[#E5E7EB]">
                <th class="py-2">When</th>
                <th class="py-2">Action</th>
                <th class="py-2">Actor</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoadingLogs">
                <td colspan="3" class="py-4">
                  <div class="table-state">
                    <span class="table-state-icon"></span>
                    <span>Loading logs...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="auditLogs.length === 0">
                <td colspan="3" class="py-4">
                  <div class="table-state">
                    <span class="table-state-icon"></span>
                    <span>No logs found.</span>
                  </div>
                </td>
              </tr>
              <tr v-for="log in auditLogs" :key="log.id" class="border-b border-[#F3F4F6]" :class="rowClass('neutral')">
                <td class="py-2">{{ formatDate(log.created_at) }}</td>
                <td class="py-2">{{ log.action }}</td>
                <td class="py-2">{{ log.actor_type }} #{{ log.actor_id }}</td>
              </tr>
            </tbody>
          </table>
          <p v-if="auditError" class="mt-3 text-base text-[#C0392B]">{{ auditError }}</p>
        </div>
      </section>
        <div id="admin-users" class="admin-card mt-10" v-if="isSuperAdmin && activeSection === 'admin-users'">
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
      <div class="control-strip mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <input v-model="newAdmin.name" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base" placeholder="Name" />
        <input v-model="newAdmin.email" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base" placeholder="Email" />
        <input v-model="newAdmin.password" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base" placeholder="Password" />
        <select v-model="newAdmin.role" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base">
          <option value="staff_admin">staff_admin</option>
          <option value="super_admin">super_admin</option>
        </select>
        <input v-model="newAdmin.service_ids" class="border border-[#E5E7EB] rounded-xl px-3 py-2 text-base sm:col-span-2 lg:col-span-2" placeholder="Service IDs (comma)" />
        <button class="bg-[#F2C300] text-black rounded-xl text-base font-semibold" @click="createAdmin">
          Add admin
        </button>
        <button class="bg-[#0B2C6F] text-white rounded-xl text-base" @click="loadAdmins">
          Refresh
        </button>
      </div>
      <div class="mt-4 overflow-x-auto">
        <table class="w-full text-base">
          <thead class="text-left text-[#6B7280]">
            <tr class="border-b border-[#E5E7EB]">
              <th class="py-2">Name</th>
              <th class="py-2">Email</th>
              <th class="py-2">Role</th>
              <th class="py-2">Services</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoadingAdmins">
              <td colspan="4" class="py-4">
                <div class="table-state">
                  <span class="table-state-icon"></span>
                  <span>Loading admins...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="admins.length === 0">
              <td colspan="4" class="py-4">
                <div class="table-state">
                  <span class="table-state-icon"></span>
                  <span>No admins found.</span>
                </div>
              </td>
            </tr>
            <tr v-for="admin in admins" :key="admin.id" class="border-b border-[#F3F4F6]" :class="rowClass('neutral')">
              <td class="py-2">{{ admin.name }}</td>
              <td class="py-2">{{ admin.email }}</td>
              <td class="py-2">{{ admin.role }}</td>
              <td class="py-2 text-base">{{ admin.service_ids.join(', ') }}</td>
            </tr>
          </tbody>
        </table>
        <p v-if="adminError" class="mt-3 text-base text-[#C0392B]">{{ adminError }}</p>
      </div>
    </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
  getResidents,
  updateResidentStatus,
  getServices,
  createService as apiCreateService,
  updateService as apiUpdateService,
  getAdmins,
  createAdmin as apiCreateAdmin,
  getKiosks,
  createKiosk as apiCreateKiosk,
  getQueue,
  queueNext,
  queueServe,
  queueCancel,
  getAuditLogs,
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
const activeSection = ref('dashboard')
const sectionTitles = {
  dashboard: 'Admin Dashboard',
  'resident-verification': 'Resident Verification',
  services: 'Services',
  'queue-control': 'Queue Control',
  transactions: 'Transactions',
  'kiosk-devices': 'Kiosk Devices',
  'audit-logs': 'Audit Logs',
  'admin-users': 'Admin Users',
}
const activeSectionTitle = computed(() => {
  if (activeSection.value === 'admin-users' && !isSuperAdmin.value) {
    return sectionTitles['resident-verification']
  }
  return sectionTitles[activeSection.value] || sectionTitles['resident-verification']
})
let dockObserver = null
let hashListener = null
const residents = ref([])
const isLoadingResidents = ref(false)
const residentError = ref('')
const statusFilter = ref('')
const search = ref('')
const services = ref([])
const isLoadingServices = ref(false)
const serviceError = ref('')
const newService = ref({ name: '', code: '' })
const kiosks = ref([])
const isLoadingKiosks = ref(false)
const kioskError = ref('')
const newKiosk = ref({ device_id: '', name: '' })
const queueTickets = ref([])
const isLoadingQueue = ref(false)
const queueError = ref('')
const queueServiceId = ref('')
const queueStatus = ref('')
const transactions = ref([])
const isLoadingTransactions = ref(false)
const transactionError = ref('')
const transactionServiceId = ref('')
const transactionStatus = ref('done')
const auditLogs = ref([])
const isLoadingLogs = ref(false)
const auditError = ref('')
const admins = ref([])
const isLoadingAdmins = ref(false)
const adminError = ref('')
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
const lastUpdatedAt = ref(new Date())

const lastUpdatedLabel = computed(() => {
  if (!lastUpdatedAt.value) return 'just now'
  return lastUpdatedAt.value.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })
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

const analyticsRangeLabel = computed(() => {
  if (analyticsRange.value === 'all') return 'All time'
  if (analyticsRange.value === 'today') return 'Today'
  if (analyticsRange.value === '7d') return 'Last 7 days'
  if (analyticsRange.value === '30d') return 'Last 30 days'
  if (analyticsRange.value === '90d') return 'Last 90 days'
  if (analyticsRange.value === 'custom') {
    if (!analyticsStartDate.value && !analyticsEndDate.value) return 'Custom range'
    const startLabel = analyticsStartDate.value || 'Start'
    const endLabel = analyticsEndDate.value || 'Today'
    return `${startLabel} to ${endLabel}`
  }
  return 'Custom range'
})

const analyticsFilterNote = computed(() => {
  const statusLabel =
    analyticsStatus.value === 'all'
      ? 'All statuses'
      : analyticsStatus.value.charAt(0).toUpperCase() + analyticsStatus.value.slice(1)
  const serviceLabel = analyticsServiceId.value
    ? services.value.find((service) => service.id === parseInt(analyticsServiceId.value, 10))?.name ||
      `Service ${analyticsServiceId.value}`
    : 'All services'
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

const shortServiceLabel = (name = '') => {
  const words = name.split(' ').filter(Boolean)
  if (!words.length) return 'SVC'
  if (words.length === 1) return words[0].slice(0, 4).toUpperCase()
  return words
    .slice(0, 2)
    .map((word) => word[0].toUpperCase())
    .join('')
}

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
    shortLabel: shortServiceLabel(item.name),
  }))
})

const analyticsTopService = computed(() => serviceVolumeBars.value[0] || null)

const analyticsTopServiceLabel = computed(() => {
  if (!analyticsTopService.value) return 'No demand yet'
  return analyticsTopService.value.name
})

const analyticsTopServiceCountLabel = computed(() => {
  if (!analyticsTopService.value) return 'Waiting for tickets'
  return `${analyticsTopService.value.count} tickets`
})

const topServiceLabel = computed(() => {
  const top = serviceDemand.value[0]
  return top ? top.name : 'No demand yet'
})

const topServiceCountLabel = computed(() => {
  const top = serviceDemand.value[0]
  return top ? `${top.count} tickets` : 'Waiting for tickets'
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
    color: item.label === 'Other' ? '#9ca3af' : palette[index % palette.length],
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

const recentLogs = computed(() => auditLogs.value.slice(0, 5))

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

const logout = () => {
  localStorage.removeItem('admin_token')
  localStorage.removeItem('admin_profile')
  router.push('/login')
}

const loadResidents = async () => {
  residentError.value = ''
  isLoadingResidents.value = true
  try {
    const data = await getResidents({ status: statusFilter.value, search: search.value })
    residents.value = data.residents || []
  } catch (err) {
    residentError.value = err.message
  } finally {
    isLoadingResidents.value = false
  }
}

const loadAllResidents = async () => {
  try {
    const data = await getResidents()
    allResidents.value = data.residents || []
  } catch (err) {
    // Ignore analytics fetch errors for now.
  }
}

const updateStatus = async (id, status) => {
  residentError.value = ''
  try {
    await updateResidentStatus(id, status)
    await loadResidents()
  } catch (err) {
    residentError.value = err.message
  }
}

const loadServices = async () => {
  serviceError.value = ''
  isLoadingServices.value = true
  try {
    const data = await getServices()
    services.value = data.services || []
  } catch (err) {
    serviceError.value = err.message
  } finally {
    isLoadingServices.value = false
  }
}

const createService = async () => {
  serviceError.value = ''
  try {
    const payload = { name: newService.value.name, code: newService.value.code, active: 1 }
    await apiCreateService(payload)
    newService.value = { name: '', code: '' }
    await loadServices()
  } catch (err) {
    serviceError.value = err.message
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

const loadKiosks = async () => {
  kioskError.value = ''
  isLoadingKiosks.value = true
  try {
    const data = await getKiosks()
    kiosks.value = data.kiosks || []
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

const loadQueue = async () => {
  queueError.value = ''
  isLoadingQueue.value = true
  try {
    const data = await getQueue({ service_id: queueServiceId.value, status: queueStatus.value })
    queueTickets.value = data.tickets || []
  } catch (err) {
    queueError.value = err.message
  } finally {
    isLoadingQueue.value = false
  }
}

const loadAllQueueTickets = async () => {
  try {
    const data = await getQueue()
    allQueueTickets.value = data.tickets || []
  } catch (err) {
    // Ignore analytics fetch errors for now.
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
  } catch (err) {
    transactionError.value = err.message
  } finally {
    isLoadingTransactions.value = false
  }
}

const callNext = async () => {
  queueError.value = ''
  try {
    if (!queueServiceId.value) {
      queueError.value = 'Service ID is required'
      return
    }
    await queueNext(parseInt(queueServiceId.value, 10))
    await loadQueue()
  } catch (err) {
    queueError.value = err.message
  }
}

const serveTicket = async (ticket) => {
  queueError.value = ''
  try {
    await queueServe(ticket.id)
    await loadQueue()
  } catch (err) {
    queueError.value = err.message
  }
}

const cancelTicket = async (ticket) => {
  queueError.value = ''
  try {
    await queueCancel(ticket.id)
    await loadQueue()
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
  } catch (err) {
    auditError.value = err.message
  } finally {
    isLoadingLogs.value = false
  }
}

const loadAdmins = async () => {
  adminError.value = ''
  isLoadingAdmins.value = true
  try {
    const data = await getAdmins()
    admins.value = data.admins || []
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

const formatDate = (value) => {
  if (!value) return '-'
  const date = new Date(value)
  return date.toLocaleDateString()
}

const formatTime = (value) => {
  if (!value) return '--'
  const date = new Date(value)
  return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })
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

const formatHourLabel = (hour) => {
  const normalized = ((hour % 24) + 24) % 24
  const period = normalized >= 12 ? 'p' : 'a'
  const display = normalized % 12 || 12
  return `${display}${period}`
}

const formatTimeRange = (startHour) => {
  const startLabel = formatHourLabel(startHour)
  const endLabel = formatHourLabel(startHour + 3)
  return `${startLabel}-${endLabel}`
}

onMounted(() => {
  const sectionIds = ['dashboard', 'resident-verification', 'services', 'queue-control', 'transactions', 'kiosk-devices', 'audit-logs', 'admin-users']
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

onBeforeUnmount(() => {
  if (dockObserver) {
    dockObserver.disconnect()
  }
  if (hashListener) {
    window.removeEventListener('hashchange', hashListener)
  }
})
</script>

<style scoped>
.admin-shell {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
  padding: 2.5rem 2rem 3.5rem;
  background: #f3f4f6;
}

.admin-dock {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 1rem 1.5rem;
  border-radius: 28px;
  background: #0b2c6f;
  border: 1px solid rgba(255, 255, 255, 0.12);
  position: sticky;
  top: 1.5rem;
  z-index: 10;
}

.admin-dock-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
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
}

.admin-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #f8f7ff;
}

.admin-email {
  margin-top: 0.15rem;
  font-size: 1rem;
  color: rgba(226, 232, 240, 0.8);
}

.admin-dock-divider {
  height: 38px;
  width: 1px;
  background: rgba(255, 255, 255, 0.18);
}

.admin-dock-nav {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex: 1;
  flex-wrap: wrap;
}

.admin-nav-item {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  padding: 0.65rem 1rem;
  border-radius: 999px;
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.86);
  text-decoration: none;
  transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
}

.admin-nav-item:hover {
  transform: translateX(2px);
  background: rgba(242, 195, 0, 0.2);
  color: #ffffff;
}

.admin-nav-item.is-active {
  background: #f2c300;
  color: #0b2c6f;
}

.admin-nav-icon {
  height: 28px;
  width: 28px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.16);
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
  transition: transform 0.2s ease, background 0.2s ease;
}

.admin-logout:hover {
  transform: translateY(-1px);
  background: #f7cf1a;
}

.admin-content {
  flex: 1;
  min-width: 0;
  font-size: 1rem;
}

.admin-intro {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  padding: 1.75rem 2rem;
  border-radius: 24px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  position: relative;
  overflow: hidden;
}

.admin-intro::before {
  content: '';
  position: absolute;
  right: -70px;
  top: -90px;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  border: 1px solid rgba(11, 44, 111, 0.18);
  pointer-events: none;
}

.admin-intro-left {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  position: relative;
  z-index: 1;
}

.admin-emblem {
  position: relative;
  height: 64px;
  width: 64px;
  border-radius: 20px;
  background: #0b2c6f;
  display: grid;
  place-items: center;
}

.admin-emblem-ring {
  position: absolute;
  height: 40px;
  width: 40px;
  border-radius: 50%;
  border: 2px solid #f2c300;
}

.admin-emblem-dot {
  height: 10px;
  width: 10px;
  border-radius: 50%;
  background: #ffffff;
}

.admin-emblem-dot.is-gold {
  position: absolute;
  height: 6px;
  width: 6px;
  background: #f2c300;
  top: 12px;
  right: 12px;
}

.admin-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0b2c6f;
  letter-spacing: 0.02em;
}

.admin-intro-art {
  display: flex;
  gap: 0.6rem;
  align-items: center;
  position: relative;
  z-index: 1;
}

.admin-intro-bar {
  width: 56px;
  height: 10px;
  border-radius: 999px;
  background: #e5e7eb;
}

.admin-intro-bar.is-primary {
  width: 80px;
  background: #0b2c6f;
}

.admin-intro-bar.is-gold {
  background: #f2c300;
}

.admin-intro-bar.is-neutral {
  width: 40px;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
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
}

.service-chart-bars {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
  gap: 0.9rem;
  align-items: end;
  min-height: 180px;
}

.service-bar {
  display: grid;
  gap: 0.5rem;
  justify-items: center;
}

.service-bar-track {
  width: 42px;
  height: 120px;
  border-radius: 999px;
  background: #f3f4f6;
  display: flex;
  align-items: flex-end;
  padding: 6px;
}

.service-bar-fill {
  width: 100%;
  border-radius: 999px;
  background: linear-gradient(180deg, #0b2c6f, #f2c300);
  transition: height 0.4s ease;
}

.service-bar-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: #6b7280;
}

.service-bar-value {
  font-weight: 700;
  color: #111827;
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

.traffic-chart {
  margin-top: 1.2rem;
  display: grid;
  gap: 1rem;
}

.traffic-bars {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(50px, 1fr));
  gap: 0.75rem;
  align-items: end;
  min-height: 160px;
}

.traffic-bar {
  display: grid;
  gap: 0.4rem;
  justify-items: center;
}

.traffic-bar-track {
  width: 28px;
  height: 110px;
  border-radius: 999px;
  background: #f3f4f6;
  display: flex;
  align-items: flex-end;
  padding: 5px;
}

.traffic-bar-fill {
  width: 100%;
  border-radius: 999px;
  background: linear-gradient(180deg, #f2c300, rgba(11, 44, 111, 0.85));
  transition: height 0.4s ease;
}

.traffic-bar-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: #6b7280;
}

.traffic-bar-value {
  font-weight: 600;
  color: #111827;
}

.traffic-note {
  font-size: 0.9rem;
  color: #6b7280;
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
}

.analytics-card.span-7 {
  grid-column: span 7;
}

.analytics-card.span-5 {
  grid-column: span 5;
}

.analytics-card.span-6 {
  grid-column: span 6;
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
  content: 'AI insight: ';
  font-weight: 700;
  color: #0b2c6f;
}

.line-chart {
  display: grid;
  gap: 0.7rem;
}

.line-svg {
  width: 100%;
  height: auto;
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
}

.donut-figure,
.pie-figure {
  position: relative;
  display: grid;
  place-items: center;
}

.donut-svg,
.pie-svg {
  width: 160px;
  height: 160px;
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

.activity-list {
  margin-top: 1.2rem;
  display: grid;
  gap: 0.85rem;
}

.activity-row {
  display: grid;
  grid-template-columns: 90px minmax(0, 1fr);
  gap: 1rem;
  padding: 0.8rem 1rem;
  border-radius: 16px;
  background: #f8fafc;
  border: 1px solid #e5e7eb;
}

.activity-time {
  font-weight: 700;
  color: #0b2c6f;
}

.activity-action {
  font-weight: 600;
  color: #111827;
}

.activity-actor {
  margin-top: 0.2rem;
  font-size: 0.85rem;
  color: #6b7280;
}

.empty-state {
  margin-top: 1.2rem;
  font-size: 0.9rem;
  color: #6b7280;
}

@media (max-width: 960px) {
  .admin-shell {
    padding: 2rem 1.25rem 3rem;
  }

  .admin-dock {
    flex-direction: column;
    align-items: flex-start;
  }

  .admin-dock-nav {
    width: 100%;
  }

  .admin-intro {
    align-items: flex-start;
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
  .analytics-card.span-6 {
    grid-column: span 1;
  }
}

@media (max-width: 640px) {
  .admin-dock {
    padding: 1rem;
  }

  .admin-nav-item {
    padding: 0.45rem 0.7rem;
    font-size: 0.95rem;
  }

  .dashboard-stat-grid {
    grid-template-columns: 1fr;
  }

  .hero-mini-grid {
    grid-template-columns: 1fr;
  }

  .activity-row {
    grid-template-columns: 1fr;
  }

  .analytics-filters {
    grid-template-columns: 1fr;
  }
}
</style>
