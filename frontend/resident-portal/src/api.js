const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080'

async function request(path, options = {}) {
  const isFormData = options.body instanceof FormData
  const headers = {
    ...(options.headers || {}),
  }
  if (!isFormData) {
    headers['Content-Type'] = 'application/json'
  }
  const response = await fetch(`${baseUrl}${path}`, {
    headers,
    ...options,
  })

  const data = await response.json()
  if (!response.ok) {
    const message = data?.error || 'Request failed'
    throw new Error(message)
  }
  return data
}

export { baseUrl, request }
