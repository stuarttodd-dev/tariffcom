import { mount } from '@vue/test-utils'
import TrashedUsers from '@/Pages/Users/Trashed.vue'

jest.mock('@inertiajs/vue3', () => ({
    router: {
        visit: jest.fn(),
        get: jest.fn(),
        post: jest.fn(),
        delete: jest.fn(),
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

global.route = jest.fn((name, id) => id ? `/${name.replace('.', '/')}/${id}` : `/${name.replace('.', '/')}`)

describe('Users/Trashed.vue', () => {
    it('shows table headers', () => {
        const users = [
            { id: 1, firstname: 'John', lastname: 'Doe', email: 'john@example.com', type: 'user', prefixname: 'Mr', deleted_at: '2024-01-01T00:00:00.000000Z' }
        ]
        const wrapper = mount(TrashedUsers, {
            props: {
                users: { data: users },
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
        const headers = ['Name', 'Email', 'Type', 'Deleted At', 'Actions']
        headers.forEach(header => {
            expect(wrapper.html()).toContain(header)
        })
    })

    it('shows correct number of user rows', () => {
        const users = [
            { id: 1, firstname: 'John', lastname: 'Doe', email: 'john@example.com', type: 'user', prefixname: 'Mr', deleted_at: '2024-01-01T00:00:00.000000Z' },
            { id: 2, firstname: 'Jane', lastname: 'Smith', email: 'jane@example.com', type: 'admin', prefixname: 'Ms', deleted_at: '2024-01-02T00:00:00.000000Z' }
        ]
        const wrapper = mount(TrashedUsers, {
            props: {
                users: { data: users },
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
            { id: 1, firstname: 'John', lastname: 'Doe', email: 'john@example.com', type: 'user', prefixname: 'Mr', deleted_at: '2024-01-01T00:00:00.000000Z' },
            { id: 2, firstname: 'Jane', lastname: 'Smith', email: 'jane@example.com', type: 'admin', prefixname: 'Ms', deleted_at: '2024-01-02T00:00:00.000000Z' }
        ]
        const wrapper = mount(TrashedUsers, {
            props: {
                users: { data: users },
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
        rows.forEach(row => {
            expect(row.text()).toMatch(/Restore/i)
            expect(row.text()).toMatch(/Delete Permanently/i)
        })
    })
}) 