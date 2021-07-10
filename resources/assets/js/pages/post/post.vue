<template>
  <div>
    <posts :postForm="false" :postView=true :url="'/api/post/' + this.$route.params.id"></posts>
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
    
  }),

  methods: {
  },

  computed: mapGetters({
    user: "auth/user"
  }),

  created() {
    
  }
};
</script>



