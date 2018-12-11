<template>
  <card :title="$t('friends')">
    <friendslist :url="`/api/friends`" :buttons="1"></friendslist>
  </card>
</template>

<script>
import friendslist from "~/components/friendslist";

export default {
  middleware: "auth",

  metaInfo() {
    return { title: this.$t("friends") };
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

