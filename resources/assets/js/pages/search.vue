<template>
  <div class="col-md-6">
    	<!-- SEARCH FORM -->
    <div class="input-group input-group-md">
       <input class="form-control form-control-navbar" @keyup="searchit" v-model="search" type="search" placeholder="Search" aria-label="Search">
       <div class="input-group-append">
        <button class="btn btn-primary" @click="searchit">
          <i class="fa fa-search"></i>
          <fa icon="search" fixed-width/>
        </button>
      </div>
  	</div>
  	<card :title="$t('friends')">
  	    <friendslist :url="url" :buttons="0"></friendslist>
    </card>
  </div>
</template>

<script>
import friendslist from "~/components/friendslist";

export default {
  middleware: "auth",

  metaInfo() {
    return { title: this.$t("Search") };
  },

  data: () => ({
  	search: '',
    url: '/api/friends/search/'
  }),

  methods: {
    searchit: function(){
      this.url = '/api/friends/search/' + this.search;
    }
  },
  components: {
    friendslist
  },


};
</script>

<style scoped>
.form-control-borderless {
  border: none;
}

.form-control-borderless:hover,
.form-control-borderless:active,
.form-control-borderless:focus {
  border: none;
  outline: none;
  box-shadow: none;
}
</style>
