<template>
 <card :title="$t('blockedusers')">
  <ul class="list-group">
    <router-link :to="{ name: 'friend.profile' ,params: { id: user.id } }" tag="li" class="list-group-item btn btn-light" v-for="user in blockedusers">
      <div class="float-left">{{ user.name }}</div>
      <button class="btn btn-sm btn-danger float-right" @click.prevent="deleteRequest(user)"><fa icon="times" fixed-width/></button>
    </router-link>
    <li class="list-group-item" v-if="blockedusers.length == 0">
      No users blocked
    </li>
  </ul>
 </card>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
import swal from 'sweetalert2'

export default {
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data() {
    return {
      blockedusers: []
    } 
  },

  methods: {
    getRequests: function(e) {
      axios.get('/api/friends/blockedusers').then((res) =>{
        this.blockedusers = res.data 
      })
    },
    deleteRequest: function(user) {
      swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.value) {
          axios.delete('/api/friends/'+user.id).then((res) =>{
            let index = this.blockedusers.indexOf(user)
            this.blockedusers.splice(index, 1)
              swal(
                'Deleted!',
                'Your block has been deleted.',
                'success'
              )
          })
        }
      })
    }
  },

  created() {
    this.getRequests()
  }
}
</script>

