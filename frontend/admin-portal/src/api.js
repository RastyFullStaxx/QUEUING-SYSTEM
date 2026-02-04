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

  let data = {}
  try {
    data = await response.json()
  } catch (err) {
    data = {}
  }
  if (!response.ok) {
    const message = data?.error || 'Request failed'
    const error = new Error(message)
    error.data = data
    error.status = response.status
    throw error
  }
  return data
}

export { baseUrl, request }
