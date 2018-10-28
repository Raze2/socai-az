<template>
  <div>



 <posts :postForm="true" :url="`/api/posts`"></posts>





  </div> 



</template>

<script>
import axios from 'axios'
import Form from 'vform'
import { mapGetters } from 'vuex'
import swal from 'sweetalert2'
import posts from '~/components/posts'

export default {
  middleware: 'auth',

  components: {
    posts
  },

  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({

  }),

  methods: {
    
  },

  computed: mapGetters({
    user: 'auth/user'
  }),


}
</script>

