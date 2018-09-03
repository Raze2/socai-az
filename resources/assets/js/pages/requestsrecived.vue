<template>
 <card :title="$t('requestsrecived')">
  <ul class="list-group">
    <router-link :to="{ name: 'friend.profile' ,params: { id: request.id } }" tag="li" class="list-group-item btn btn-light" v-for="request in requestsrecived">
      <div class="float-left">{{ request.name }}</div>
      <button class="btn btn-sm btn-danger float-right" @click.prevent="deleteRequest(request)"><fa icon="times" fixed-width/></button>
      <button class="btn btn-sm btn-primary float-right mr-1" @click.prevent="acceptRequest(request)"><fa icon="check" fixed-width/></button>
    </router-link>
    <li class="list-group-item" v-if="requestsrecived.length == 0">
      No requested recived
    </li>
  </ul>
 </card>
</template>

<script>
import axios from 'axios'
import swal from 'sweetalert2'

export default {
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data() {
    return {
      requestsrecived: []
    } 
  },

  methods: {
    getRequests: function(e) {
      axios.get('/api/friends/requestsrecived').then((res) =>{
        this.requestsrecived = res.data 
        console.log(res)
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
            let index = this.requestsrecived.indexOf(request)
            this.requestsrecived.splice(index, 1)
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
    },
    acceptRequest: function(request) {
          axios.put('/api/friends/'+request.pivot.first_user, []).then((res) =>{
            let index = this.requestsrecived.indexOf(request)
            this.requestsrecived.splice(index, 1);
            swal(
                'Accepted!',
                'Your friend has been added.',
                'success'
              )
          })
    },
  },

  created() {
    this.getRequests()
  }
}
</script>

