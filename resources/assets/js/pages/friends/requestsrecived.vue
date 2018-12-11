<template>
  <card :title="$t('requestsrecived')">
    <friendslist :url="`/api/friends/requestsrecived`" :buttons="2"></friendslist>
  </card>
</template>

<script>
import friendslist from "~/components/friendslist";

export default {
  middleware: "auth",

  metaInfo() {
    return { title: this.$t("settings") };
  },

  components: {
    friendslist
  },

  data() {
    return {};
  },

  methods: {},

  created() {}
};
</script>

