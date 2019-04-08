<template>
  <div class="tweetEntry">
    <div v-if="post.user_id == user.id" class="dropdown float-right">
      <button
        type="button"
        class="btn dropdown-toggle"
        id="dropdownMenuButton"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
      ></button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" @click="$emit('init-post', post, index)">Edit</a>
        <a class="dropdown-item" @click="$emit('delete-post', post, index)">Delete</a>
      </div>
    </div>
    <div class="tweetEntry-content">
      <router-link
        :to="{ name: 'friend.profile' ,params: { id: post.user_id } }"
        tag="a"
        class="tweetEntry-account-group"
      >
        <img class="tweetEntry-avatar" :src="post.user.photo_url">
        
        <strong class="tweetEntry-fullname">{{ post.user.name }}</strong>
      </router-link>

      <span class="tweetEntry-username">
        <b>{{ post.privacy }}</b>
      </span>
      
      <span class="tweetEntry-timestamp">- {{ post.created_at }}</span>
      
      <span v-if="post.created_at != post.updated_at" class="tweetEntry-username">
        <b>Edited</b>
      </span>

      <div class="tweetEntry-text-container">{{ post.body }}</div>
    </div>

    <!-- <div class="optionalMedia" style="[displayMedia]">
       <img class="optionalMedia-img" src="[tweetImageLinkSource]">
    </div>-->
    <div class="optionalMedia" v-if="post.photo_url">
      <img class="optionalMedia-img" :src="'../img/posts/' + post.photo_url">
    </div>
    <div class="tweetEntry-action-list" style="line-height:24px;color: #b1bbc3;">
      <a class="ml-2">{{ post.likes_num }} likes</a>
      <a class="ml-2">{{ post.comments_num }} Comments</a>
    </div>
    <div class="tweetEntry-action-list" style="line-height:24px;color: #b1bbc3;">
      <span v-if="!likeLoading">
        <fa
          icon="thumbs-up"
          v-if="!post.auth_liked"
          fixed-width
          @click="addLike(post, index)"
          style="width: 80px;"
        />
        <fa
          icon="thumbs-up"
          v-else
          fixed-width
          @click="addLike(post, index)"
          style="width: 80px;color: red;"
        />
      </span>
      <span v-else>
        <fa icon="spinner" spin fixed-width style="width: 80px;"/>
      </span>
      <fa
        icon="reply"
        v-if="!commentsLoading"
        @click="getComments(post, index)"
        fixed-width
        style="width: 80px;"
      />
      <fa
        icon="spinner"
        spin
        v-else
        fixed-width
        style="width: 80px;"
      />
    </div>

    <div class="comments mt-3" v-if="comments">
      <div class="tweetEntry-text-container" v-if="comments.data.length == 0">
        <span class="col">be first who comment on this post</span>
      </div>
      <div
        v-for="(comment, comment_index) in comments.data"
        style="min-height: 50px;"
        class="ma-5 col-12 mb-2"
      >
        <div v-if="comment.user_id == user.id" class="dropdown float-right">
          <button
            type="button"
            class="btn dropdown-toggle"
            id="dropdownMenuButton"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          ></button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a
              class="dropdown-item"
              @click="$emit('init-comment',post,index,comment,comment_index)"
            >Edit</a>
            <a
              class="dropdown-item"
              @click="deleteComment(post,index, comment,comment_index)"
            >Delete</a>
          </div>
        </div>

        <router-link
          :to="{ name: 'friend.profile' ,params: { id: comment.user_id } }"
          tag="a"
          class="tweetEntry-account-group"
        >
          <img class="tweetEntry-avatar ml-4 mr-1" :src="comment.user.photo_url">
          
          <strong class="tweetEntry-fullname">{{ comment.user.name }}</strong>
        </router-link>

        <span class="tweetEntry-timestamp">- {{ comment.created_at }}</span>
        
        <span v-if="comment.created_at != comment.updated_at" class="tweetEntry-username">
          <b>Edited</b>
        </span>
        <div class="tweetEntry-text-container">{{ comment.body }}</div>
      </div>
      <button
        class="btn btn-link col align-self-center"
        @click="seeMoreComments(post,index)"
        v-if="comments.next_page_url"
      >
        <div v-if="!moreloading">See more ...</div>
        <fa icon="spinner" spin v-else fixed-width style="width: 80px;"/>
      </button>
      <div class="row col-12 mt-3">
        <textarea class="form-control col-10" rows="1" v-model="newComment[index]"></textarea>
        <button class="btn btn-primary col ml-1" @click="addComment(post, index)">Comment</button>
      </div>
    </div>
  </div>
</template> 


<script>
import axios from "axios";
import Form from "vform";
import { mapGetters } from "vuex";
import swal from "sweetalert2";

export default {
  props: ["post", "index", "updateCommentform"],

  data: () => ({
    comments: false,
    newComment: [],
    likeLoading: false,
    commentsLoading: false,
    moreloading: false
  }),

  computed: mapGetters({
    user: "auth/user"
  }),

  methods: {
    addLike: function(post, index) {
      this.likeLoading = true;
      axios.get("/api/posts/like/" + post.id).then(res => {
        console.log(res);
        if (!post.auth_liked) {
          this.post.likes.unshift(res.data);
          this.post.likes_num++;
          this.likeLoading = false;
        } else {
          let likeIndex = this.post.likes.indexOf(res.data);
          this.post.likes.splice(likeIndex, 1);
          this.post.likes_num--;
          this.likeLoading = false;
        }
        this.post.auth_liked = !this.post.auth_liked;
      });
    },
    addComment: function(post, index) {
      axios
        .post("/api/post/" + post.id + "/comment", {
          body: this.newComment[index]
        })
        .then(res => {
          // this.posts[index].comments.data = res.data.concat(this.posts[index].comments.data)
          // this.posts[index].comments.data.concat(res.data)
          // this.posts[index].comments.data.unshift(res.data);
          // this.posts[index].comments = null;
          // this.getComments(post);
          // this.newComment[index] = ''
          this.comments.data.unshift(res.data);
          this.post.comments_num++;
          // let comment_index = this.comments.indexOf(res.data)
          this.getComments(post, index);
          this.getComments(post, index);
          this.newComment[index] = "";
        });
    },
    getComments: function(post, index) {
      // let index = this.posts.indexOf(post)
      this.commentsLoading = true;
      if (!this.comments) {
        axios.get("/api/post/" + post.id + "/comments").then(res => {
          // this.$set(this.posts, index, Object.assign({}, post, { comments: res.data }));
          this.comments = res.data;
          this.commentsLoading = false;
          console.log(res.data);
        });
      } else {
        this.comments = null;
        this.commentsLoading = false;
      }
      console.log(index);
    },
    seeMoreComments: function(post, index) {
      // let index = this.posts.indexOf(post)
      this.moreloading = true;
      let limit = Math.floor(this.comments.data.length / 2) + 1;
      axios
        .get("/api/post/" + post.id + "/comments", { params: { page: limit } })
        .then(res => {
          this.moreloading = false;
          this.comments.next_page_url = res.data.next_page_url;
          this.comments.data = this.comments.data.concat(res.data.data);
        });
    },
    deleteComment: function(post, index, comment, comment_index) {
      swal({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (result.value) {
          axios
            .delete("/api/post/" + post.id + "/comment/" + comment.id)
            .then(res => {
              this.getComments(post, index);
              this.post.comments_num--;
              if (res) {
                swal("Deleted!", "Your request has been deleted.", "success");
              }
            });
        }
      });
    },
    async updateComment() {
      // Submit the form.
      this.updateCommentform
        .patch(
          "/api/post/" +
            this.updateCommentform.post_id +
            "/comment/" +
            this.updateCommentform.comment_id
        )
        .then(({ data }) => {
          // console.log(data)
          // console.log(this.updateCommentform.post_index)
          // console.log(this.updateform.comment_index)
          // this.$set(this.posts[this.updateCommentform.post_index].comments.data, this.updateCommentform.comment_index, data);
          this.comments.data[this.updateCommentform.comment_index].body =
            data.body;
          this.comments.data[
            this.updateCommentform.comment_index
          ].updated_at = new Date();
          // this.$emit('update-comment');
        });
    }
    // addLike(post, index) {
    //   this.$emit('add-like',[post],[index])
    // }
  }
};
</script>