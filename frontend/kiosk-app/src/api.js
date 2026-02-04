const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080'

const resolveKioskDeviceId = () => {
  const stored = localStorage.getItem('kiosk_device_id')
  const envValue = import.meta.env.VITE_KIOSK_DEVICE_ID
  const value = stored || envValue
  const parsed = Number.parseInt(value, 10)
  return Number.isNaN(parsed) ? 0 : parsed
}

const resolveKioskToken = () => localStorage.getItem('kiosk_token') || import.meta.env.VITE_KIOSK_TOKEN || ''

const kioskPayload = () => ({
  kiosk_device_id: resolveKioskDeviceId(),
  kiosk_token: resolveKioskToken(),
})

async function request(path, options = {}) {
  const response = await fetch(`${baseUrl}${path}`, {
    headers: {
      'Content-Type': 'application/json',
      ...(options.headers || {}),
    },
    ...options,
  })

  const data = await response.json()
  if (!response.ok) {
    const message = data?.error || 'Request failed'
    throw new Error(message)
  }
  return data
}

export { baseUrl, request, kioskPayload }
