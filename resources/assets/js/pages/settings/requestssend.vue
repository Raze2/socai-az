<template>
 <card :title="$t('requestssend')">
  <ul class="list-group">
    <router-link :to="{ name: 'friend.profile' ,params: { id: request.id } }" tag="li" class="list-group-item btn btn-light" v-for="request in requestssend">
      <div class="float-left">{{ request.name }}</div>
      <button class="btn btn-sm btn-danger float-right" @click.prevent="deleteRequest(request)"><fa icon="times" fixed-width/></button>
    </router-link>
    <li class="list-group-item" v-if="requestssend.length == 0">
      No requested send
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
      requestssend: []
    } 
  },

  methods: {
    getRequests: function(e) {
      axios.get('/api/friends/friendrequests').then((res) =>{
        this.requestssend = res.data 
      })
    },
    deleteRequest: function(request) {
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
          axios.delete('/api/friends/'+request.pivot.id).then((res) =>{
            let index = this.requestssend.indexOf(request)
            this.requestssend.splice(index, 1)
            if(res) {
              swal(
                'Deleted!',
                'Your request has been deleted.',
                'success'
              )
            }
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

