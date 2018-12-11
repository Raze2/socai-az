<template>
  <card :title="$t('blockedusers')">
    <friendslist :url="`/api/friends/blockedusers`" :buttons="3"></friendslist>
  </card>
</template>

<script>
import friendslist from "~/components/friendslist";

export default {
  middleware: "auth",
  scrollToTop: false,

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

