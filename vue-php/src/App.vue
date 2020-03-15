<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script>
import {setRoleID} from "./store/mutations-type"
export default {
  name: 'App',
  components: {
  },
  created () {
    let role_id = sessionStorage.getItem("role_id");
    if(role_id){
      this.$store.commit(setRoleID,role_id);
    }
  }
}
</script>

<style>
#app {
  
}
</style>
