<template>
 <div class="card card-profile text-center">
  <img class="card-img-top" src="https://unsplash.it/340/160?image=354"/>
  <div class="card-block">
    <img class="card-img-profile" :src="friend.photo_url"/>
    <h4 class="card-title">
      {{ friend.name }}
      <!-- <small>Front-end designer</small> -->
    </h4>
    <div class="card-links" v-if="show">
      <button class="btn btn-sm btn-primary mr-1" v-if="friend.friend === 1" @click="acceptRequest()"><fa icon="check" fixed-width/> Accept</button>
      <button class="btn btn-sm btn-primary mr-1" v-if="friend.friend === 0" @click="sendRequest()"><fa icon="user-plus" fixed-width/> Add</button>
      <button class="btn btn-sm btn-primary mr-1" disabled v-if="friend.friend === 2"><fa icon="user-plus" fixed-width/> Request sended</button>
      <button class="btn btn-sm btn-danger" v-if="friend.friend !== 0" @click="deleteRequest()"><fa icon="times" fixed-width/> Delete</button>
    </div>
  </div>
</div>

</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
import swal from 'sweetalert2'

export default {
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data() {
    return {
      friend: [],
      show: true
    } 
  },

  methods: {
    getRequests: function(e) {
      axios.get('/api/friends/'+ this.$route.params.id).then((res) =>{
        this.friend = res.data 
        console.log(res.data)
      })
    },
    sendRequest: function(e) {
      axios.post('/api/friends/'+ this.$route.params.id, []).then((res) =>{
        if(res){
          this.friend.friend = 2
        }
      })
    },
    deleteRequest: function() {
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
            axios.delete('/api/friends/profile/'+this.$route.params.id).then((res) =>{
            this.friend.friend = 0 
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
    acceptRequest: function() {
          axios.put('/api/friends/profile/'+this.$route.params.id, []).then((res) =>{
            this.friend.friend = 'friend',
            swal(
                'Accepted!',
                'Your friend has been added.',
                'success'
              )
          })
    },
    checkUser: function(e) {
      if (this.user.id == this.$route.params.id){
        this.show = false
      }
    }
  },

  computed: mapGetters({
    user: 'auth/user'
  }),

  created() {
    this.checkUser()
    this.getRequests()
  }
}
</script>

<style scoped>

html {
  min-height: 100%;
  background: linear-gradient(#9d9181, #9e866c);
}
body {
  background-color: transparent;
}
.card-profile {
  width: 340px;
  margin: 50px auto;
  background-color: #e6e5e1;
  border-radius: 0;
  border: 0;
  box-shadow: 1em 1em 2em rgba(0, 0, 0, .2);
}
.card-profile .card-img-top {
  border-radius: 0;
}
.card-profile .card-img-profile {
  max-width: 100%;
  border-radius: 50%;
  margin-top: -95px;
  margin-bottom: 35px;
  border: 5px solid #e6e5e1;
}
.card-profile .card-title {
  margin-bottom: 50px;
}
.card-profile .card-title small {
  display: block;
  font-size: 0.6em;
  margin-top: 0.2em;
}
.card-profile .card-links {
  margin-bottom: 25px;
}
.card-profile .card-links .fa {
  margin: 0 1em;
  font-size: 1.6em;
}
.card-profile .card-links .fa:focus, .card-profile .card-links .fa:hover {
  text-decoration: none;
}
.card-profile .card-links .fa.fa-dribbble {
  color: #ea4b89;
  font-weight: bold;
}
.card-profile .card-links .fa.fa-dribbble:hover {
  color: #e51d6b;
}
.card-profile .card-links .fa.fa-twitter {
  color: #68aade;
}
.card-profile .card-links .fa.fa-twitter:hover {
  color: #3e92d5;
}
.card-profile .card-links .fa.fa-facebook {
  color: #3b5999;
}
.card-profile .card-links .fa.fa-facebook:hover {
  color: #2d4474;
}


</style>

