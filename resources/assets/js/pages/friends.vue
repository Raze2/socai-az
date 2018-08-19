<template>
 <card :title="$t('friends')">
  <ul class="list-group">
      
      <router-link :to="{ name: 'friend.profile' ,params: { id: friend.id } }" tag="li" class="list-group-item btn btn-light" v-for="friend in friends">
        <div class="float-left">{{ friend.name }}</div>
        <button class="btn btn-sm btn-danger float-right" @click.prevent="deleteRequest(friend)"><fa icon="user-times" fixed-width/></button>
        <button class="btn btn-sm btn-primary float-right mr-1"><fa icon="comment" fixed-width/></button>
      </router-link>
    <li class="list-group-item" v-if="friends.length == 0">
      No friends 
    </li>
  </ul>
 </card>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
import swal from 'sweetalert2'

export default {
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('friends') }
  },

  data() {
    return {
      friends: []
    } 
  },

  methods: {
    getRequests: function(e) {
      axios.get('/api/friends').then((res) =>{
        this.friends = res.data 
      })
    },
    deleteRequest: function(friend) {
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
          axios.delete('/api/friends/'+friend.pivot.id).then((res) =>{
            let index = this.friends.indexOf(friend)
            this.friends.splice(index, 1)
            if(res) {
              swal(
                'Deleted!',
                'Your friend has been deleted.',
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

