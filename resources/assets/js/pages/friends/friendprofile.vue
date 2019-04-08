<template>
  <div>
    <div class="card card-profile text-center">
      <img class="card-img-top" :src="'../img/cover/'+friend.cover">
      <div class="card-block">
        <img class="card-img-profile" :src="friend.photo_url">
        <div
          class="dropdown float-right"
          v-if="show"
          style="position: absolute;top: 180px;right: 10px;"
        >
          <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"></button>
          <div class="dropdown-menu">
            <a class="dropdown-item" @click="blockUser()">Block</a>
          </div>
        </div>
        <h4 class="card-title">
          {{ friend.name }}
          <!-- <small>Front-end designer</small> -->
        </h4>
        <div class="card-links" v-if="show">
          <button
            class="btn btn-sm btn-primary mr-1"
            v-if="friend.friendship.status == 'pending' && friend.friendship.first_user == this.$route.params.id"
            @click="acceptRequest()"
          >
            <fa icon="check" fixed-width/>Accept
          </button>
          <button
            class="btn btn-sm btn-primary mr-1"
            v-if="!friend.friendship"
            @click="sendRequest()"
          >
            <fa icon="user-plus" fixed-width/>Add
          </button>
          <button
            class="btn btn-sm btn-primary mr-1"
            disabled
            v-if="friend.friendship.status == 'pending' && friend.friendship.first_user == user.id"
          >
            <fa icon="user-plus" fixed-width/>Request sended
          </button>
          <button
            class="btn btn-sm btn-danger"
            v-if="friend.friendship.status"
            @click="deleteRequest()"
          >
            <fa icon="times" fixed-width/>Delete
          </button>
        </div>
      </div>
    </div>

    <posts :postForm="false" :url="'/api/posts/' + this.$route.params.id"></posts>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Form from "vform";
import axios from "axios";
import swal from "sweetalert2";
import posts from "~/components/posts";

export default {
  middleware: "auth",

  components: {
    posts
  },

  metaInfo() {
    return { title: this.$t("settings") };
  },

  data: () => ({
    friend: {
      friendship: []
    },
    show: true
  }),

  methods: {
    async getData() {
      try {
        await axios.get("/api/friends/" + this.$route.params.id).then(res => {
          this.friend = res.data;
        });
      } catch (error) {
        this.$router.push({ name: "notfound" });
      }
    },
    sendRequest: function(e) {
      axios.post("/api/friends/" + this.$route.params.id, []).then(res => {
        console.log(res);
        this.friend.friendship = {
          status: "pending",
          first_user: this.user.id,
          id: res.data.id
        };
      });
    },
    deleteRequest: function() {
      swal({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
                      console.log(this.friend.friendship.id)

        if (result.value) {
          axios
            .delete("/api/friends/" + this.friend.friendship.id)
            .then(res => {
              console.log(res)
              this.friend.friendship = false;
              if (res) {
                swal("Deleted!", "Your request has been deleted.", "success");
              }
            });
        }
      });
    },
    acceptRequest: function() {
      axios.put("/api/friends/" + this.$route.params.id, []).then(res => {
        (this.friend.friendship.status = "confirmed"), console.log(res);
        swal("Accepted!", "Your friend has been added.", "success");
      });
    },
    checkUser: function(e) {
      if (this.user.id == this.$route.params.id) {
        this.show = false;
      }
    },
    blockUser: function() {
      swal({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes!"
      }).then(result => {
        if (result.value) {
          axios.patch("/api/friends/" + this.$route.params.id).then(res => {
            this.friend.friendship = false;
            this.$router.push({ name: "home" });
            if (res) {
              this.flash("User has been blocked", "success", {
                timeout: 3000
              });
            }
          });
        }
      });
    }
  },

  computed: mapGetters({
    user: "auth/user"
  }),

  created() {
    this.getData();
    this.checkUser();
  }
};
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
  box-shadow: 1em 1em 2em rgba(0, 0, 0, 0.2);
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
.card-profile .card-links .fa:focus,
.card-profile .card-links .fa:hover {
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
.card-profile .dropdown-menu {
  left: -60px !important;
  min-width: 1rem;
}

.card-profile .dropdown-menu .dropdown-item {
  cursor: pointer;
}
</style>

