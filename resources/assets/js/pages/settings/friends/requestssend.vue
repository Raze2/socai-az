<template>
 <card :title="$t('requestssend')">
  <friendslist :url="`/api/friends/friendrequests`" :buttons="3"></friendslist>
 </card>
 

</template>

<script>
import friendslist from '~/components/friendslist'


export default {
  middleware: 'auth',
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data() {
    return {
      
    } 
  },

  components: {
    friendslist
  },


  methods: {
    
  },

  created() {
    
  }
}
</script>

