<template>
  <div>
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on, attrs }">
        <v-icon v-bind="attrs" v-on="on">mdi-cog</v-icon>
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">
            マイプロフィール
          </span>
        </v-card-title>
        <form @submit.prevent>
        <!-- Error -->
          <div v-if="errors.length != 0">
            <div v-if="errors.content">
              <div v-for="e in errors.content" :key="e">{{ e }}</div>
            </div>
          </div>
          <!-- profileForm.content -->
          <v-textarea
            class="mt-1 ml-10 mr-10"
            counter
            label="自己紹介"
            v-model="profileForm.content"
          ></v-textarea>
          <div class="mt-5 mb-5 d-flex justify-center">
            <v-btn
              v-if="!sending"
              @click="submit"
              width="255px" class="mb-5" color="primary" dark
            >
              完了
            </v-btn>
            <v-btn
              v-else
              class="mb-5" width="255px" disabled
            >
              完了
            </v-btn>
          </div>
        </form>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="blue darken-1"
            text
            @click="dialog = false;"
          >
            閉じる
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  props: {
    user: {
      type: Object,
      required: true,
    }
  },
  data () {
    return {
      dialog:   false,
      sending:  false,
      errors: '',
      profileForm: {
        content: this.isProfile(),
      }
    }
  },
  methods: {
    async submit () {
      this.sending = true

      const response = await axios.post('/api/profiles', this.profileForm)

      if (response.status === 422) {
        this.errors = response.data.errors;
        this.sending = false
        return false
      }

      this.sending = false
      this.dialog  = false

      if (response.status !== 201) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.$emit('reloadUser')
    },
    isProfile () {
      if (this.user.profile) {
        return this.user.profile.content
      } else {
        return ''
      }
    },
  }
}
</script>