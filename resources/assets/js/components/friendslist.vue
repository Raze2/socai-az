<template>
  <ul class="list-group">
      <router-link :to="{ name: 'friend.profile' ,params: { id: friend.id } }" tag="li" class="list-group-item btn btn-light" v-for="friend in friends">
        <div class="float-left">{{ friend.name }}</div>
        <button class="btn btn-sm btn-danger float-right" v-if="buttons != 3" @click.prevent="deleteRequest(friend)"><fa icon="user-times" fixed-width/></button>
        <button class="btn btn-sm btn-danger float-right" v-if="buttons == 3" @click.prevent="deleteRequest(friend)"><fa icon="times" fixed-width/></button>
        <button class="btn btn-sm btn-primary float-right mr-1" v-if="buttons == 2" @click.prevent="acceptRequest(friend)"><fa icon="check" fixed-width/></button>
        <button class="btn btn-sm btn-primary float-right mr-1" v-if="buttons == 1"><fa icon="comment" fixed-width/></button>
      </router-link>
    <li class="list-group-item" v-if="friends.length == 0">
      No results 
    </li>
  </ul>
</template>

<script>
import axios from 'axios'
import swal from 'sweetalert2'

export default {
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('friends') }
  },

  props: ['url','buttons'],

  data() {
    return {
      friends: []
    } 
  },

  methods: {
    getRequests: function(e) {
      axios.get(this.url).then((res) =>{
        this.friends = res.data 
        console.log(res)
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
                'Your request done.',
                'success'
              )
            }
          })
        }
      })
    },
    acceptRequest: function(friend) {
          axios.put('/api/friends/'+friend.pivot.first_user, []).then((res) =>{
            let index = this.friends.indexOf(friend)
            this.friends.splice(index, 1);
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

