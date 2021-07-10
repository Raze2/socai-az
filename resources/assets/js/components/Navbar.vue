<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
      <router-link :to="{ name: user ? 'home' : 'welcome' }" class="navbar-brand">{{ appName }}</router-link>

      <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarToggler"
        aria-controls="navbarToggler"
        aria-expanded="false"
      >
        <span class="navbar-toggler-icon"/>
      </button>

      <div id="navbarToggler" class="collapse navbar-collapse">
        <ul class="navbar-nav">
          <locale-dropdown/>
          <!-- <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>-->
        </ul>

        <ul class="navbar-nav ml-auto">
          <!-- Authenticated -->
          <li v-if="user" class="nav-item">
            <router-link :to="{ name: 'search' }" class="nav-link" active-class="active">
              <fa icon="search" fixed-width/>
              {{ $t('search') }}
            </router-link>
          </li>
          <li v-if="user" class="nav-item">
            <router-link :to="{ name: 'friends' }" class="nav-link" active-class="active">
              <fa icon="users" fixed-width/>
              {{ $t('friends') }}
            </router-link>
          </li>
          <li v-if="user" class="nav-item dropdown">
            <a
              class="nav-link text-dark"
              href="#"
              role="button"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
              @click="readAllNotifications"
            >
              <fa icon="bell" fixed-width/>
              <span id="notificationsBadge" class="badge badge-danger">{{unreadNotifications.length}}</span>
            </a>

            <div class="dropdown-menu" v-if="unreadNotifications.length">
              <router-link 
                v-for="notification in unreadNotifications"
                v-bind:key="notification.id"
                :to="{ name: 'post', params: { id: notification.data.post_id } }"
                class="dropdown-item pl-3"
              >
                <img :src="'../img/profile/'+notification.data.user_avatar" class="rounded-circle profile-photo mr-1"> {{notification.data.user_name}} has commented to your post
              </router-link>
            </div>
            <div class="dropdown-menu" v-else>
              <li class="dropdown-item">There's no new notification</li>
            </div>
          </li>
          <li v-if="user" class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle text-dark"
              href="#"
              role="button"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <img :src="user.photo_url" class="rounded-circle profile-photo mr-1">
              {{ user.name }}
            </a>

            <div class="dropdown-menu">
              <router-link
                :to="{ name: 'friend.profile', params: { id: user.id } }"
                class="dropdown-item pl-3"
              >
                <fa icon="user" fixed-width/>
                {{ $t('profile') }}
              </router-link>

              <div class="dropdown-divider"/>
              <router-link :to="{ name: 'requests.recived' }" class="dropdown-item pl-3">
                <fa icon="user-plus" fixed-width/>
                {{ $t('requestsrecived') }}
              </router-link>

              <div class="dropdown-divider"/>
              <router-link :to="{ name: 'settings.profile' }" class="dropdown-item pl-3">
                <fa icon="cog" fixed-width/>
                {{ $t('settings') }}
              </router-link>

              <div class="dropdown-divider"/>
              <a href="#" class="dropdown-item pl-3" @click.prevent="logout">
                <fa icon="sign-out-alt" fixed-width/>
                {{ $t('logout') }}
              </a>
            </div>
          </li>
          <!-- Guest -->
          <template v-else>
            <li class="nav-item">
              <router-link
                :to="{ name: 'login' }"
                class="nav-link"
                active-class="active"
              >{{ $t('login') }}</router-link>
            </li>
            <li class="nav-item">
              <router-link
                :to="{ name: 'register' }"
                class="nav-link"
                active-class="active"
              >{{ $t('register') }}</router-link>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import axios from "axios";
import { mapGetters } from "vuex";
import LocaleDropdown from "./LocaleDropdown";

export default {
  components: {
    LocaleDropdown
  },

  data: () => ({
    appName: window.config.appName
  }),

  computed: {
    unreadNotifications(){
        return this.user.notifications.filter(notification=>{
          return notification.read_at == null;
        });
    },
    ...mapGetters({
      user: "auth/user"
    })
  },

  created () {

    window.Echo.private('App.User.' + this.user.id)
    .notification((notification) => {
        console.log("new");
    }, (e) => {
                console.log(e);
    });
  },

  methods: {
    async logout() {
      // Log out the user.
      await this.$store.dispatch("auth/logout");

      // Redirect to login.
      this.$router.push({ name: "login" });
    },
    readAllNotifications(){
      axios.get('api/notification/mark-all-read').then(response=>{
        console.log(response);
      });
    }
  }
};
</script>

<style scoped>
.profile-photo {
  width: 2rem;
  height: 2rem;
  margin: -0.375rem 0;
}
</style>
