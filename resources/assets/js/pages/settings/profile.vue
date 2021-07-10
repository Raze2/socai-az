<template>
  <card :title="$t('your_info')">
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
      <alert-success :form="form" :message="$t('info_updated')"/>

      <!-- Name -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('name') }}</label>
        <div class="col-md-7">
          <input
            v-model="form.name"
            :class="{ 'is-invalid': form.errors.has('name') }"
            class="form-control"
            type="text"
            name="name"
          >
          <has-error :form="form" field="name"/>
        </div>
      </div>

      <!-- Email -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
        <div class="col-md-7">
          <input
            v-model="form.email"
            :class="{ 'is-invalid': form.errors.has('email') }"
            class="form-control"
            type="email"
            name="email"
          >
          <has-error :form="form" field="email"/>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('profile-picture') }}</label>
        <div class="col-md-9 ml-md-auto mt-2">
          <div class="file-upload btn  btn-primary">
            <span>BROWSE</span>
            <input type="file" name="FileAttachment" id="FileAttachment" @change="updateProfile" class="upload">
          </div>
          <input type="text" id="fileuploadurl" readonly :placeholder="(user.avatar != form.photo) ? 'Uploaded' : 'Maximum file size is 2MB'">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('cover') }}</label>
        <div class="col-md-9 ml-md-auto mt-2">
          <div class="file-upload btn  btn-primary">
            <span>BROWSE</span>
            <input type="file" name="FileAttachment" @change="updateCover" id="FileAttachment" class="upload">
          </div>
          <input type="text" id="fileuploadurl" readonly :placeholder="(user.cover != form.cover) ? 'Uploaded' : 'Maximum file size is 2MB'">
        </div>
      </div>

      <!-- Submit Button -->
      <div class="form-group row">
        <div class="col-md-9 ml-md-auto">
          <v-button :loading="form.busy" type="success">{{ $t('update') }}</v-button>
        </div>
      </div>
    </form>
  </card>
</template>

<script>
import Form from "vform";
import { mapGetters } from "vuex";
import swal from "sweetalert2";

export default {
  scrollToTop: false,

  metaInfo() {
    return { title: this.$t("settings") };
  },

  data: () => ({
    form: new Form({
      name: "",
      email: "",
      photo: "",
      cover:""
    })
  }),

  computed: mapGetters({
    user: "auth/user"
  }),

  created() {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key];
    });
    this.form.photo = this.user.avatar;
  },

  methods: {
    async update() {
      const { data } = await this.form.patch("/api/settings/profile");

      this.$store.dispatch("auth/updateUser", { user: data });
    },
    updateProfile(e){
        let file = e.target.files[0];
        let reader = new FileReader();
        let limit = 1024 * 1024 * 2;
        if(file['size'] > limit){
            swal({
                type: 'error',
                title: 'Oops...',
                text: 'You are uploading a large file',
            })
            return false;
        }
        reader.onloadend = (file) => {
            this.form.photo = reader.result;
        }
        reader.readAsDataURL(file);
    },
    updateCover(e){
        let file = e.target.files[0];
        let reader = new FileReader();
        let limit = 1024 * 1024 * 2;
        if(file['size'] > limit){
            swal({
                type: 'error',
                title: 'Oops...',
                text: 'You are uploading a large file',
            })
            return false;
        }
        reader.onloadend = (file) => {
            this.form.cover = reader.result;
        }
        reader.readAsDataURL(file);
    }
  }
};
</script>
