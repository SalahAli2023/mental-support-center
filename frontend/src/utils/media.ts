const DEFAULT_API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

const getApiOrigin = (): string => {
      try {
            const url = new URL(DEFAULT_API_BASE)
            return `${url.protocol}//${url.host}`
      } catch (error) {
            console.warn('Failed to parse API base URL, falling back to window origin.', error)
            return typeof window !== 'undefined' ? window.location.origin : ''
      }
}

const getStorageBase = (): string => {
      const envStorage = import.meta.env.VITE_STORAGE_URL
      if (envStorage) {
            try {
                  const url = new URL(envStorage)
                  return `${url.protocol}//${url.host}${url.pathname.replace(/\/$/, '')}`
            } catch (error) {
                  console.warn('Invalid VITE_STORAGE_URL, falling back to API origin.', error)
            }
      }

      const apiOrigin = getApiOrigin()
      // إزالة /api إن وُجد في النهاية
      return apiOrigin.replace(/\/api\/?$/, '')
}

export const resolveMediaUrl = (path?: string | null, fallback = ''): string => {
      if (!path || typeof path !== 'string' || !path.trim()) {
            return fallback
      }

      if (path.startsWith('data:')) {
            return path
      }

      if (/^https?:\/\//i.test(path)) {
            return path
      }

      const storageBase = getStorageBase().replace(/\/$/, '')
      const normalizedPath = path.replace(/^\/+/, '')

      if (normalizedPath.startsWith('storage/')) {
            return `${storageBase}/${normalizedPath}`
      }

      return `${storageBase}/storage/${normalizedPath}`
}

export { getApiOrigin }