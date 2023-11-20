export const capitalize = (str: string): string => {
  if (!str) return
  return str.charAt(0).toUpperCase() + str.slice(1)
}

export const parseToBrDate = (str: string): string => {
  if (!str) return
  const date = new Date(str)
  const parsedDate = `${('0' + String(date.getUTCDate())).slice(-2)}/${('0' + String(date.getMonth() + 1)).slice(-2)}/${date.getFullYear()}`
  return parsedDate.replace(/\s/g, '')
}

export const onlyNumbers = (str: string): string => {
  if (!str) return
  return str.replace(/\D/g, '')
}

export const nestedValue = (obj: object, attribute: string): any => {
  return attribute.split('.').reduce((previous, current) => previous[current], obj)
}

export const bool = (str: string | number | boolean): boolean => {
  switch (str) {
    case true:
    case 'true':
    case 1:
    case '1':
    case 'on':
    case 'yes':
      return true
    default:
      return false
  }
}

export const checkForNull = (obj: Object): boolean => {
  return Object.values(obj).includes(null)
}
