import * as helpers from './helper'

describe('utils - helper functions', () => {
  const { bool, capitalize, nestedValue, onlyNumbers, parseToBrDate, checkForNull } = helpers

  describe('capitalize', () => {
    it('should return capitalized string', () => {
      const string = 'mystring'

      const response = capitalize(string)

      expect(response).toBe('Mystring')
    })

    it('should return nothing if string is empty', () => {
      const string = ''

      const response = capitalize(string)

      expect(response).toBeUndefined()
    })
  })

  describe('parseToBrDate', () => {
    it('should return brazilian date', () => {
      const usaDate = '12-24-1998'

      const response = parseToBrDate(usaDate)

      expect(response).toBe('24/12/1998')
    })

    it('should return nothing if string is empty', () => {
      const string = ''

      const response = parseToBrDate(string)

      expect(response).toBeUndefined()
    })
  })

  describe('onlyNumbers', () => {
    it('should remove special and alphabetical chars from string', () => {
      const string = '1.2.3.4.5.6.7.8abc'

      const response = onlyNumbers(string)

      expect(response).toBe('12345678')
    })

    it('should return nothing if string is empty', () => {
      const string = ''

      const response = onlyNumbers(string)

      expect(response).toBeUndefined()
    })
  })

  describe('nestedValue', () => {
    it('should get nested value from object', () => {
      const obj = {
        any: {
          anyInside: 'insideValue'
        }
      }

      const response = nestedValue(obj, 'any.anyInside')
      expect(response).toBe('insideValue')
    })
  })

  describe('bool', () => {
    it('should return true for defined truthy values', () => {
      const adaptedTruthy = [true, 'true', 1, '1', 'on', 'yes'].map((t) => (bool(t)))

      adaptedTruthy.map((t) => expect(t).toBe(true))
    })

    it('should return false for any different value', () => {
      expect(bool('any')).toBe(false)
    })
  })

  describe('checkForNull', () => {
    it('should return true for objects with at least one null property', () => {
      const object = {
        any1: '',
        any2: '',
        any3: null
      }

      expect(checkForNull(object)).toBe(true)
    })

    it('should return false for objects with non null properties', () => {
      const object = {
        any1: '',
        any2: '',
        any3: ''
      }

      expect(checkForNull(object)).toBe(false)
    })
  })
})
