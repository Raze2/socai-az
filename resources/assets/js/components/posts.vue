<template>
  <div>
    <form
      class="form-control"
      v-if="postForm"
      @submit.prevent="post"
      @keydown="form.onKeydown($event)"
    >
      <div class="form-group">
        <textarea
          class="form-control"
          v-model="form.body"
          :class="{ 'is-invalid': form.errors.has('body') }"
          rows="3"
        ></textarea>
        <has-error :form="form" field="body"/>
      </div>
      <div class="form-row align-items-center">
        <label class="mr-sm-2 col-auto" for="inlineFormCustomSelect">Privacy</label>
        <div class="col-auto my-1">
          <select
            class="custom-select mr-sm-2"
            id="inlineFormCustomSelect"
            v-model="form.privacy"
            name="privacy"
          >
            <option>public</option>
            <option>friends</option>
            <option>private</option>
          </select>
        </div>
        <div class="col-auto">
          <div class="file-upload btn btn-primary">
            <span>BROWSE</span>
            <input type="file" name="FileAttachment" id="FileAttachment" class="upload">
          </div>
          <input type="text" id="fileuploadurl" readonly placeholder="Maximum file size is 1GB">
        </div>
        <div class="col-auto my-1">
          <v-button :loading="form.busy">Post</v-button>
        </div>
      </div>
    </form>

    <div class="tweetEntry-tweetHolder mt-3">
      <post
        v-for="(post,index) in posts"
        :post="post"
        :index="index"
        :updateCommentform="updateCommentform"
        v-bind:key="post.id"
        @delete-post="deletePost(...arguments)"
        @init-post="initUpdatePost(...arguments)"
        @init-comment="initUpdateComment(...arguments)"
      ></post>
      <infinite-loading @infinite="infiniteHandler">
        <span slot="no-more">There's no more posts</span>
      </infinite-loading>

      <!--  <a class="btn btn-primary" @click="seeMore()">See more</a>
      -->
      <!--End of tweetHolder-->
    </div>

    <!-- <b-button @click="showModal">
      Open Modal
    </b-button>-->
    <b-modal ref="myModalRef" hide-footer title="Edit Post">
      <form
        class="form-control"
        @submit.prevent="updatePost"
        @keydown="updateform.onKeydown($event)"
      >
        <div class="form-group">
          <textarea
            class="form-control"
            v-model="updateform.body"
            :class="{ 'is-invalid': updateform.errors.has('body') }"
            rows="3"
          ></textarea>
          <has-error :form="updateform" field="body"/>
        </div>
        <div class="form-row align-items-center">
          <label class="mr-sm-2 col-auto" for="inlineFormCustomSelect">Privacy</label>
          <div class="col-auto my-1">
            <select
              class="custom-select mr-sm-2"
              id="inlineFormCustomSelect"
              v-model="updateform.privacy"
              name="privacy"
            >
              <option>public</option>
              <option>friends</option>
              <option>private</option>
            </select>
          </div>
          <div class="col-auto my-1">
            <v-button :loading="updateform.busy">Edit</v-button>
            <button
              type="button"
              class="btn btn-default"
              data-dismiss="modal"
              block
              @click="$refs.myModalRef.hide()"
            >Close</button>
          </div>
        </div>
      </form>
    </b-modal>

    <b-modal ref="myCommentsModalRef" hide-footer title="Edit Comment">
      <form
        class="form-control"
        @submit.prevent="updateComment"
        @keydown="updateCommentform.onKeydown($event)"
      >
        <div class="form-group">
          <textarea
            class="form-control"
            v-model="updateCommentform.body"
            :class="{ 'is-invalid': updateCommentform.errors.has('body') }"
            rows="1"
          ></textarea>
          <has-error :form="updateCommentform" field="body"/>
        </div>
        <div class="form-row align-items-center">
          <div class="col-auto my-1">
            <v-button :loading="updateCommentform.busy">Edit</v-button>
            <button
              type="button"
              class="btn btn-default"
              data-dismiss="modal"
              block
              @click="$refs.myCommentsModalRef.hide()"
            >Close</button>
          </div>
        </div>
      </form>
    </b-modal>
  </div>
</template> 

<script>
import axios from "axios";
import Form from "vform";
import { mapGetters } from "vuex";
import swal from "sweetalert2";
import post from "./post";

export default {
  props: ["postForm", "url"],

  components: {
    post
  },

  data: () => ({
    form: new Form({
      body: "",
      privacy: "public"
    }),
    updateform: new Form({
      body: "",
      privacy: ""
    }),
    posts: {},
    updateCommentform: new Form({
      body: ""
    }),
    newComment: "",
    limit: 0
  }),

  computed: mapGetters({
    user: "auth/user"
  }),

  methods: {
    getPosts: function() {
      this.limit = 2;
      axios.get(this.url).then(response => {
        this.posts = response.data.data;
      });
    },
    infiniteHandler($state) {
      axios
        .get(this.url, {
          params: {
            page: this.limit
          }
        })
        .then(response => {
          if (response.data.data.length) {
            this.posts = this.posts.concat(response.data.data);
            this.limit++;
            $state.loaded();
            if (this.posts.length === response.data.total) {
              $state.complete();
            }
          } else {
            $state.complete();
          }
        });
    },
    // infiniteHandler: function ($state) {
    //   console.log(this.posts.length)
    //   let limit = (this.posts.length / 5) + 1;
    //   axios.get(this.url, { params: { page: limit } }).then(response => {
    //   this.loadMore($state, response);
    //   // console.log(this.posts.length)
    //   });
    // },
    // loadMore: function ($state, response) {
    //   if ( response.data.data.length ) {
    //     this.posts = this.posts.concat(response.data.data);
    //     setTimeout(() => {
    //       $state.loaded();
    //     }, 3000);
    //     if ( response.data.total == this.posts.length ) {
    //       $state.complete();
    //     }
    //   } else {
    //     $state.complete();
    //   }
    // },
    async post() {
      // Submit the form.
      const { data } = await this.form.post("/api/post");
      console.log(data);
      (this.form.body = ""),
        (this.form.privacy = "public"),
        this.posts.unshift(data),
        this.flash("Post Publiched", "success", {
          timeout: 3000
        });
    },
    initUpdatePost(post, index) {
      this.$refs.myModalRef.show();
      this.updateform.body = post.body;
      this.updateform.privacy = post.privacy;
      this.updateform.id = post.id;
      this.updateform.index = index;
    },
    async updatePost() {
      // Submit the form.
      this.updateform
        .patch("/api/post/" + this.updateform.id)
        .then(({ data }) => {
          this.$set(this.posts, this.updateform.index, data);
          this.$refs.myModalRef.hide();
          this.flash("Post Edited", "success", {
            timeout: 3000
          });
        });
    },
    deletePost: function(post, index) {
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
          axios.delete("/api/post/" + post.id).then(res => {
            // let index = this.posts.indexOf(post)
            this.posts.splice(index, 1);
            if (res) {
              swal("Deleted!", "Your request has been deleted.", "success");
            }
          });
        }
      });
    },
    initUpdateComment(post, index, comment, comment_index) {
      // let index = this.posts.indexOf(post);
      // let comment_index = this.posts[index].comments.data.indexOf(comment)
      this.$refs.myCommentsModalRef.show();
      this.updateCommentform.body = comment.body;
      this.updateCommentform.post_id = post.id;
      this.updateCommentform.comment_id = comment.id;
      this.updateCommentform.post_index = index;
      this.updateCommentform.comment_index = comment_index;
    },
    async updateComment() {
      console.log(this)
      this.$children[this.updateCommentform.post_index + 5].updateComment();
      this.$refs.myCommentsModalRef.hide();
      this.flash("Comment Edited", "success", {
        timeout: 3000
      });
      // this.flash('Comment Edited', 'success',{
      //   timeout: 3000,
      // });
    }
  },
  created() {
    this.getPosts();
  }
};
</script>