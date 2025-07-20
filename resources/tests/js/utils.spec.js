// Enhanced utility functions test
describe('Utility Functions', () => {
    it('formats date correctly', () => {
        const formatDate = (date) => {
            return new Date(date).toLocaleDateString()
        }

        const testDate = '2024-01-15T00:00:00.000000Z'
        const formatted = formatDate(testDate)
        
        expect(typeof formatted).toBe('string')
        expect(formatted).toMatch(/\d{1,2}\/\d{1,2}\/\d{4}/)
    })

    it('generates full name from parts', () => {
        const getFullName = (user) => {
            if (user.full_name) {
                return user.full_name
            }
            const parts = [user.firstname, user.middlename, user.lastname].filter(Boolean)
            return parts.join(' ')
        }

        const user1 = { firstname: 'John', lastname: 'Doe' }
        const user2 = { firstname: 'Jane', middlename: 'Elizabeth', lastname: 'Smith' }
        const user3 = { full_name: 'Pre-computed Name' }

        expect(getFullName(user1)).toBe('John Doe')
        expect(getFullName(user2)).toBe('Jane Elizabeth Smith')
        expect(getFullName(user3)).toBe('Pre-computed Name')
    })

    it('validates email format', () => {
        const isValidEmail = (email) => {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
            return emailRegex.test(email)
        }

        expect(isValidEmail('test@example.com')).toBe(true)
        expect(isValidEmail('invalid-email')).toBe(false)
        expect(isValidEmail('test@')).toBe(false)
        expect(isValidEmail('@example.com')).toBe(false)
    })

    it('capitalizes first letter', () => {
        const capitalize = (str) => {
            return str.charAt(0).toUpperCase() + str.slice(1)
        }

        expect(capitalize('hello')).toBe('Hello')
        expect(capitalize('world')).toBe('World')
        expect(capitalize('')).toBe('')
    })

    it('truncates text with ellipsis', () => {
        const truncate = (text, maxLength) => {
            if (text.length <= maxLength) return text
            return text.slice(0, maxLength) + '...'
        }

        expect(truncate('Short text', 20)).toBe('Short text')
        expect(truncate('This is a very long text that should be truncated', 20)).toBe('This is a very long ...')
    })
}) 