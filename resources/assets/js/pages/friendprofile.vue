<template>
<div>
 <div class="card card-profile text-center">
  <img class="card-img-top" src="https://unsplash.it/340/160?image=354"/>
  <div class="card-block">
    <img class="card-img-profile" :src="friend.photo_url"/>
    <h4 class="card-title">
      {{ friend.name }}
      <!-- <small>Front-end designer</small> -->
    </h4>
    <div class="card-links" v-if="show">
      <button class="btn btn-sm btn-primary mr-1" v-if="friend.friendship.status == 'pending' && friend.friendship.first_user == this.$route.params.id" @click="acceptRequest()"><fa icon="check" fixed-width/> Accept</button>
      <button class="btn btn-sm btn-primary mr-1" v-if="!friend.friendship" @click="sendRequest()"><fa icon="user-plus" fixed-width/> Add</button>
      <button class="btn btn-sm btn-primary mr-1" disabled v-if="friend.friendship.status == 'pending' && friend.friendship.first_user == user.id"><fa icon="user-plus" fixed-width/> Request sended</button>
      <button class="btn btn-sm btn-danger" v-if="friend.friendship.status" @click="deleteRequest()"><fa icon="times" fixed-width/> Delete</button>
    </div>
  </div>
</div>

<div class="tweetEntry-tweetHolder mt-3">
 <!-- Template Entry -->
 <div class="tweetEntry" v-for="post in posts">
   <div class="tweetEntry-content">
     
     <router-link :to="{ name: 'friend.profile' ,params: { id: post.user.id } }" tag="a" class="tweetEntry-account-group">

       
       <img class="tweetEntry-avatar" :src="post.user.photo_url">
       
       <strong class="tweetEntry-fullname">
       {{ post.user.name }}
       </strong>

     </router-link>
       
       <span class="tweetEntry-username">
         <b>{{ post.privacy }}</b>
       </span>
       
       <span class="tweetEntry-timestamp">- {{ post.created_at }}</span>
       
     <span v-if="post.created_at != post.updated_at" class="tweetEntry-username">
           <b>Edited</b>
        </span>
     
     <div class="tweetEntry-text-container">
       {{ post.body }}
     </div>
     
   </div>
   
   <!-- <div class="optionalMedia" style="[displayMedia]">
     <img class="optionalMedia-img" src="[tweetImageLinkSource]">
   </div>
   
   <div class="tweetEntry-action-list" style="line-height:24px;color: #b1bbc3;">
     <i class="fa fa-reply" style="width: 80px;"></i>
     <i class="fa fa-retweet" style="width: 80px"></i>
     <i class="fa fa-heart" style="width: 80px"></i>
   </div> -->
   
 </div>
   
 <!--End of tweetHolder-->
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
      friend: {
        friendship: []
      },
      posts: [],
      show: true
    } 
  },

  methods: {
    getData: function(e) {
      axios.get('/api/friends/'+ this.$route.params.id).then((res) =>{
        this.friend = res.data 
        this.getPosts()
      })
    },
    getPosts: function(e) {
      axios.get('/api/posts/' + this.$route.params.id).then((res) =>{
        this.posts = res.data,
        console.log(res)
      })
    },
    sendRequest: function(e) {
      axios.post('/api/friends/'+ this.$route.params.id, []).then((res) =>{
        this.friend.friendship = {status: 'pending',first_user: this.user.id}
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
            axios.delete('/api/friends/'+this.friend.friendship.id).then((res) =>{
            this.friend.friendship = false
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
          axios.put('/api/friends/'+this.$route.params.id, []).then((res) =>{
            this.friend.friendship.status = 'confirmed',
            console.log(res)
            swal(
                'Accepted!',
                'Your friend has been added.',
                'success'
              )
          })
    },
    checkUser: function(e) {
      if (this.user.id == this.$route.params.id) {
        this.show = false
      } 
    }
  },

  computed: mapGetters({
    user: 'auth/user'
  }),

  created() {
    this.getData()
    this.checkUser()
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

