<template>
  <div>
   <form class="form-control" @submit.prevent="post" @keydown="form.onKeydown($event)">
    <div class="form-group">
     <textarea class="form-control" v-model="form.body" :class="{ 'is-invalid': form.errors.has('body') }" rows="3"></textarea>
     <has-error :form="form" field="body"/>
   </div>
     <div class="form-row align-items-center">
      <label class="mr-sm-2 col-auto" for="inlineFormCustomSelect">Privacy</label>
     <div class="col-auto my-1">
       <select class="custom-select mr-sm-2" id="inlineFormCustomSelect" v-model="form.privacy" name="privacy">
         <option>public</option>
         <option>friends</option>
         <option>private</option>
       </select>
     </div>
     <div class="col-auto my-1">
       <v-button :loading="form.busy">
                Post
       </v-button>
     </div>
   </div>
   </form>


 <div class="tweetEntry-tweetHolder mt-3">
 <!-- Template Entry -->
 <div class="tweetEntry" v-for="post in posts">
   <div v-if="post.user_id == user.id" class="dropdown float-right">
      <button type="button" class="btn dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" @click="initUpdate(post)">Edit</a>
        <a class="dropdown-item" @click="deletePost(post)">Delete</a>
      </div>
   </div> 
   <div class="tweetEntry-content">
     
     <router-link :to="{ name: 'friend.profile' ,params: { id: post.user_id } }" tag="a" class="tweetEntry-account-group">

       
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
   </div> -->
   
   <div class="tweetEntry-action-list" style="line-height:24px;color: #b1bbc3;">
      <p>{{ post.likes.length }} people likes this</p>
   </div>
   <div class="tweetEntry-action-list" style="line-height:24px;color: #b1bbc3;">
     <fa icon="thumbs-up" v-if="!post.auth_liked" fixed-width @click="addLike(post);" style="width: 80px;"/>
     <fa icon="thumbs-up" v-else fixed-width @click="addLike(post)" style="width: 80px;color: red;"/>
     <fa icon="reply" fixed-width style="width: 80px;"/>
   </div>
   
 </div>

<!--  <a class="btn btn-primary" @click="seeMore()">See more</a>
 -->   
 <!--End of tweetHolder-->
   </div>

   <!-- <b-button @click="showModal">
      Open Modal
    </b-button> -->
    <b-modal ref="myModalRef" hide-footer title="Edit Post">
      <form class="form-control" @submit.prevent="updatePost" @keydown="updateform.onKeydown($event)">
        <div class="form-group">
         <textarea class="form-control" v-model="updateform.body" :class="{ 'is-invalid': updateform.errors.has('body') }" rows="3"></textarea>
         <has-error :form="updateform" field="body"/>
       </div>
         <div class="form-row align-items-center">
          <label class="mr-sm-2 col-auto" for="inlineFormCustomSelect">Privacy</label>
         <div class="col-auto my-1">
           <select class="custom-select mr-sm-2" id="inlineFormCustomSelect" v-model="updateform.privacy" name="privacy">
             <option>public</option>
             <option>friends</option>
             <option>private</option>
           </select>
         </div>
         <div class="col-auto my-1">
           <v-button :loading="updateform.busy">
                    Edit
           </v-button>
           <button type="button" class="btn btn-default" data-dismiss="modal" block @click="$refs.myModalRef.hide()">Close</button>
         </div>
       </div>
       </form>
    </b-modal>

  </div> 



</template>

<script>
import axios from 'axios'
import Form from 'vform'
import { mapGetters } from 'vuex'
import swal from 'sweetalert2'

export default {
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({
    form: new Form({
      body: '',
      privacy: 'public'
    }),
    updateform: new Form({
      body: '',
      privacy: ''
    }),
    posts: []
  }),

  methods: {
    getPosts: function(e) {
      axios.get('/api/posts').then((res) =>{
        this.posts = res.data,
        console.log(res.data)
      })
    },
    async post () {
      // Submit the form.
      const { data } = await this.form.post('/api/post')
      console.log(data)
      this.form.body = '',
      this.form.privacy = 'public',
      this.posts.unshift(data),
  	  this.flash('Post Publiched', 'success',{
  		  timeout: 3000,
  		});
    },
    deletePost: function(post) {
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
            axios.delete('/api/post/'+ post.id).then((res) =>{
            let index = this.posts.indexOf(post)
            this.posts.splice(index, 1)
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
    initUpdate(post)
    {
        this.$refs.myModalRef.show();
        this.updateform.body = post.body;
        this.updateform.privacy = post.privacy;
        this.updateform.id = post.id;
        this.updateform.index = this.posts.indexOf(post);
    },
    async updatePost()
    {   
        // Submit the form.
        this.updateform.patch('/api/post/' + this.updateform.id).then(({ data }) => {
          this.$set(this.posts, this.updateform.index, data);
          this.$refs.myModalRef.hide();
          this.flash('Post Edited', 'success',{
            timeout: 3000,
          });
         });

    },
    addLike: function(post) {
      axios.get('/api/posts/like/'+ post.id).then((res) =>{
        console.log(res)
        let index = this.posts.indexOf(post);
        if(!post.auth_liked){
          this.posts[index].likes.unshift(res.data)
        } else {
          let likeIndex = this.posts[index].likes.indexOf(res.data)
          this.posts[index].likes.splice(likeIndex, 1) 
        }
        this.posts[index].auth_liked = !this.posts[index].auth_liked;
      })
    }
  },

  computed: mapGetters({
    user: 'auth/user'
  }),

  created() {
   this.getPosts()
  }


}
</script>

