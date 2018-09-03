<template>
<!-- <ais-index app-id="5YGLD642I0"
           api-key="930bbf37fbc553c464fd424260d366e8"
           index-name="users">

 <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
        <form class="card card-sm">
            <div class="card-body row no-gutters align-items-center">
                <div class="col-auto">
                    <i class="fas fa-search h4 text-body"></i>
                </div>
                <!--end of col-->
                <div class="col">
   <ais-input class="form-control form-control-lg form-control-borderless" placeholder="Search users..."></ais-input>
                </div>
                <!--end of col-->
                <div class="col-auto">
                  <fa icon="search" fixed-width/>
                </div>
                <!--end of col-->
            </div>
        </form>
        <ais-results inline-template>
        <ul class="list-group mt-1">  
            <router-link :key="result.objectID" :to="{ name: 'friend.profile' ,params: { id: result.id } }" tag="li" class="list-group-item btn btn-light" v-for="result in results">
              <div class="float-left">{{ result.name }}</div>
            </router-link>
        </ul>
        </ais-results>
    </div>
    <!--end of col-->
  </div>


</ais-index> -->



</template>

<script>

export default {
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('Search') }
  },


}
</script>

<style scoped>
.form-control-borderless {
    border: none;
}

.form-control-borderless:hover, .form-control-borderless:active, .form-control-borderless:focus {
    border: none;
    outline: none;
    box-shadow: none;
}
</style>
