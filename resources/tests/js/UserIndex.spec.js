import { mount } from '@vue/test-utils'
import UserIndex from '@/Pages/Users/Index.vue'

jest.mock('@inertiajs/vue3', () => ({
    router: {
        visit: jest.fn(),
        get: jest.fn(),
    },
    Link: {
        template: '<a><slot /></a>',
    },
    usePage: () => ({
        props: {
            auth: {
                user: { id: 1 }
            }
        }
    })
}))

global.route = jest.fn((name) => `/${name.replace('.', '/')}`)

describe('Users/Index.vue', () => {
    it('renders Create User button', () => {
        const wrapper = mount(UserIndex, {
            props: {
                users: {
                    data: [],
                    links: []
                },
                filters: {}
            },
            global: {
                stubs: {
                    AuthenticatedLayout: { template: '<div><slot /></div>' },
                    PrimaryButton: { template: '<button><slot /></button>' },
                    SecondaryButton: { template: '<button><slot /></button>' },
                    DangerButton: { template: '<button><slot /></button>' },
                    Modal: { template: '<div><slot /></div>' },
                },
                config: {
                    globalProperties: { route: global.route }
                }
            }
        })
        const createUserButton = wrapper.findAll('button').find(button =>
            button.text().toLowerCase().includes('create user')
        )
        expect(createUserButton).toBeDefined()
        expect(createUserButton.exists()).toBe(true)
    })

    it('shows table headers', () => {
        const users = [
            { id: 1, firstname: 'John', lastname: 'Doe', email: 'john@example.com', type: 'user', prefixname: 'Mr', created_at: '2024-01-01T00:00:00.000000Z' }
        ]
        const wrapper = mount(UserIndex, {
            props: {
                users,
                filters: {}
            },
            global: {
                stubs: {
                    AuthenticatedLayout: { template: '<div><slot /></div>' },
                    PrimaryButton: { template: '<button><slot /></button>' },
                    SecondaryButton: { template: '<button><slot /></button>' },
                    DangerButton: { template: '<button><slot /></button>' },
                    Modal: { template: '<div><slot /></div>' },
                },
                config: { globalProperties: { route: global.route } }
            }
        })
        const headers = ['Name', 'Email', 'Type', 'Created', 'Actions']
        headers.forEach(header => {
            expect(wrapper.html()).toContain(header)
        })
    })

    it('shows correct number of user rows', () => {
        const users = [
            { id: 1, firstname: 'John', lastname: 'Doe', email: 'john@example.com', type: 'user', prefixname: 'Mr', created_at: '2024-01-01T00:00:00.000000Z' },
            { id: 2, firstname: 'Jane', lastname: 'Smith', email: 'jane@example.com', type: 'admin', prefixname: 'Ms', created_at: '2024-01-02T00:00:00.000000Z' }
        ]
        const wrapper = mount(UserIndex, {
            props: {
                users,
                filters: {}
            },
            global: {
                stubs: {
                    AuthenticatedLayout: { template: '<div><slot /></div>' },
                    PrimaryButton: { template: '<button><slot /></button>' },
                    SecondaryButton: { template: '<button><slot /></button>' },
                    DangerButton: { template: '<button><slot /></button>' },
                    Modal: { template: '<div><slot /></div>' },
                },
                config: { globalProperties: { route: global.route } }
            }
        })
        const rows = wrapper.findAll('tbody tr')
        expect(rows.length).toBe(users.length)
    })

    it('shows correct action buttons for each user row', () => {
        const users = [
            { id: 1, firstname: 'John', lastname: 'Doe', email: 'john@example.com', type: 'user', prefixname: 'Mr', created_at: '2024-01-01T00:00:00.000000Z' },
            { id: 2, firstname: 'Jane', lastname: 'Smith', email: 'jane@example.com', type: 'admin', prefixname: 'Ms', created_at: '2024-01-02T00:00:00.000000Z' }
        ]
        const wrapper = mount(UserIndex, {
            props: {
                users,
                filters: {}
            },
            global: {
                stubs: {
                    AuthenticatedLayout: { template: '<div><slot /></div>' },
                    PrimaryButton: { template: '<button><slot /></button>' },
                    SecondaryButton: { template: '<button><slot /></button>' },
                    DangerButton: { template: '<button><slot /></button>' },
                    Modal: { template: '<div><slot /></div>' },
                },
                config: { globalProperties: { route: global.route } }
            }
        })
        const rows = wrapper.findAll('tbody tr')
        rows.forEach((row, idx) => {
            expect(row.text()).toMatch(/View/i)
            expect(row.text()).toMatch(/Edit/i)
            const userId = users[idx].id
            if (userId === 2) {
                expect(row.text()).toMatch(/Delete/i)
            } else {
                expect(row.text()).not.toMatch(/Delete/i)
            }
        })
    })
}) 