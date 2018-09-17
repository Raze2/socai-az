const Welcome = () => import('~/pages/welcome').then(m => m.default || m)
const Login = () => import('~/pages/auth/login').then(m => m.default || m)
const Register = () => import('~/pages/auth/register').then(m => m.default || m)
const PasswordEmail = () => import('~/pages/auth/password/email').then(m => m.default || m)
const PasswordReset = () => import('~/pages/auth/password/reset').then(m => m.default || m)
const Friends = () => import('~/pages/friends').then(m => m.default || m)
const RequestsRecived = () => import('~/pages/requestsrecived').then(m => m.default || m)
const FriendsProfile = () => import('~/pages/friendprofile').then(m => m.default || m)
const Search = () => import('~/pages/search').then(m => m.default || m)
const NotFound = () => import('~/pages/errors/404').then(m => m.default || m)

const Home = () => import('~/pages/home').then(m => m.default || m)
const Settings = () => import('~/pages/settings/index').then(m => m.default || m)
const SettingsProfile = () => import('~/pages/settings/profile').then(m => m.default || m)
const SettingsPassword = () => import('~/pages/settings/password').then(m => m.default || m)
const SettingsRequestsSend = () => import('~/pages/settings/requestssend').then(m => m.default || m)
const SettingsBlockedUsers = () => import('~/pages/settings/blockedusers').then(m => m.default || m)

export default [
  { path: '/', name: 'welcome', component: Welcome },

  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/password/reset', name: 'password.request', component: PasswordEmail },
  { path: '/password/reset/:token', name: 'password.reset', component: PasswordReset },
  { path: '/friends', name: 'friends', component: Friends },
  { path: '/friends/requestsrecived', name: 'requests.recived', component: RequestsRecived },
  { path: '/profile/:id', name: 'friend.profile', component: FriendsProfile },
  { path: '/search', name: 'search', component: Search },

  { path: '/home', name: 'home', component: Home },
  { path: '/settings',
    component: Settings,
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: SettingsProfile },
      { path: 'password', name: 'settings.password', component: SettingsPassword },
      { path: 'requestssend', name: 'settings.requests.send', component: SettingsRequestsSend },
      { path: 'blockedusers', name: 'settings.blocked', component: SettingsBlockedUsers }
    ] },

  { path: '*', name: 'notfound', component: NotFound }
]
