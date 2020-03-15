import Vue from 'vue';
import App from './App.vue';
import store from "./store/index";
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.prototype.$message = ElementUI.message;


import router from './router/index'
// 导入全局样式表
import './assets/css/global.css';

// 导入treetable
import TreeTable from "vue-table-with-tree-grid";
Vue.component("tree-table",TreeTable);

import Breadcrumb from "./components/Common/Breadcrumb.vue"
Vue.component("Breadcrumb",Breadcrumb);

Vue.config.productionTip = false
Vue.use(ElementUI);

const vue = new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')

export default vue;