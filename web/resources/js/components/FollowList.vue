<template>
  <div>
    <v-virtual-scroll
      :items="list"
      :item-height="65"
      height="270"
    >
      <template v-slot="{ item }">
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title>
              <router-link
                :to="{ name: 'userDetail', params: { id: item.id.toString() }}" 
                class="mypage--link"
              >
                {{ item.name }}
              </router-link>
            </v-list-item-title>
            <div v-if="item.profile">
              <v-list-item-subtitle
                v-show="item.profile.content"
              >
                {{ item.profile.content | truncate }}
              </v-list-item-subtitle>
            </div>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
      </template>
    </v-virtual-scroll>
  </div>
</template>

<script>
export default {
  props: {
    list: {
      type: Array,
      required: true
    }
  },
  filters: {
    truncate: function (val) {
      let length = 40
      let ommision = "..."
      if (val.length <= length) {
        return val
      }
      return val.substring(0, length) + ommision
    }
  },
}
</script>

<style scoped>
.mypage--link {
  color: black;
  text-decoration: none;
}
</style>