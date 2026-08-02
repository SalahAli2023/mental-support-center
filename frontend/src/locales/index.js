
import { ar } from './ar';
import { en } from './en';

export const translations = { ar, en };

// Helper function to get translation
export function t(key, language = 'ar') {
  const keys = key.split('.');
  let value = translations[language];

  
  for (const k of keys) {
    if (value && typeof value === 'object' && k in value) {
      value = value[k];
    } else {
      return key; // Return key if translation not found
    }
  }
  return value || key
}