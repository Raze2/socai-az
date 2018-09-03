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
import axios from 'axios'
import Form from 'vform'
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
    posts: []
  }),

  methods: {
    getPosts: function(e) {
      axios.get('/api/posts').then((res) =>{
        this.posts = res.data,
        console.log(res)
      })
    },
    async post () {
      // Submit the form.
      const { data } = await this.form.post('/api/post')
      this.posts.unshift(data),
      this.form.body = '',
      this.form.privacy = 'public',
	  this.flash('Post Publiched', 'success',{
		  timeout: 3000,
		});
    } 
  },

  created() {
   this.getPosts()
  }


}
</script>

