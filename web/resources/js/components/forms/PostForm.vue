<template>
  <div>
    <v-dialog v-model="dialog" max-width="600px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-bind="attrs" v-on="on" class="mx-2" fab dark large color="cyan">
          <v-icon dark>mdi-plus</v-icon>
        </v-btn>
      </template>
      <v-card>
        <form @submit.prevent>
          <v-card-title>
            <span class="headline">
              投稿
            </span>
          </v-card-title>
        <!-- Error -->
          <div v-if="errors.length != 0" class="error--message">
            <div v-if="errors.content">
              <div v-for="e in errors.content" :key="e">{{ e }}</div>
            </div>
            <div v-if="errors.platform">
              <div v-for="e in errors.platform" :key="e">{{ e }}</div>
            </div>
          </div>
          <!-- postForm.content -->
          <v-textarea
            class="mt-2 ml-10 mr-10"
            counter
            label="本文"
            v-model="postForm.content"
          ></v-textarea>
          <!-- postForm.platform -->
          <v-select
            @change="changePlatForm"
            class="mt-4 ml-10 mr-10"
            :items="['PS4', 'PC']"
            v-model="postForm.platform"
            label="プラットフォーム"
          ></v-select>
          <!-- postForm.myid -->
          <v-text-field
            v-show="ps4Flag"
            class="mt-1 ml-10 mr-10"
            v-model="postForm.myid"
            label="PSID"
            hide-details="auto"
          ></v-text-field>
          <!-- postForm.myid -->
          <v-text-field
            v-show="pcFlag"
            class="mt-1 ml-10 mr-10"
            v-model="postForm.myid"
            label="UID"
            hide-details="auto"
          ></v-text-field>
          <!-- postForm.legend -->
          <v-select
            class="mt-4 ml-10 mr-10"
            :items="legends"
            v-model="postForm.legend"
            label="使用するレジェンド"
          ></v-select>
          <!-- postForm.private -->
          <v-switch
            v-model="postForm.private"
            class="mt-1 ml-10 mr-10"
            label="ログインユーザーにのみID表示"
          ></v-switch>

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
            @click="dialog = false;
            resetValue()"
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
    legends: {
      type: Array,
    }
  },
  data () {
    return {
      dialog: false,
      sending:  false,
      ps4Flag:  false,
      pcFlag:   false,
      errors: {},
      postForm: {
        content: '',
        myid: '',
        platform: '',
        legend: '',
        private: false,
      }
    }
  },
  methods: {
    async submit () {
      this.sending = true

      const response = await axios.post('/api/posts', this.postForm)

      if (response.status === 422) {
        this.errors = response.data.errors;
        this.sending = false
        return false
      }

      this.resetValue()
      this.sending = false
      this.dialog  = false

      if (response.status !== 201) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.$router.push(`/posts/${response.data.id}`)
    },
    resetValue () {
      this.postForm.content = ''
      this.postForm.myid = ''
      this.postForm.platform = ''
      this.postForm.legend = ''
      this.ps4Flag = false
      this.pcFlag = false
      this.errors = {}
    },
    changePlatForm () {
      if (this.postForm.platform === 'PS4') {
        this.ps4Flag  = true
        this.pcFlag   = false
      } else if (this.postForm.platform === 'PC') {
        this.pcFlag   = true
        this.ps4Flag  = false
      }
    }
  }
}
</script>

<style scoped>
.error--message {
  color: red;
  text-align: center;
}
</style>