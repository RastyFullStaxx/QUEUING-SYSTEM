import { request } from './api'

function authHeaders() {
  const token = localStorage.getItem('admin_token')
  return token ? { Authorization: `Bearer ${token}` } : {}
}

function getResidents({ status = '', search = '' } = {}) {
  const params = new URLSearchParams()
  if (status) params.set('status', status)
  if (search) params.set('search', search)
  const query = params.toString() ? `?${params.toString()}` : ''
  return request(`/api/admin/residents${query}`, { headers: authHeaders() })
}

function updateResidentStatus(id, status) {
  return request(`/api/admin/residents/${id}/status`, {
    method: 'POST',
    headers: authHeaders(),
    body: JSON.stringify({ status }),
  })
}

function createResident(payload) {
  return request('/api/admin/residents', {
    method: 'POST',
    headers: authHeaders(),
    body: JSON.stringify(payload),
  })
}

function updateResident(id, payload) {
  return request(`/api/admin/residents/${id}`, {
    method: 'POST',
    headers: authHeaders(),
    body: JSON.stringify(payload),
  })
}

function deleteResident(id) {
  return request(`/api/admin/residents/${id}/delete`, {
    method: 'POST',
    headers: authHeaders(),
  })
}

function getServices() {
  return request('/api/admin/services', { headers: authHeaders() })
}

function createService(payload) {
  return request('/api/admin/services', {
    method: 'POST',
    headers: authHeaders(),
    body: JSON.stringify(payload),
  })
}

function updateService(id, payload) {
  return request(`/api/admin/services/${id}`, {
    method: 'POST',
    headers: authHeaders(),
    body: JSON.stringify(payload),
  })
}

function getAdmins() {
  return request('/api/admin/users', { headers: authHeaders() })
}

function createAdmin(payload) {
  return request('/api/admin/users', {
    method: 'POST',
    headers: authHeaders(),
    body: JSON.stringify(payload),
  })
}

function updateAdmin(id, payload) {
  return request(`/api/admin/users/${id}`, {
    method: 'POST',
    headers: authHeaders(),
    body: JSON.stringify(payload),
  })
}

function getKiosks() {
  return request('/api/admin/kiosks', { headers: authHeaders() })
}

function createKiosk(payload) {
  return request('/api/admin/kiosks', {
    method: 'POST',
    headers: authHeaders(),
    body: JSON.stringify(payload),
  })
}

function updateKiosk(id, payload) {
  return request(`/api/admin/kiosks/${id}`, {
    method: 'POST',
    headers: authHeaders(),
    body: JSON.stringify(payload),
  })
}

function getQueue({ service_id = '', status = '' } = {}) {
  const params = new URLSearchParams()
  if (service_id) params.set('service_id', service_id)
  if (status) params.set('status', status)
  const query = params.toString() ? `?${params.toString()}` : ''
  return request(`/api/admin/queue${query}`, { headers: authHeaders() })
}

function queueNext(service_id) {
  const payload = service_id ? { service_id } : {}
  return request('/api/admin/queue/next', {
    method: 'POST',
    headers: authHeaders(),
    body: JSON.stringify(payload),
  })
}

function queueServe(id) {
  return request(`/api/admin/queue/${id}/serve`, {
    method: 'POST',
    headers: authHeaders(),
  })
}

function queueCall(id) {
  return request(`/api/admin/queue/${id}/call`, {
    method: 'POST',
    headers: authHeaders(),
  })
}

function queueCancel(id) {
  return request(`/api/admin/queue/${id}/cancel`, {
    method: 'POST',
    headers: authHeaders(),
  })
}

function getAuditLogs(limit = 100) {
  return request(`/api/admin/audit-logs?limit=${limit}`, { headers: authHeaders() })
}

export {
  getResidents,
  updateResidentStatus,
  createResident,
  updateResident,
  deleteResident,
  getServices,
  createService,
  updateService,
  getAdmins,
  createAdmin,
  updateAdmin,
  getKiosks,
  createKiosk,
  updateKiosk,
  getQueue,
  queueNext,
  queueCall,
  queueServe,
  queueCancel,
  getAuditLogs,
}
